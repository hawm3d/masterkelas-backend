<?php

namespace MasterKelas\Schema\Object;

/**
 * Video object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Video {
  public static function register() {
    self::image_type();
  }

  public static function image_type() {
    register_graphql_object_type("Video", [
      "description" => "Video attributes",
      "fields" => [
        "url" => [
          "type" => "String",
          "description" => "Video url",
        ],
        "poster" => [
          "type" => "Image",
          "description" => "Video poster",
        ],
      ],
    ]);
  }
}
