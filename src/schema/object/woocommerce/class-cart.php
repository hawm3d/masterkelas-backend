<?php

namespace MasterKelas\Schema\Object;

/**
 * Cart object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Cart {
  public static function register() {
    self::cart_type();
  }

  public static function cart_type() {
    register_graphql_object_type("Cart", [
      "description" => "Cart attributes",
      "fields" => [
        "items" => [
          "type" => ["list_of" => "CartItem"],
          "description" => "Cart item",
        ],
      ],
    ]);
  }
}
