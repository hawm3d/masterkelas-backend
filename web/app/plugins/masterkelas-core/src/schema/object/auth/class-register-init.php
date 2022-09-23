<?php

namespace MasterKelas\Schema\Object;

/**
 * Register init object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RegisterInit {
  public static function register() {
    self::register_init_type();
  }

  public static function register_init_type() {
    register_graphql_object_type("RegisterInit", [
      "description" => "Register form settings",
      "fields" => [
        "methods" => [
          "type" => ["list_of" => "AuthMethod"],
          "description" => "Available register methods",
        ],
        "accountNumber" => [
          "type" => "Integer",
          "description" => "New account number",
        ],
        "dyk" => [
          "type" => ["list_of" => "DidYouKnow"],
          "description" => "List of Did you know",
        ],
        "privacyPolicy" => [
          "type" => ["non_null" => "String"],
          "description" => "Privacy policy",
        ],
      ],
    ]);
  }
}
