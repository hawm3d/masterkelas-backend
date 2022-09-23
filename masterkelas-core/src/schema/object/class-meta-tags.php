<?php

namespace MasterKelas\Schema\Object;

/**
 * Meta Tags object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class MetaTags {
  public static function register() {
    self::meta_tags_type();
  }

  public static function meta_tags_type() {
    register_graphql_object_type("MetaTags", [
      "description" => "Meta Tags attributes",
      "fields" => [
        "title" => [
          "type" => "String",
          "description" => "Title tag",
        ],
        "description" => [
          "type" => "String",
          "description" => "Description meta tag",
        ],
      ],
    ]);
  }
}
