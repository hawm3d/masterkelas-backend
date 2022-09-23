<?php

namespace MasterKelas\Schema\Mutation;

use MasterKelas\Controller\LoginController;
use MasterKelas\Controller\RegisterController;
use MasterKelas\Model\Auth;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Auth by Google
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Google {
  public static function register() {
    Schema::mutation(
      "google",
      [
        "inputFields" => self::input_fields(),
        "outputFields" => self::output_fields(),
        "mutate" => self::mutate(),
      ],
      ["zone" => "guest"],
    );
  }

  public static function input_fields() {
    return [
      "context" => [
        "type" => ["non_null" => "AuthContext"],
        "description" => "Auth context"
      ],
      "code" => [
        "type" => ["non_null" => "String"],
        "description" => "Google auth code"
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
        "type" => ["non_null" => "Auth"],
        "description" => "Authentication tokens and ids",
      ],
    ];
  }

  public static function mutate() {
    return function ($input, MasterContext $context) {
      $auth_context = Auth::validate_context($input['context'] ?? "");

      switch ($auth_context) {
        case 'login':
          return LoginController::google($input);
          break;
        case 'register':
          return RegisterController::google($input, $context);
          break;
      }

      return;
    };
  }
}
