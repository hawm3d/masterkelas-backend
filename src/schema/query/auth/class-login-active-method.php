<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Schema;

/**
 * Login active methods
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class LoginActiveMethods {
  public static function register() {
    self::login_active_methods_field();
  }

  public static function login_active_methods_field() {
    Schema::query(
      'RootQuery',
      "loginActiveMethods",
      [
        "type" => [
          "list_of" => "AuthMethod"
        ],
        "description" => "Name of the method",
        "resolve" => function () {
          $methods = [];

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

          return $methods;
        }
      ]
    );
  }
}
