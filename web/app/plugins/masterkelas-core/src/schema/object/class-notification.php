<?php

namespace MasterKelas\Schema\Object;

/**
 * Notification object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Notification {
  public static function register() {
    self::notification_type();
  }

  public static function notification_type() {
    register_graphql_object_type("Notification", [
      "description" => "Notification data",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "String"],
          "description" => "User notification id",
        ],
        "actionId" => [
          "type" => "Integer",
          "description" => "User notification action id",
        ],
        "type" => [
          "type" => ["non_null" => "String"],
          "description" => "User notification type",
        ],
        "template" => [
          "type" => ["non_null" => "String"],
          "description" => "User notification template",
        ],
      ],
    ]);
  }
}
