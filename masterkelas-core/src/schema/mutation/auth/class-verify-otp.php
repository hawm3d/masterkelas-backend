<?php

namespace MasterKelas\Schema\Mutation;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use MasterKelas\Controller\LoginController;
use MasterKelas\Controller\RegisterController;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\OTP as OTPModel;
use MasterKelas\Model\RateLimiter;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;
use WPGraphQL\AppContext;

/**
 * Verify OTP
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class VerifyOTP {
  public static function register() {
    Schema::mutation(
      "verifyOTP",
      [
        "inputFields" => self::input_fields(),
        "outputFields" => self::output_fields(),
        "mutate" => self::mutate(),
      ],
      // ["zone" => "guest"],
    );
  }

  public static function input_fields() {
    return [
      "context" => [
        "type" => ["non_null" => "AuthContext"],
        "description" => "Auth context"
      ],
      "type" => [
        "type" => "String",
        "description" => "Type of OTP provider (mobile, email and ...)"
      ],
      "recipient" => [
        "type" => "String",
        "description" => "OTP recipient"
      ],
      "code" => [
        "type" => "String",
        "description" => "One time Password"
      ],
      "firstName" => [
        "type" => "String",
        "description" => "First name"
      ],
      "lastName" => [
        "type" => "String",
        "description" => "Last name"
      ],
    ];
  }

  public static function output_fields() {
    return [
      "auth" => [
        "type" => "Auth",
        "description" => "Authentication tokens and session info",
      ],
    ];
  }

  public static function mutate() {
    return function ($input, MasterContext $context, ResolveInfo $info) {
      $auth_context = Auth::validate_context($input['context'] ?? "");

      if (in_array($auth_context, ['login', 'register']) && !is_null($context->auth)) {
        Schema::set_status(403);
        throw new MasterException("auth.guest.only");
      }

      if ($auth_context === 'complete-registration' && is_null($context->auth)) {
        Schema::set_status(403);
        throw new MasterException("auth.user.only");
      }

      switch ($auth_context) {
        case 'login':
          return LoginController::verify_otp($input);
          break;
        case 'register':
          return RegisterController::verify_otp($input, $context);
          break;
        case 'complete-registration':
          return RegisterController::verify_complete_otp($input, $context);
          break;
      }

      return;
    };
  }
}
