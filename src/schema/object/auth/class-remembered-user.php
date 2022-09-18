<?php

namespace MasterKelas\Schema\Object;

/**
 * Remembered User object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RememberedUser {
  public static function register() {
    self::remembered_user_type();
  }

  public static function remembered_user_type() {
    register_graphql_object_type("RememberedUser", [
      "description" => "Remembered user",
      "fields" => [
        "name" => [
          "type" => "String",
          "description" => "User name",
        ],
        "by" => [
          "type" => "String",
          "description" => "Remembered method",
        ],
        "field" => [
          "type" => "String",
          "description" => "Remembered field",
        ],
      ],
    ]);
  }
}
