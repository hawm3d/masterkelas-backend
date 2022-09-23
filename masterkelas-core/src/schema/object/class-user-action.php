<?php

namespace MasterKelas\Schema\Object;

/**
 * User action object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class UserAction {
  public static function register() {
    self::user_action_type();
  }

  public static function user_action_type() {
    register_graphql_object_type("UserAction", [
      "description" => "User action data",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "String"],
          "description" => "User action id",
        ],
        "action" => [
          "type" => ["non_null" => "String"],
          "description" => "User action name",
        ],
        "priority" => [
          "type" => ["non_null" => "Integer"],
          "description" => "User action priority",
        ],
      ],
    ]);
  }
}
