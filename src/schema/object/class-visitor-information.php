<?php

namespace MasterKelas\Schema\Object;

/**
 * Visitor Information object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class VisitorInformation {
  public static function register() {
    self::visitor_information_type();
  }

  public static function visitor_information_type() {
    register_graphql_object_type("VisitorInformation", [
      "description" => "Visitor Information",
      "fields" => [
        "ip" => [
          "type" => ["non_null" => "String"],
          "description" => "Visitor ip",
        ],
        "region" => [
          "type" => ["non_null" => "String"],
          "description" => "Visitor region",
        ],
        "timezone" => [
          "type" => ["non_null" => "String"],
          "description" => "Visitor timezone",
        ],
        "bot" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Visitor is a bot?",
        ],
        "authenticated" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Is user?",
        ],
        "policy" => [
          "type" => ["non_null" => "String"],
          "description" => "Visitor policy",
        ],
      ],
    ]);
  }
}
