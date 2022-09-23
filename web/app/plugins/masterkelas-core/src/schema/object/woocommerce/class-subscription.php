<?php

namespace MasterKelas\Schema\Object;

/**
 * Subscription object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Subscription {
  public static function register() {
    self::subscription_type();
  }

  public static function subscription_type() {
    register_graphql_object_type("Subscription", [
      "description" => "Subscription attributes",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Subscription id",
        ],
        "price" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Subscription price",
        ],
        "days" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Subscription days",
        ],
      ],
    ]);
  }
}
