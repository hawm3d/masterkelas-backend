<?php

namespace MasterKelas\Model;

use Carbon\Carbon;
use MasterKelas\Admin;
use MasterKelas\Database\Session_Query;
use MasterKelas\MasterException;
use MasterKelas\RemoteAddress;
use MasterKelas\UserAgent;

/**
 * Session Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Session {
  public static function get_max_active_sessions() {
    return (int) (Admin::get_option("sessions-max-active") ?? 5);
  }

  public static function get_sessions_lifetime() {
    return (int) (Admin::get_option("sessions-lifetime") ?? 15);
  }

  public static function get_locked_sessions_lifetime() {
    return (int) (Admin::get_option("locked-sessions-lifetime") ?? 60);
  }

  public static function get_junk_sessions_lifetime() {
    return (int) (Admin::get_option("junk-sessions-lifetime") ?? 30);
  }

  public static function get_max_inactivity_time() {
    return (int) (Auth::get_access_token_expire() ?? MINUTE_IN_SECONDS) + MINUTE_IN_SECONDS;
  }

  public static function start($user_id, $creation_method = null) {
    $data = [
      "user_id" => $user_id,
      "data" => [
        "creation_method" => $creation_method,
        "creation_ip" => RemoteAddress::Ip(),
      ],
      "status" => 1
    ];

    // Get all active sessions
    $sessions = self::active([
      "user_id" => $user_id
    ]);

    // Check if user can start new session
    if (!self::can_start($sessions))
      $data['status'] = 2;

    // Create new session
    $session_id = self::create($data, false);
    if (!$session_id)
      throw new MasterException("session.notfound");

    return self::find(["id" => $session_id]);
  }

  public static function end($args) {
    if (is_string($args))
      $args = ["fingerprint" => $args];
    if (is_int($args))
      $args = ["id" => $args];

    $session = $args instanceof Session ? $args : self::find($args);

    if (!$session || !$session->id)
      throw new MasterException("session.notfound");

    return self::update($session->id, [
      "terminated_at" => Carbon::now(),
      "status" => 3
    ]);
  }

  public static function can_start($active_sessions) {
    if (empty($active_sessions) || !is_array($active_sessions))
      return true;

    return count($active_sessions) < self::get_max_active_sessions();
  }

  public static function beat($session_id) {
    return self::update($session_id, [
      'last_activity' => Carbon::now(),
    ]);
  }

  public static function activate($session_id) {
    return self::update($session_id, [
      'terminator_id' => null,
      'terminated_at' => null,
      'status' => 1
    ]);
  }

  public static function terminate($terminator_id, $session_id) {
    return self::update($session_id, [
      'terminator_id' => $terminator_id,
      'terminated_at' => Carbon::now(),
      'status' => 3
    ]);
  }

  public static function active($args) {
    $defaults = [
      "status" => 1,
    ];

    $args = wp_parse_args($args, $defaults);
    return self::all($args);
  }

  public static function online($args) {
    $defaults = [
      "status" => 1,
      "last_activity" => [
        "after" => Carbon::now()->subSeconds(
          self::get_max_inactivity_time()
        )
      ],
    ];

    $args = wp_parse_args($args, $defaults);
    return self::all($args);
  }

  public static function all($args = []) {
    $defaults = [
      "number" => -1,
    ];

    $args = wp_parse_args($args, $defaults);
    return self::find($args);
  }

  public static function find($args = []) {
    $defaults = [
      "number" => 1,
      "orderby" => "created_at",
    ];

    $args = wp_parse_args($args, $defaults);
    if (isset($args['fingerprint']))
      $args['fingerprint'] = strtoupper($args['fingerprint']);

    $number = $args['number'] ?? null;
    if ($args['number'] === -1)
      unset($args['number']);

    $query = new Session_Query($args);

    if (empty($query->items))
      return $number === 1 ? null : [];

    return $number === 1 ? $query->items[0] : $query->items;
  }

  public static function create($args = [], $cleanup = true) {
    $defaults = [
      "fingerprint" => self::generate_fingerprint(),
      "ua" => self::get_user_agent(),
      "status" => 0,
      "created_at" => Carbon::now()->toDateTimeString(),
    ];

    $args = wp_parse_args($args, $defaults);

    if (!isset($args['user_id'], $args['fingerprint'], $args['status']))
      throw new MasterException("session.bad.input");

    if (isset($args['ua']))
      $args['ua'] = self::to_string($args['ua']);

    if (isset($args['data']))
      $args['data'] = self::to_string($args['data']);

    $created = (new Session_Query())->add_item($args);

    return $created;
  }

  public static function update($id, $session, $cleanup = true) {
    if (isset($session['ua']))
      $session['ua'] = self::to_string($session['ua']);

    if (isset($session['data']))
      $session['data'] = self::to_string($session['data']);

    $updated = (new Session_Query())->update_item($id, $session);
    if ($updated && $cleanup && isset($session['user_id']))
      self::cleanup($session['user_id']);

    return $updated;
  }

  public static function delete($id) {
    $deleted =  (new Session_Query())->delete_item($id);
    return $deleted;
  }

  public static function cleanup($user_id) {
    $trash = [];
    $sessions = self::all([
      "user_id" => $user_id,
    ]);

    if (empty($sessions))
      return;

    $sessions_lifetime = self::get_sessions_lifetime();
    $locked_lifetime = self::get_locked_sessions_lifetime();
    $junk_lifetime = self::get_junk_sessions_lifetime();

    foreach ($sessions as $session) {
      switch (true) {
        case $session->last_activity && $session->last_activity->lte(Carbon::now()->subDays($sessions_lifetime)):
        case $session->is_locked && $session->created_at->lte(Carbon::now()->subMinutes($locked_lifetime)):
        case $session->is_junk && $session->created_at->lte(Carbon::now()->subMinutes($junk_lifetime)):
          array_push($trash, $session->id);
          break;
      }
    }

    foreach ($trash as $id) {
      self::delete($id, false);
    }
  }

  public static function generate_fingerprint() {
    return strtoupper(wp_generate_uuid4());
  }

  public static function get_user_agent() {
    $ua = new UserAgent();
    return [
      "client" => $ua->client(),
      "device" => $ua->device(),
      "os" => $ua->os(),
    ];
  }

  public static function to_string($data) {
    if (empty($data))
      return null;

    return !is_string($data) ? json_encode($data) : $data;
  }
}
