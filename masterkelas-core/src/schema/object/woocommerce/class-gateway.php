<?php

namespace MasterKelas\Schema\Object;

/**
 * Gateway object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Gateway {
  public static function register() {
    self::gateway_type();
  }

  public static function gateway_type() {
    register_graphql_object_type("Gateway", [
      "description" => "Gateway attributes",
      "fields" => [
        "title" => [
          "type" => ["non_null" => "String"],
          "description" => "Gateway title",
        ],
      ],
    ]);
  }
}
