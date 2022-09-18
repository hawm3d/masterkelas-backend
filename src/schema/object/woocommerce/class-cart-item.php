<?php

namespace MasterKelas\Schema\Object;

/**
 * CartItem object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class CartItem {
  public static function register() {
    self::gateway_type();
  }

  public static function gateway_type() {
    register_graphql_object_type("CartItem", [
      "description" => "Cart Item attributes",
      "fields" => [
        "productId" => [
          "type" => ["non_null" => "String"],
          "description" => "Cart item product id",
        ],
      ],
    ]);
  }
}
