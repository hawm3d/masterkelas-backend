<?php

namespace MasterKelas\Schema\Object;

/**
 * Course Duration object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class CourseDuration {
  public static function register() {
    self::course_duration_type();
  }

  public static function course_duration_type() {
    register_graphql_object_type("CourseDuration", [
      "description" => "Course duration attributes",
      "fields" => [
        "hour" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Duration hour",
        ],
        "minute" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Duration minute",
        ],
        "second" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Duration second",
        ],
      ],
    ]);
  }
}
