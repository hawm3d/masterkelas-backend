<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\MasterException;
use MasterKelas\Model\Course as ModelCourse;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Course
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Course {
  public static function register() {
    self::course_field();
  }

  public static function course_field() {
    Schema::query(
      'RootQuery',
      "course",
      [
        "type" => "Course",
        "description" => "MasterKelas Course",
        "args" => [
          "context" => [
            "type" => [
              "non_null" => "String"
            ],
            "description" => "Find course by slug or id"
          ],
          "prop" => [
            "type" => [
              "non_null" => "String"
            ],
            "description" => "Course slug|id"
          ]
        ],
        "resolve" => function ($_, $args, MasterContext $context) {
          $auth = $context->auth;
          $course = null;

          try {
            if (!isset($args['context'], $args['prop']) || !in_array($args['context'], ['slug', 'id']) || empty($args['prop']))
              throw new \Exception();

            $prop = sanitize_text_field($args['prop']);

            switch ($args['context']) {
              case 'id':
                $course = ModelCourse::get_course_by_id((int) $prop);
                break;

              case 'slug':
              default:
                $course = ModelCourse::get_course_by_slug($prop);
                break;
            }
          } catch (\Throwable $th) {
            throw new MasterException("course.not.found");
          }

          return ModelCourse::get_course($course, $auth ? $auth->user : null);
        }
      ]
    );
  }
}
