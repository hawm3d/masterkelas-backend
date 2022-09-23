<?php

namespace MasterKelas\Schema\Mutation;

use MasterKelas\Controller\LoginController;
use MasterKelas\Controller\RegisterController;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\OTP as OTPModel;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * OTP Mutation
 *
 * Send and verify OTP
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP {
  public static function register() {
    Schema::mutation(
      "otp",
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
      "requestedAt" => [
        "type" => "Integer",
        "description" => "OTP creation date in Unix timestamp format",
        "resolve" => function ($otp) {
          return $otp->created_at->timestamp;
        }
      ],
      "seconds" => [
        "type" => "Integer",
        "description" => "How many seconds does user need to wait before each OTP request?",
        "resolve" => function ($otp) {
          return OTPModel::get_retry_seconds($otp->type);
        }
      ],
    ];
  }

  public static function mutate() {
    return function ($input, MasterContext $context) {
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
          return LoginController::request_otp($input);
          break;
        case 'register':
        case 'complete-registration':
          return RegisterController::request_otp($input, $context);
          break;
      }

      return;
    };
  }
}
