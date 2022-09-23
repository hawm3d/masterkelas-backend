<?php

namespace MasterKelas\Schema\Object;

/**
 * Course Category object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class CourseCategory {
  public static function register() {
    self::category_type();
  }

  public static function category_type() {
    register_graphql_object_type("CourseCategory", [
      "description" => "Course Category attributes",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Course Category id",
        ],
        "name" => [
          "type" => ["non_null" => "String"],
          "description" => "Course Category name",
        ],
        "slug" => [
          "type" => ["non_null" => "String"],
          "description" => "Course Category slug",
        ],
        "icon" => [
          "type" => "String",
          "description" => "Course Category svg icon",
        ],
        "image" => [
          "type" => "Image",
          "description" => "Course Category image",
        ],
        "description" => [
          "type" => "String",
          "description" => "Course Category description",
        ],
      ],
    ]);
  }
}
