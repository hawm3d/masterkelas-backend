<?php

namespace MasterKelas\Schema\Object;

/**
 * Register Step object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RegisterStep {
  public static function register() {
    self::method_type();
  }

  public static function method_type() {
    register_graphql_object_type("RegisterStep", [
      "description" => "Register step object",
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
