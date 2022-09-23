<?php

namespace MasterKelas\Schema\Query;

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

          try {
            $query = new \WP_Query([
              "post_type" => "sfwd-courses",
              "post_status" => "publish",
              "posts_per_page" => -1,
              "fields" => "post_names",
            ]);

            if ($query instanceof \WP_Query && $query->have_posts())
              foreach ($query->posts as $course)
                if (!empty($course->post_name))
                  $slugs[] = $course->post_name;
          } catch (\Throwable $th) {
            $slugs = [];
          }

          return $slugs;
        }
      ]
    );
  }
}
