<?php

namespace MasterKelas\Schema\Object;

/**
 * User profile object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class UserProfile {
  public static function register() {
    self::user_profile_type();
  }

  public static function user_profile_type() {
    register_graphql_object_type("UserProfile", [
      "description" => "User profile info",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "String"],
          "description" => "User id",
        ],
        "name" => [
          "type" => ["non_null" => "String"],
          "description" => "User full name",
        ],
        "firstName" => [
          "type" => ["non_null" => "String"],
          "description" => "User first name",
        ],
        "lastName" => [
          "type" => ["non_null" => "String"],
          "description" => "User last name",
        ],
      ],
    ]);
  }
}
