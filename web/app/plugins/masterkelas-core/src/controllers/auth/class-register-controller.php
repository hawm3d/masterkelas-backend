<?php

namespace MasterKelas\Controller;

use Carbon\Carbon;
use MasterKelas\Database\OTP_Query;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Model\User;
use MasterKelas\Model\UserAction;
use MasterKelas\NanoId;
use MasterKelas\Schema\MasterContext;
use MasterKelas\Validator;

/**
 * Register Controller
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class RegisterController {

  public static function create($args, MasterContext $context) {
    if (
      !isset($args['first_name'], $args['last_name'], $args['provider'])
      || ($args['provider'] === 'mobile' && !isset($args['mobile']))
      || ($args['provider'] === 'email' && !isset($args['email']))
      || ($args['provider'] === 'google' && !isset($args['email']))
    )
      throw new MasterException("invalid.userdata");

    $name = "{$args['first_name']} {$args['last_name']}";
    $user_name = isset($args['email']) ? $args['email'] : $args['mobile'];
    $userdata = array(
      'user_login'            => $user_name,
      'user_nicename'         => $name,
      'display_name'          => $name,
      'first_name'            => $args['first_name'],
      'last_name'             => $args['last_name'],
      'user_pass'             => null,
    );

    if (isset($args['email']))
      $userdata['user_email'] = $args['email'];

    $user_id = wp_insert_user($userdata);
    if (is_wp_error($user_id) || $user_id <= 0)
      throw new MasterException("invalid.userdata");

    $user = new User($user_id);
    $user->update_meta("registration_method", $args['provider']);

    if (isset($args['mobile']))
      $user->update_meta("mobile", $args['mobile'], false);

    $action_fields = [];
    if (!$context->region->is_allowed()) {
      $user->update_meta("nationality_status", "unknown");
      $action_fields[] = [
        "type" => "nationality_validation",
        "question_uid" => Auth::get_random_nationality_question()['uid'],
        "ttl" => (int) (\MasterKelas\Admin::get_option("auth-nationality-validation-ttl") ?? MINUTE_IN_SECONDS * 10),
        "max_attempts" => (int) (\MasterKelas\Admin::get_option("auth-nationality-validation-attempts") ?? 5),
        "attempts" => 0,
      ];
    }

    if (!$user->mobile && $context->region->is_allowed() && Auth::is_active("mobile"))
      $action_fields[] = [
        "type" => 'mobile',
      ];

    if (!$user->email && Auth::is_active("email"))
      $action_fields[] = [
        "type" => 'email',
      ];

    if (!empty($action_fields))
      try {
        $action = UserAction::assign([
          "user_id" => $user->id,
          "action" => "complete-registration",
          "priority" => 100,
          "data" => [
            "fields" => $action_fields
          ]
        ]);

        if (is_wp_error($action[0]) || $action[0] <= 0)
          throw new \Exception();

        $user->update_meta("action_required", $action[1]);
      } catch (\Throwable $th) {
        graphql_debug($th->getMessage());
        $user->update_meta("action_required", 'failed');
      }

    return AuthController::sign($user->id, $args['provider']);
  }

  public static function sanitize_names($payload) {
    if (!isset($payload['firstName']))
      throw new MasterException("first.name.required");

    if (!isset($payload['lastName']))
      throw new MasterException("last.name.required");

    $first_name = sanitize_text_field($payload['firstName']);
    $last_name = sanitize_text_field($payload['lastName']);

    if (!Validator::name($first_name))
      throw new MasterException("invalid.first.name");

    if (!Validator::name($last_name))
      throw new MasterException("invalid.last.name");

    return [$first_name, $last_name];
  }

  public static function google($payload, MasterContext $context) {
    return AuthController::middleware([
      "context" => "register",
      "method" => "google",
      "callback" => function () use ($payload, $context) {
        $names = self::sanitize_names($payload);
        $user_info = Google::oauth($payload);

        $email = sanitize_email($user_info['email']);
        $user = User::find_by(['email' => $email]);
        if ($user)
          throw new MasterException("user.exists");

        $new_user = [
          "first_name" => $names[0],
          "last_name" => $names[1],
          "provider" => "google",
          "email" => $email
        ];
        return self::create($new_user, $context);
      }
    ]);
  }

  public static function verify_otp($payload, MasterContext $context) {
    return AuthController::middleware([
      "context" => "register",
      "method" => isset($payload['type']) ? $payload['type'] : "",
      "throttle_action" => "auth-otp-verify",
      "limit" => 100,
      "interval" => MINUTE_IN_SECONDS,
      "callback" => function () use ($payload, $context) {
        $names = self::sanitize_names($payload);

        if (!isset($payload['code']) || empty($payload['code']))
          throw new MasterException("otp.invalid.code");

        $otp_model = new OTP($payload);
        $otp_model->verify($payload['code']);

        $user = User::find_by([$otp_model->type => $otp_model->recipient]);
        if ($user)
          throw new MasterException("user.exists");

        $new_user = [
          "first_name" => $names[0],
          "last_name" => $names[1],
          "provider" => $otp_model->type,
          $otp_model->type => $otp_model->recipient
        ];

        return self::create($new_user, $context);
      }
    ]);
  }

  public static function verify_complete_otp($payload, MasterContext $context) {
    return AuthController::middleware([
      "context" => "register.complete",
      "method" => isset($payload['type']) ? $payload['type'] : "",
      "throttle_action" => "auth-otp-verify",
      "limit" => 100,
      "interval" => MINUTE_IN_SECONDS,
      "callback" => function () use ($payload, $context) {
        if (!isset($payload['code']) || empty($payload['code']))
          throw new MasterException("otp.invalid.code");

        $otp_model = new OTP($payload);
        $otp_model->verify($payload['code']);

        $user_exists = User::find_by([$otp_model->type => $otp_model->recipient]);
        if ($user_exists)
          throw new MasterException("user.exists");

        $user = $context->auth->user;

        if ($otp_model->type === 'email')
          $completed = wp_update_user(["ID" => $user->id, "user_email" => $otp_model->recipient]);

        if ($otp_model->type === 'mobile')
          $completed = $user->update_meta("mobile", $otp_model->recipient, false);

        if (is_wp_error($completed) || !$completed)
          throw new MasterException("complete.registration.failed");

        $action = UserAction::assign([
          "user_id" => $context->auth->user->id,
          "action" => "welcome",
          "priority" => 100,
        ]);

        if (is_wp_error($action[0]) || $action[0] <= 0)
          $context->auth->user->delete_meta("action_required");
        else
          $context->auth->user->update_meta("action_required", $action[1]);
        return;
      }
    ]);
  }

  public static function request_otp($payload, MasterContext $context) {
    return AuthController::middleware([
      "context" => isset($payload['context']) && $payload['context'] === 'complete-registration' ? 'register.complete' : "register",
      "method" => isset($payload['type']) ? $payload['type'] : "",
      "throttle_action" => "auth-otp-request",
      "limit" => 50,
      "interval" => MINUTE_IN_SECONDS,
      "callback" => function () use ($payload, $context) {
        if (is_null($context->auth))
          self::sanitize_names($payload);

        $otp_model = new OTP($payload);

        $user = User::find_by([$otp_model->type => $otp_model->recipient]);
        if ($user)
          throw new MasterException("user.exists");

        $otp_id = $otp_model->send();
        $otp = (new OTP_Query())->get_item($otp_id);

        if (!$otp)
          throw new \Exception("otp.null");

        return $otp;
      }
    ]);
  }
}
