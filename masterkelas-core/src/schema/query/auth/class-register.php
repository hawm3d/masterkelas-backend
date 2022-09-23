<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Controller\AuthController;
use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Schema;

/**
 * Register
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Register {
  public static function register() {
    self::register_field();
  }

  public static function register_field() {
    Schema::query(
      'RootQuery',
      "register",
      [
        "type" => "RegisterInit",
        "description" => "Register form settings",
        "resolve" => function ($_) {
          $methods = [];
          $dyk = [];
          $content = "";
          $policy_id = (int) \MasterKelas\Admin::get_option("policy-page");

          if ($policy_id > 0)
            $content = apply_filters('the_content', get_post_field('post_content', $policy_id));

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

          $dyk[] = [
            "icon" => "transporter",
            "title" => 'آیا میدانستید؟',
            "description" => 'خانواده مسترکلاس شامل 6 هزار دانشجو و 200 استاد در 15 تخصص مختلف می باشد. خوشحال می شویم شما هم به ما بپیوندید!',
          ];

          $dyk[] = [
            "icon" => "person-to-portal",
            "title" => 'آیا میدانستید؟',
            "description" => 'خانواده مسترکلاس شامل 6 هزار دانشجو و 200 استاد در 15 تخصص مختلف می باشد. خوشحال می شویم شما هم به ما بپیوندید!',
          ];

          $dyk[] = [
            "icon" => "atom",
            "title" => 'آیا میدانستید؟',
            "description" => 'خانواده مسترکلاس شامل 6 هزار دانشجو و 200 استاد در 15 تخصص مختلف می باشد. خوشحال می شویم شما هم به ما بپیوندید!',
          ];

          return [
            "methods" => $methods,
            "accountNumber" => (int) count_users()['total_users'] + 1,
            "dyk" => $dyk,
            "privacyPolicy" => $content
          ];
        }
      ]
    );
  }
}
