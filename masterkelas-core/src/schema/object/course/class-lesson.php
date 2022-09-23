<?php

namespace MasterKelas\Schema\Object;

/**
 * Lesson object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Lesson {
  public static function register() {
    self::lesson_type();
  }

  public static function lesson_type() {
    register_graphql_object_type("Lesson", [
      "description" => "Lesson attributes",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Lesson id",
        ],
        "order" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Lesson order",
        ],
        "title" => [
          "type" => ["non_null" => "String"],
          "description" => "Lesson title",
        ],
        "description" => [
          "type" => "String",
          "description" => "Lesson description",
        ],
        "shortTitle" => [
          "type" => "String",
          "description" => "Lesson short title",
        ],
        "shortDesc" => [
          "type" => ["non_null" => "String"],
          "description" => "Lesson short description",
        ],
        "isSample" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Lesson sample status",
        ],
        "duration" => [
          "type" => ["non_null" => "CourseDuration"],
          "description" => "Lesson duration",
        ],
        "poster" => [
          "type" => "Image",
          "description" => "Lesson poster",
        ],
        "video" => [
          "type" => "Video",
          "description" => "Lesson video",
        ],
      ],
    ]);

    register_graphql_object_type("LessonPage", [
      "description" => "Lesson page attributes",
      "fields" => [
        "lesson" => [
          "type" => ["non_null" => "Lesson"],
          "description" => "Lesson attributes",
        ],
        "course" => [
          "type" => ["non_null" => "CourseDetails"],
          "description" => "Course details",
        ],
        "next" => [
          "type" => "Integer",
          "description" => "Next lesson",
        ],
        "prev" => [
          "type" => "Integer",
          "description" => "Prev lesson",
        ],
      ],
    ]);

    register_graphql_object_type("LessonPath", [
      "description" => "Lesson path attributes",
      "fields" => [
        "course" => [
          "type" => ["non_null" => "String"],
          "description" => "Lesson course slug",
        ],
        "lessons" => [
          "type" => ["list_of" => "Integer"],
          "description" => "Lesson Ids",
        ],
      ],
    ]);
  }
}
