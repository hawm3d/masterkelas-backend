<?php

namespace MasterKelas\Schema\Object;

/**
 * Price object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Price {
  public static function register() {
    self::price_type();
  }

  public static function price_type() {
    register_graphql_object_type("Price", [
      "description" => "Price attributes",
      "fields" => [
        "amount" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Price amount",
        ],
        "currency" => [
          "type" => ["non_null" => "String"],
          "description" => "Price currency",
        ],
      ],
    ]);
  }
}
