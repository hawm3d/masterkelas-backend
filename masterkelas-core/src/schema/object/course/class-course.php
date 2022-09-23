<?php

namespace MasterKelas\Schema\Object;

/**
 * Course object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Course {
  public static function register() {
    self::course_type();
  }

  public static function course_type() {
    register_graphql_object_type("CourseDetails", [
      "description" => "Course basic attributes",
      "fields" => [
        "id" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Course ID",
        ],
        "title" => [
          "type" => ["non_null" => "String"],
          "description" => "Course title",
        ],
        "slug" => [
          "type" => ["non_null" => "String"],
          "description" => "Course short title",
        ],
        "shortTitle" => [
          "type" => ["non_null" => "String"],
          "description" => "Course short title",
        ],
        "shortDesc" => [
          "type" => ["non_null" => "String"],
          "description" => "Course short description",
        ],
        "category" => [
          "type" => "CourseCategory",
          "description" => "Course category",
        ],
        "restricted" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Course restriction status",
        ],
        "masterNameFA" => [
          "type" => ["non_null" => "String"],
          "description" => "Master Persian name",
        ],
        "masterNameEN" => [
          "type" => ["non_null" => "String"],
          "description" => "Master english master name",
        ],
        "duration" => [
          "type" => ["non_null" => "CourseDuration"],
          "description" => "Course duration",
        ],
        "totalLessons" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Number of lessons",
        ],
        "portraitImage" => [
          "type" => ["non_null" => "Image"],
          "description" => "Course portrait image",
        ],
        "typographyImage" => [
          "type" => ["non_null" => "Image"],
          "description" => "Course typography image",
        ],
      ],
    ]);

    register_graphql_object_type("CourseStudentData", [
      "description" => "Course student attributes",
      "fields" => [
        "hasAccess" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Student access status",
        ],
        "progress" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Student progress",
        ],
        "completed" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Course visibility status",
        ],
      ],
    ]);

    register_graphql_object_type("CourseCard", [
      "description" => "Course attributes",
      "fields" => [
        "details" => [
          "type" => ["non_null" => "CourseDetails"],
          "description" => "Course basic attributes",
        ],
        "student" => [
          "type" => ["non_null" => "CourseStudentData"],
          "description" => "Course student",
        ],
      ],
    ]);

    register_graphql_object_type("Course", [
      "description" => "Course attributes",
      "fields" => [
        "details" => [
          "type" => ["non_null" => "CourseDetails"],
          "description" => "Course basic attributes",
        ],
        "student" => [
          "type" => ["non_null" => "CourseStudentData"],
          "description" => "Course student",
        ],
        "description" => [
          "type" => ["non_null" => "String"],
          "description" => "Course description",
        ],
        "teaser" => [
          "type" => ["non_null" => "Video"],
          "description" => "Course teaser",
        ],
        "cinematicImage" => [
          "type" => ["non_null" => "Image"],
          "description" => "Course cinematic image",
        ],
        "landscapeImage" => [
          "type" => ["non_null" => "Image"],
          "description" => "Course landscape image",
        ],
        "lessons" => [
          "type" => ["list_of" => "Lesson"],
          "description" => "Course lessons list",
        ],
      ],
    ]);
  }
}
