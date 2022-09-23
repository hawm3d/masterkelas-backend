<?php

namespace MasterKelas\Schema\Object;

/**
 * Image object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Image {
  public static function register() {
    self::image_type();
  }

  public static function image_type() {
    register_graphql_object_type("Image", [
      "description" => "Image attributes",
      "fields" => [
        "blur" => [
          "type" => ["non_null" => "String"],
          "description" => "Image blurhash",
        ],
        "title" => [
          "type" => "String",
          "description" => "Image title",
        ],
        "alt" => [
          "type" => "String",
          "description" => "Image alt",
        ],
        "url" => [
          "type" => ["non_null" => "String"],
          "description" => "Image url",
        ],
        "width" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Image width",
        ],
        "height" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Image height",
        ],
      ],
    ]);
  }
}
