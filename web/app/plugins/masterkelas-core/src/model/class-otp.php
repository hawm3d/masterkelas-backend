<?php

namespace MasterKelas\Model;

use Carbon\Carbon;
use MasterKelas\Database\OTP_Query;
use MasterKelas\Admin;
use MasterKelas\MasterException;
use MasterKelas\Validator;
use MasterKelas\Queue;
use PasswordHash;

/**
 * OTP Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP {
  public static $types = ["mobile", "email"];

  public static function get_retry_seconds($type) {
    return intval(Admin::get_option("auth-{$type}-code-retry")) ?: 120;
  }

  public static function get_code_expire($type) {
    return intval(Admin::get_option("auth-{$type}-code-expire")) ?: HOUR_IN_SECONDS;
  }

  public static function get_code_length($type) {
    return intval(Admin::get_option("auth-{$type}-code-length")) ?: 5;
  }

  public function __construct($data = []) {
    if (empty($data) || !isset($data['type'], $data['recipient']))
      throw new MasterException("otp.invalid.data");

    if (isset($data['id']) && (!is_int($data['id']) || $data['id'] <= 0))
      throw new MasterException("otp.invalid.id");

    $this->id = isset($data['id']) ? $data['id'] : null;

    if (!in_array($data['type'], self::$types) || !Auth::is_active($data['type']))
      throw new MasterException("otp.invalid.type");

    $this->type = $data['type'];
    $data['recipient'] = $this->sanitize_recipient($data['recipient']);

    if (
      empty($data['recipient']) ||
      ($data['type'] === 'email' && !Validator::email($data['recipient'])) ||
      ($data['type'] === 'mobile' && !Validator::mobile($data['recipient']))
    ) throw new MasterException("otp.invalid.recipient");

    $this->recipient = $data['recipient'];
  }

  public function send() {
    $last_otp = $this->last_otp($this->recipient);
    if ($last_otp)
      return $last_otp;

    $this->id = $this->create();

    if (empty($this->id))
      throw new MasterException("otp.invalid.id");

    $this->dispatch(true, false);
    return $this->id;
  }

  public function verify($code) {
    $validated_otp = false;
    $code = sanitize_text_field($code);
    $length = self::get_code_length($this->type);
    $wp_hasher = new PasswordHash(8, TRUE);

    if (strlen($code) !== $length)
      throw new MasterException("otp.invalid.code");

    $query = new OTP_Query([
      "type" => $this->type,
      "recipient" => $this->recipient,
      "orderby" => "created_at",
    ]);

    if (empty($query->items))
      throw new MasterException("otp.code.notfound");

    foreach ($query->items as $otp) {
      // if (!$wp_hasher->CheckPassword($code, $otp->token))
      if ((string) $code !== (string) $otp->token)
        continue;

      if ($otp->is_expired)
        throw new MasterException("otp.expired.code");

      $validated_otp = $otp;
      break;
    }

    if (!$validated_otp)
      throw new MasterException("otp.invalid.code");

    // Remove old OTPS
    foreach ($query->items as $item)
      $query->delete_item($item->id);
  }

  public function last_otp() {
    $retry_seconds = self::get_retry_seconds($this->type);

    $query = new OTP_Query([
      "type" => $this->type,
      "recipient" => $this->recipient,
      "created_at_query" => [
        "after" => Carbon::now()->subSeconds($retry_seconds)
      ],
      "expire_at_query" => [
        "after" => 'now'
      ],
      "orderby" => "created_at",
      "fields" => "ids",
      "number" => 1
    ]);

    if (empty($query->items))
      return null;

    return $query->items[0];
  }

  public function create($data = []) {
    $defaults = [
      'token' => $this->create_token(),
      'status' => 0,
      'created_at' => Carbon::now()->toDateTimeString(),
      'expire_at' => $this->create_expire_date(),
      'data' => [
        "provider" => null,
        "provider-attempts" => 0,
        "provider-status" => 0,
        "provider-response" => null,
      ]
    ];

    $args = wp_parse_args($data, $defaults);
    $args['type'] = $this->type;
    $args['recipient'] = $this->recipient;

    if (isset($args['data']))
      $args['data'] = $this->encode_data($args['data']);

    return (new OTP_Query())->add_item($args);
  }

  public function update($data = []) {
    if (!$this->id || empty($data))
      throw new MasterException("otp.invalid.id");

    $data['type'] = $this->type;
    $data['recipient'] = $this->recipient;

    if (isset($data['id']))
      unset($data['id']);

    if (isset($data['data']))
      $data['data'] = $this->encode_data($data['data']);

    return (new OTP_Query())->update_item($this->id, $data);
  }

  public function dispatch($async = true, $hook = true, $timestamp = null) {
    $data = ["id" => $this->id];
    $job_name = "send_otp_to_{$this->type}";
    $group = "otp";

    if ($async)
      return Queue::async($job_name, $data, $group, $hook);

    return Queue::single($timestamp, $job_name, $data, $group, $hook);
  }

  public function create_token() {
    $token_length = self::get_code_length($this->type);
    $token = substr('123456789', 0, $token_length);
    // $token = substr(number_format(time() * rand(), 0, '', ''), 0, $token_length);

    // return wp_hash_password($token);
    return $token;
  }

  public function create_expire_date() {
    $seconds = self::get_code_expire($this->type);
    return Carbon::now()->addSeconds($seconds)->toDateTimeString();
  }

  public function encode_data($data) {
    if (empty($data))
      return null;

    return !is_string($data) ? json_encode($data) : $data;
  }

  public function sanitize_recipient($recipient = '') {
    return $this->type === 'email' ? sanitize_email($recipient) : sanitize_text_field($recipient);
  }
}
