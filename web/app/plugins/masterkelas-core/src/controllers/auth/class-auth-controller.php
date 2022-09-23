<?php

namespace MasterKelas\Controller;

use MasterKelas\AES128CBC;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\RateLimiter;
use MasterKelas\Model\Session;
use MasterKelas\Model\User;
use MasterKelas\Schema;
use MasterKelas\UserAgent;

/**
 * Auth Controller
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class AuthController {
  public static function middleware($args = []) {
    $defaults = [
      "context" => $args['context'] ?? "login",
      "check_active" => true,
      "throttle" => true,
      "throttle_action" => "auth-{$args['method']}",
      "limit" => 10,
      "interval" => MINUTE_IN_SECONDS,
    ];

    $args = wp_parse_args($args, $defaults);

    try {
      $method = Auth::validate_method($args['method']);

      if ($args["throttle"])
        RateLimiter::throttle([
          "action" => $args['throttle_action'],
          "limit" => $args['limit'],
          "interval" => $args['interval']
        ]);

      if ($args['check_active'] && !Auth::is_active($method))
        throw new MasterException("{$method}.unavailable");

      return $args['callback']();
    } catch (\Throwable $th) {
      if (is_a($th, '\MasterKelas\MasterException'))
        throw new MasterException("auth.{$args['context']}.{$th->getMessage()}");

      throw $th;
    }
  }

  public static function auth_response($tokens) {
    return [
      "tokenType" => "Bearer",
      "accessToken" => $tokens['accessToken'],
      "refreshToken" => $tokens['refreshToken'],
    ];
  }

  public static function sign($user_id, $auth_method = null) {
    if (!$user_id || $user_id <= 0)
      throw new MasterException("invalid.userid");

    $session = Session::start($user_id, $auth_method);
    $auth = Auth::fromSession($session);
    $tokens = $auth->issue();

    return [
      "auth" => static::auth_response($tokens)
    ];
  }

  public static function refresh(UserAgent $ua) {
    $auth = Auth::fromHeader('refresh', $ua);
    $tokens = $auth->issue();
    Session::beat($auth->session->id);

    return static::auth_response($tokens);
  }

  public static function terminate(Auth $auth) {
    $encrypted = null;

    if (in_array($auth->session->creation_method, ["mobile", "session"])) {
      $user = [
        "id" => Schema::toGlobalId("user", $auth->user->id),
        "name" => $auth->user->name,
        "by" => $auth->session->creation_method,
        "field" => $auth->session->creation_method === 'mobile' ? $auth->user->mobile : $auth->user->email,
      ];
      $encrypted = AES128CBC::encrypt($user);
    }

    Session::end($auth->session->id);

    return $encrypted;
  }

  public static function remember_user($remember_token) {
    $remember_token = AES128CBC::decrypt($remember_token);

    if (
      !isset($remember_token['id'], $remember_token['name'], $remember_token['by'], $remember_token['field'])
      || !in_array($remember_token['by'], ['mobile', 'email'])
    )
      throw new MasterException("invalid.remember.payload");

    if (!Auth::is_active($remember_token['by']))
      throw new MasterException("invalid.remember.method");

    $user_id = Schema::fromGlobalId($remember_token['id']);
    if (!$user_id || !$user_id['id'])
      throw new MasterException("invalid.remember.id");

    $user = new User((int) $user_id['id']);
    if (!$user || !$user->is_active())
      throw new MasterException("invalid.remember.user");

    $user_field = $remember_token['by'] === 'mobile' ? $user->mobile : $user->email;
    if (empty($user_field) || $user_field !== $remember_token['field'])
      throw new MasterException("invalid.remember.field");

    return [
      "name" => sanitize_text_field($remember_token['name']),
      "by" => sanitize_text_field($remember_token['by']),
      "field" => sanitize_text_field($remember_token['field']),
    ];
  }
}
