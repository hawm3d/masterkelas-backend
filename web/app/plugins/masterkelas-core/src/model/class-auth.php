<?php

namespace MasterKelas\Model;

use MasterKelas\Admin;
use MasterKelas\Database\Session_Row;
use MasterKelas\JWT;
use MasterKelas\MasterException;
use MasterKelas\Model\WebApp;
use MasterKelas\Schema;

/**
 * Auth Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Auth {

  public static JWT $jwt;
  public static $access_header = 'HTTP_AUTHORIZATION';
  public static $refresh_header = 'HTTP_X_REFRESH_TOKEN';

  public User $user;
  public Session_Row $session;

  public static function get_nationality_questions() {
    return [
      [
        'uid' => "3xVobcQ9IIQTcmWeUDJHs",
        'question' => 'عید نوروز مصادف با آمدن کدام فصلی از سال است؟',
        'answer' => ['بهار', 'فصل بهار']
      ],
      [
        'uid' => "IbaTCgteP3SWqf5TzAY7Z",
        'question' => 'طولانی ترین شب سال چه نام دارد؟',
        'answer' => ['یلدا', 'شب یلدا']
      ],
      [
        'uid' => "i_Mk734mWloEVpAlxV1a7",
        'question' => 'سرآینده شاهنامه ی فارسی کیست؟',
        'answer' => ['حکیم فردوسی', 'فردوسی', 'ابوقاسم فردوسی', 'حکیم ابوقاسم فردوسی']
      ],
    ];
  }

  public static function get_random_nationality_question() {
    $questions = self::get_nationality_questions();
    $index = wp_rand(1, count($questions));
    return $questions[$index - 1];
  }

  public static function get_nationality_question_by_uid($uid) {
    $questions = self::get_nationality_questions();
    foreach ($questions as $question)
      if ($question['uid'] === $uid)
        return $question;

    return null;
  }

  public static function validate_nationality_question($uid, $answer) {
    $question = self::get_nationality_question_by_uid($uid);

    if ($question)
      foreach ($question['answer'] as $correct_answer)
        if (str_replace(' ', '', $correct_answer) === str_replace(' ', '', $answer))
          return true;

    return false;
  }

  public static function is_active($type) {
    return (bool) Admin::get_option("auth-{$type}");
  }

  public static function get_access_token_expire() {
    return 30000;
    // return (int) (Admin::get_option("auth-refresh-token-expire") ?? MINUTE_IN_SECONDS * 2);
    return (int) (Admin::get_option("auth-refresh-token-expire") ?? MINUTE_IN_SECONDS * 2);
  }

  public static function get_refresh_token_expire() {
    return (int) (Admin::get_option("auth-refresh-token-expire") ?? WEEK_IN_SECONDS);
  }

  private static function get_access_token_key() {
    return wp_salt('auth');
  }

  private static function get_refresh_token_key() {
    return wp_salt('auth-secure');
  }

  public static function get_header($type) {
    $header = $_SERVER[$type === 'refresh' ? static::$refresh_header : static::$access_header] ?? null;
    if (!isset($header) || empty($header))
      throw new MasterException("auth.header.missing");

    return $header;
  }

  public static function fromSession($session) {
    return new self($session);
  }

  public static function fromHeader($type, $ua = null) {
    $header = static::get_header($type);
    $session = static::verify($header, $type);

    if ($ua)
      $session->validate_ua($ua);

    return new self($session);
  }

  public function __construct(Session_Row $session) {
    if (!$session || !isset($session->fingerprint, $session->user_id))
      throw new MasterException("auth.invalid.session");

    $this->user = $session->user();
    $this->session = $session;
    static::$jwt = WebApp::JWT()
      ->set_subject(
        Schema::toGlobalId("user", $session->user->id)
      )
      ->set_claim('fingerprint', $this->session->fingerprint)
      ->add_validator(function ($decoded, $claims) {
        if ($decoded->fingerprint !== $claims['fingerprint'])
          throw new MasterException("invalid.fingerprint");
      });
  }

  public function access_token() {
    return static::$jwt
      ->set_key($this->get_access_token_key())
      ->set_expiration_seconds(self::get_access_token_expire());
  }

  public function refresh_token() {
    return static::$jwt
      ->set_key($this->get_refresh_token_key())
      ->set_expiration_seconds(self::get_refresh_token_expire());
  }

  public function issue() {
    return [
      "accessToken" => $this->access_token()->encode(),
      "refreshToken" => $this->refresh_token()->encode()
    ];
  }

  public static function verify($token, $type = 'access') {
    static::$jwt = WebApp::JWT()->add_validator(function ($decoded) {
      if (
        !$decoded->sub
        || !($sub = Schema::fromGlobalId($decoded->sub))
        || !isset($sub['type'], $sub['id'])
        || $sub['type'] !== 'user'
        || $sub['id'] <= 0
      )
        throw new MasterException("invalid.sub");

      if (!$decoded->fingerprint || empty($decoded->fingerprint))
        throw new MasterException("invalid.fingerprint");
    });

    if ($type === 'refresh')
      static::$jwt->set_key(static::get_refresh_token_key());
    else
      static::$jwt->set_key(static::get_access_token_key());

    $token = static::$jwt->decode_bearer($token);
    if (
      !$token
      || !isset($token->fingerprint, $token->sub)
      || empty($token->fingerprint)
      || empty($token->sub)
    ) throw new MasterException("auth.invalid.token");

    $token_user_id = (int) Schema::fromGlobalId($token->sub)['id'];
    $session = Session::find([
      "fingerprint" => $token->fingerprint
    ]);

    if (
      !$session
      || $session->user_id !== $token_user_id
      || $session->fingerprint !== $token->fingerprint
      || $session->is_terminated
    ) throw new MasterException("auth.invalid.session");

    $session->user();
    if (!$session->user || is_wp_error($session->user))
      throw new MasterException("auth.invalid.user");

    return $session;
  }

  public static function validate_method($method) {
    if (!in_array($method, ["mobile", "email", "google"]))
      throw new MasterException("invalid.method");

    return $method;
  }

  public static function validate_context($context) {
    if (!in_array($context, ["login", "register", "complete-registration"]))
      throw new MasterException("invalid.context");

    return $context;
  }
}
