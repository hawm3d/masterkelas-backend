<?php

namespace MasterKelas\Controller;

use MasterKelas\Database\OTP_Query;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Model\RateLimiter;
use MasterKelas\Model\User;

/**
 * Login Controller
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class LoginController {
  public static function verify_otp($payload) {
    return self::middleware([
      "method" => isset($payload['type']) ? $payload['type'] : "",
      "throttle_action" => "auth-otp-verify",
      "limit" => 10,
      "interval" => MINUTE_IN_SECONDS,
      "callback" => function () use ($payload) {
        if (!isset($payload['code']) || empty($payload['code']))
          throw new MasterException("otp.invalid.code");

        $otp_model = new OTP($payload);
        $otp_model->verify($payload['code']);

        $user = User::find_by([$otp_model->type => $otp_model->recipient]);
        if (!$user)
          throw new MasterException("otp.recipient.notfound");

        return AuthController::sign($user->ID, $otp_model->type);
      }
    ]);
  }

  public static function request_otp($payload) {
    return self::middleware([
      "method" => isset($payload['type']) ? $payload['type'] : "",
      "throttle_action" => "auth-otp-request",
      "limit" => 5,
      "interval" => MINUTE_IN_SECONDS,
      "callback" => function () use ($payload) {
        $otp_model = new OTP($payload);

        $user = User::find_by([$otp_model->type => $otp_model->recipient]);
        if (!$user)
          throw new MasterException("otp.recipient.notfound");

        $otp_id = $otp_model->send();
        $otp = (new OTP_Query())->get_item($otp_id);

        if (!$otp)
          throw new \Exception("otp.null");

        return $otp;
      }
    ]);
  }

  public static function google($payload) {
    return self::middleware([
      "method" => "google",
      "callback" => function () use ($payload) {
        $user_info = Google::oauth($payload);
        $user = User::find_by(['email' => sanitize_email($user_info['email'])]);

        if (!$user)
          throw new MasterException("google.notfound");

        return AuthController::sign($user->ID);
      }
    ]);
  }

  public static function middleware($args = []) {
    $defaults = [
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
        throw new MasterException("auth.login.{$th->getMessage()}");

      throw $th;
    }
  }
}
