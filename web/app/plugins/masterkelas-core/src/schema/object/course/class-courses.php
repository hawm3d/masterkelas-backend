<?php

namespace MasterKelas\Schema\Object;

/**
 * Courses object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Courses {
  public static function register() {
    self::course_type();
  }

  public static function course_type() {
    register_graphql_object_type("Courses", [
      "description" => "Courses attributes",
      "fields" => [
        "items" => [
          "type" => ["list_of" => "CourseCard"],
          "description" => "Query items",
        ],
        "pagination" => [
          "type" => ["non_null" => "Pagination"],
          "description" => "Pagination attributes",
        ],
      ],
    ]);
  }
}
