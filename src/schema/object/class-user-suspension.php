<?php

namespace MasterKelas\Schema\Object;

/**
 * User Suspension object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class UserSuspension {
  public static function register() {
    self::user_suspension_type();
  }

  public static function user_suspension_type() {
    register_graphql_object_type("UserSuspension", [
      "description" => "User suspension status",
      "fields" => [
        "suspended" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is User suspended",
        ],
        "intensity" => [
          "type" => "String",
          "description" => "User suspension intensity",
        ],
        "reason" => [
          "type" => "String",
          "description" => "User suspension reason",
        ],
        "lockdown" => [
          "type" => "Integer",
          "description" => "User suspension lockdown",
        ],
      ],
    ]);
  }
}
