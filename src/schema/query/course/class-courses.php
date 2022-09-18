<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\MasterException;
use MasterKelas\Model\Course as ModelCourse;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Courses
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Courses {
  public static function register() {
    self::course_field();
  }

  public static function course_field() {
    Schema::query(
      'RootQuery',
      "courses",
      [
        "type" => [
          "non_null" => "Courses"
        ],
        "args" => [
          "term" => [
            "type" => "String",
            "description" => "Search query"
          ],
          "category" => [
            "type" => ["list_of" => "String"],
            "description" => "Filter courses by category"
          ],
          "tags" => [
            "type" => ["list_of" => "Integer"],
            "description" => "Filter courses by tag ID"
          ],
          "page" => [
            "type" => "Integer",
            "description" => "Current page"
          ],
          "perPage" => [
            "type" => "Integer",
            "description" => "Total courses per page"
          ],
          "exclude" => [
            "type" => "String",
            "description" => "List of course ids to exclude from query"
          ],
          "sort" => [
            "type" => "String",
            "description" => "Sort retrieved courses"
          ],
          "student" => [
            "type" => ["list_of" => "String"],
            "description" => "Filter courses by student activities"
          ],
        ],
        "description" => "MasterKelas Courses",
        "resolve" => function ($_, $args, MasterContext $context) {
          $auth = $context->auth;
          $courses = [];
          $default_pagination = [
            "current" => 1,
            "next" => null,
            "previous" => null,
            "lastPage" => 1,
            "perPage" => 12,
            "total" => 0,
          ];
          $pagination = $default_pagination;

          try {
            $query_args = ModelCourse::create_query_args($args, $context);
            $query_args = array_merge($query_args, ["fields" => "ids"]);

            $query = new \WP_Query($query_args);
            if ($query instanceof \WP_Query && $query->have_posts()) {
              foreach ($query->posts as $course) {
                $courses[] = ModelCourse::get_course_card(get_post($course), $auth ? $auth->user : null);
              }
            }

            if (empty($courses))
              throw new \Exception();

            $pagination['current'] = (int) ($query_args['paged'] ?? 1);
            $pagination['perPage'] = (int) ($query_args['posts_per_page'] ?? 12);
            $pagination['lastPage'] = (int) ($query->max_num_pages ?: 1);

            if ($pagination['current'] > $pagination['lastPage'])
              throw new \Exception();

            $pagination['next'] = $pagination['lastPage'] > $pagination['current'] ? (int) ($pagination['current'] + 1) : null;
            $pagination['previous'] = $pagination['current'] > 1 && $pagination['lastPage'] > 1 ? (int) ($pagination['current'] - 1) : null;
            $pagination['total'] = (int) ($query->found_posts ?: 0);
          } catch (\Throwable $th) {
            $courses = [];
            $pagination = $default_pagination;

            //throw $th;
          }

          return [
            "items" => $courses,
            "pagination" => $pagination
          ];
        }
      ]
    );
  }
}
