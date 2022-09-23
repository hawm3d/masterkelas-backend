<?php

namespace MasterKelas\Schema\Object;

/**
 * Auth method object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class AuthMethod {
  public static function register() {
    self::method_type();
  }

  public static function method_type() {
    register_graphql_object_type("AuthMethod", [
      "description" => "Auth method object",
      "fields" => [
        "type" => [
          "type" => ["non_null" => "String"],
          "description" => "Name of the method",
        ],
        "otpLength" => [
          "type" => "Integer",
          "description" => "Length of the OTP token",
        ],
        "restrictNonIranian" => [
          "type" => "Boolean",
          "description" => "Is this method only for Iranian?",
        ],
        "oAuth" => [
          "type" => "String",
          "description" => "OAuth options and settings in JSON format",
        ],
      ],
    ]);
  }
}
