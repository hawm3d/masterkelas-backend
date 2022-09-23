<?php

namespace MasterKelas\Schema\Object;

/**
 * User session object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class UserSession {
  public static function register() {
    self::user_session_type();
  }

  public static function user_session_type() {
    register_graphql_object_type("UserSession", [
      "description" => "User session info",
      "fields" => [
        "status" => [
          "type" => ["non_null" => "String"],
          "description" => "Session status",
        ],
        "active" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is session active?",
        ],
        "locked" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is session locked?",
        ],
        "terminated" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is session terminated?",
        ],
        "lastActivity" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Session last activity",
        ],
      ],
    ]);
  }
}
