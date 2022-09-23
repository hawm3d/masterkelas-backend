<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Controller\AuthController;
use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Schema;

/**
 * Login
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Login {
  public static function register() {
    self::login_field();
  }

  public static function login_field() {
    Schema::query(
      'RootQuery',
      "login",
      [
        "type" => "LoginInit",
        "description" => "Config login form",
        "args" => [
          "remember" => [
            "type" => "String",
            "description" => "Remember Token"
          ]
        ],
        "resolve" => function ($_, $args) {
          $methods = [];
          $remembered = null;
          $remember_token = sanitize_text_field($args['remember'] ?? "");

          if (Auth::is_active("mobile")) {
            array_push($methods, [
              "type" => "mobile",
              "otpLength" => OTP::get_code_length("mobile"),
              "restrictNonIranian" => \MasterKelas\Admin::get_option("auth-mobile-restrict")
            ]);
          }

          if (Auth::is_active("email")) {
            array_push($methods, [
              "type" => "email",
              "otpLength" => OTP::get_code_length("email"),
              "restrictNonIranian" => \MasterKelas\Admin::get_option("auth-email-restrict")
            ]);
          }

          if (Auth::is_active("google") && $client_id = Google::get_client_id()) {
            array_push($methods, [
              "type" => "google",
              "oAuth" => json_encode([
                "client_id" => $client_id
              ]),
              "restrictNonIranian" => \MasterKelas\Admin::get_option("auth-google-restrict")
            ]);
          }

          if (!empty($remember_token)) {
            try {
              $remembered = AuthController::remember_user($remember_token);
            } catch (\Throwable $th) {
              graphql_debug($th->getMessage());
              $remembered = null;
            }
          }

          return [
            "methods" => $methods,
            "remember" => $remembered
          ];
        }
      ]
    );
  }
}
