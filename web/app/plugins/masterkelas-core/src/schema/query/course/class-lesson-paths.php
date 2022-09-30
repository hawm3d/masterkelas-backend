<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Model\Course;
use MasterKelas\OptimusId;
use MasterKelas\Schema;

/**
 * All Lesson Paths
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class LessonPaths {
  public static function register() {
    self::lesson_slugs();
  }

  public static function lesson_slugs() {
    Schema::query(
      'RootQuery',
      "LessonPaths",
      [
        "type" => [
          "list_of" => "LessonPath"
        ],
        "description" => "All Lesson Paths",
        "resolve" => function ($_, $__) {
          return Course::paths();
        }
      ]
    );
  }
}
