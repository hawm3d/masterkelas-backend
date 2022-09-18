<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\MasterException;
use MasterKelas\Model\Course as ModelCourse;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Lesson
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Lesson {
  public static function register() {
    self::lesson_field();
  }

  public static function lesson_field() {
    Schema::query(
      'RootQuery',
      "lesson",
      [
        "type" => "LessonPage",
        "description" => "MasterKelas Lesson",
        "args" => [
          "id" => [
            "type" => [
              "non_null" => "Integer"
            ],
            "description" => "Lesson id"
          ]
        ],
        "resolve" => function ($_, $args, MasterContext $context) {
          $auth = $context->auth;
          $lesson = null;

          try {
            if (empty($args['id']) || $args['id'] <= 0)
              throw new \Exception();

            $lesson = ModelCourse::get_lesson_by_id((int) $args['id']);
          } catch (\Throwable $th) {
            throw new MasterException("lesson.not.found");
          }

          return ModelCourse::get_lesson($lesson, $auth ? $auth->user : null);
        }
      ]
    );
  }
}
