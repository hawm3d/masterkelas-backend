<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Model\Course;
use MasterKelas\Schema;

/**
 * All Course Slugs
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class CourseSlugs {
  public static function register() {
    self::course_slugs();
  }

  public static function course_slugs() {
    Schema::query(
      'RootQuery',
      "CourseSlugs",
      [
        "type" => [
          "list_of" => "String"
        ],
        "description" => "All Course Slugs",
        "resolve" => function ($_, $__) {
          $slugs = [];

          foreach (Course::paths() as $path)
            $slugs[] = $path['course'];

          return $slugs;
        }
      ]
    );
  }
}
