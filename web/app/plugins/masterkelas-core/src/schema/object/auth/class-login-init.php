<?php

namespace MasterKelas\Schema\Object;

/**
 * Login init object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class LoginInit {
  public static function register() {
    self::login_init_type();
  }

  public static function login_init_type() {
    register_graphql_object_type("LoginInit", [
      "description" => "Login form settings",
      "fields" => [
        "methods" => [
          "type" => ["list_of" => "AuthMethod"],
          "description" => "Available login methods",
        ],
        "remember" => [
          "type" => "RememberedUser",
          "description" => "Remembered User",
        ],
      ],
    ]);
  }
}
