<?php

namespace MasterKelas\Schema\Query;

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
          $data = [];

          try {
            $query = new \WP_Query([
              "post_type" => "sfwd-courses",
              "post_status" => "publish",
              "posts_per_page" => -1,
              "fields" => "post_names",
            ]);

            if ($query instanceof \WP_Query && $query->have_posts())
              foreach ($query->posts as $course)
                if (!empty($course->post_name)) {
                  $lessonIds = [];
                  $lessons = learndash_get_course_lessons_list($course);
                  foreach ($lessons as $lesson) {
                    if ($lesson['id'] > 0)
                      $lessonIds[] = (int) OptimusId::course_lesson()->encode($lesson['id']);
                  }

                  if (!empty($lessonIds))
                    $data[] = [
                      "course" => (string) $course->post_name,
                      "lessons" => $lessonIds
                    ];
                }
          } catch (\Throwable $th) {
            throw $th;
            $data = [];
          }

          return $data;
        }
      ]
    );
  }
}
