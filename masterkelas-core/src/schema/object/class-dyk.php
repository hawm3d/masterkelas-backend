<?php

namespace MasterKelas\Schema\Object;

/**
 * Did You Know object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class DidYouKnow {
  public static function register() {
    self::dyk_type();
  }

  public static function dyk_type() {
    register_graphql_object_type("DidYouKnow", [
      "description" => "Did YouK now attributes",
      "fields" => [
        "icon" => [
          "type" => ["non_null" => "String"],
          "description" => "Did You Know icon ID",
        ],
        "title" => [
          "type" => ["non_null" => "String"],
          "description" => "Did You Know title",
        ],
        "description" => [
          "type" => ["non_null" => "String"],
          "description" => "Did You Know description",
        ],
      ],
    ]);
  }
}
