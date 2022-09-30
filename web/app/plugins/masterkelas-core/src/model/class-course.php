<?php

namespace MasterKelas\Model;

use MasterKelas\OptimusId;
use MasterKelas\RankMathPaper;
use MasterKelas\Schema\MasterContext;

/**
 * MasterKelas Course
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Course {
  public static function get_head($course_slug) {
    $course = self::get_course_by_slug($course_slug);

    rank_math()->variables->setup();
    $paper = RankMathPaper::get($course, [
      "Singular" => true
    ]);

    return [
      "title" => $paper->get_title() ?: null,
      "description" => $paper->get_description() ?: null,
    ];
  }

  public static function get_lesson_head($lesson_id) {
    $lesson = self::get_lesson_by_id((int) $lesson_id);

    rank_math()->variables->setup();
    $paper = RankMathPaper::get($lesson, [
      "Singular" => true
    ]);

    return [
      "title" => $paper->get_title() ?: null,
      "description" => $paper->get_description() ?: null,
    ];
  }

  public static function get_category_head($category_slug) {
    $category = null;
    $term = get_term_by('slug', sanitize_text_field($category_slug), 'ld_course_category');
    if ($term && !is_wp_error($term) && $term->term_id > 0 && !empty($term->name) && $term->slug !== 'uncategorized')
      $category = $term;

    if (!$category) throw new \Exception();

    rank_math()->variables->setup();
    $paper = RankMathPaper::get($category, [
      "Singular" => true,
    ]);

    return [
      "title" => $paper->get_title() ?: null,
      "description" => $paper->get_description() ?: null,
    ];
  }

  public static function get_course_by_slug($course_slug) {
    $post_type = learndash_get_post_type_slug('course');
    $slug = sanitize_text_field($course_slug);
    if (empty($slug))
      throw new \Exception("invalid.slug");

    $course = get_posts([
      'name'        => $slug,
      'post_type'   => $post_type,
      'post_status' => 'publish',
      'numberposts' => 1
    ]);

    if (!$course || !$course[0]->ID)
      throw new \Exception("course.not.found");

    return $course[0];
  }

  public static function get_course_by_id($course_id, $decode_id = true) {
    $post_type = learndash_get_post_type_slug('course');
    $id = (int) sanitize_text_field($course_id);
    if ($decode_id)
      $id = OptimusId::course()->decode($id);

    if (empty($id) || $id <= 0)
      throw new \Exception("invalid.id");

    $course = get_posts([
      'p' => $id,
      'post_type' => $post_type,
      'post_status' => 'publish',
      'numberposts' => 1
    ]);

    if (!$course || !$course[0]->ID)
      throw new \Exception("course.not.found");

    return $course[0];
  }

  public static function get_lesson_by_id($lesson_id) {
    $post_type = learndash_get_post_type_slug('lesson');
    $id = OptimusId::course_lesson()->decode((int) sanitize_text_field($lesson_id));
    if (empty($id) || $id <= 0)
      throw new \Exception("invalid.id");

    $lesson = get_posts([
      'p' => $id,
      'post_type' => $post_type,
      'post_status' => 'publish',
      'numberposts' => 1
    ]);

    if (!$lesson || !$lesson[0]->ID)
      throw new \Exception("lesson.not.found");

    return $lesson[0];
  }

  public static function create_query_args($args = [], MasterContext $context) {
    $query = [
      "post_type" => "sfwd-courses",
      "post_status" => "publish",
    ];
    $tax_query = [];
    $meta_query = [];
    $MIN_SEARCH_LENGTH = 3;
    $DEFAULT_SORT = "latest";
    $MAX_PER_PAGE = 12;
    $MIN_PER_PAGE = 1;

    $defaults = [
      "term" => "",
      "category" => [],
      "tags" => [],
      "page" => 1,
      "perPage" => $MAX_PER_PAGE,
      "exclude" => "",
      "sort" => $DEFAULT_SORT,
      "student" => [],
    ];

    $args = wp_parse_args($args, $defaults);
    $search_keyword = sanitize_text_field($args['term']);
    $category_ids = [];
    $tags_ids = [];
    $exclude_ids = [];
    $exclude_collection = !empty($args['exclude']) ? explode(",", sanitize_text_field($args['exclude'])) : null;
    $page = (int) $args['page'];
    $per_page = (int) $args['perPage'];
    $sorts = ["latest", "trending", "popular"];
    $sort = in_array($args['sort'], $sorts) ? sanitize_text_field($args['sort']) : $DEFAULT_SORT;
    $student_activities = ["completed", "started", "in-progress", "related"];

    if (!empty($search_keyword) && strlen($search_keyword) >= $MIN_SEARCH_LENGTH) {
      $query['s'] = $search_keyword;
    }

    if (!empty($args['category']))
      foreach ($args['category'] as $cat) {
        if (!is_numeric($cat)) {
          $term = get_term_by('slug', sanitize_text_field($cat), 'ld_course_category');
          if ($term && !is_wp_error($term) && $term->term_id > 0 && !empty($term->name) && $term->slug !== 'uncategorized')
            $category_ids[] = $term->term_id;
        } elseif ((int) $cat > 0) {
          $cat = (int) OptimusId::course_category()->decode((int) $cat);
          if ($cat > 0)
            $category_ids[] = $cat;
        }
      }

    if (!empty($args['tags']))
      foreach ($args['tags'] as $tag_id) {
        if ((int) $tag_id > 0) {
          $tag_id = (int) OptimusId::course_tag()->decode((int) $tag_id);
          if ($tag_id > 0)
            $tags_ids[] = $tag_id;
        }
      }

    if (is_array($exclude_collection) && !empty($exclude_collection))
      foreach ($exclude_collection as $ex_id) {
        if ((int) $ex_id > 0 && count($exclude_ids) < $MAX_PER_PAGE) {
          $ex_id = (int) OptimusId::course()->decode((int) $ex_id);
          if ($ex_id > 0)
            $exclude_ids[] = $ex_id;
        }
      }

    if (!empty($category_ids))
      $tax_query[] = [
        'taxonomy' => "ld_course_category",
        'field' => 'term_id',
        'terms' => $category_ids,
        'include_children' => false,
      ];


    if (!empty($tags_id))
      $tax_query[] = [
        'taxonomy' => "ld_course_tag",
        'field' => 'term_id',
        'terms' => $tags_id,
        'include_children' => false,
      ];


    if (!empty($exclude_ids)) {
      $query['post__not_in'] = $exclude_ids;
    }

    if ($page < 1)
      $page = 1;

    if ($per_page > $MAX_PER_PAGE)
      $per_page = $MAX_PER_PAGE;

    if ($per_page < $MIN_PER_PAGE)
      $per_page = $MIN_PER_PAGE;

    $query['paged'] = $page;
    $query['posts_per_page'] = $per_page;

    if (!empty($tax_query))
      $query['tax_query'] = $tax_query;

    switch ($sort) {
      case 'trending':
        $query['order'] = 'ASC';
        $query['orderby'] = 'date';
        break;
      case 'popular':
        $query['order'] = 'DESC';
        $query['orderby'] = 'title';
        break;
      case 'latest':
      default:
        $query['order'] = 'DESC';
        $query['orderby'] = 'date';
        break;
    }

    graphql_debug($query);

    return $query;
  }

  public static function get_course_details($course) {
    $category = null;
    $terms = get_the_terms($course, 'ld_course_category');
    if (!is_wp_error($terms) && is_array($terms) && !empty($terms))
      foreach ($terms as $term)
        $category = [
          "id" => (int) OptimusId::course_category()->encode($term->term_id),
          "name" => (string) $term->name,
          "slug" => (string) $term->slug,
          "icon" => null,
          "image" => null,
          "description" => (string) $term->description,
        ];

    return [
      "id" => (int) OptimusId::course()->encode($course->ID),
      "title" => $course->post_title,
      "slug" => $course->post_name,
      "category" => $category,
      "shortTitle" => learndash_get_setting($course->ID, "mk_short_title"),
      "shortDesc" => learndash_get_setting($course->ID, "mk_short_desc"),
      "restricted" => learndash_get_setting($course->ID, "mk_restricted") === 'on',
      "masterNameFA" => learndash_get_setting($course->ID, "mk_master_name_fa"),
      "masterNameEN" => learndash_get_setting($course->ID, "mk_master_name_en"),
      "duration" => self::time_to_duration(learndash_get_setting($course->ID, "mk_duration")),
      "totalLessons" => learndash_get_course_steps_count($course->ID),
      "portraitImage" => self::get_image_from_settings($course->ID, "mk_portrait_img"),
      "typographyImage" => self::get_image_from_settings($course->ID, "mk_typography_img"),
    ];
  }

  public static function get_course_student($course, $user = null) {
    $subscribed = false;
    if ($user) {
      if (wcs_user_has_subscription($user->id, '', 'active')) {
        $subscriptions = wcs_get_users_subscriptions($user->id);
        foreach ($subscriptions as $sub) {
          $_subscription = wcs_get_subscription($sub->ID);
          if ($_subscription->get_status() === 'active') {
            $subscribed = true;
          }
        }
      }
    }

    return [
      "hasAccess" => (bool) ($user && ($subscribed || sfwd_lms_has_access($course->ID, $user->id))),
      "progress" => 0,
      "completed" => (bool) ($user && learndash_course_completed($user->id, $course->ID)),
    ];
  }

  public static function get_course_card($course, $user = null) {
    return [
      "details" => self::get_course_details($course),
      "student" => self::get_course_student($course, $user),
    ];
  }

  public static function get_course($course, $user = null) {
    $student = self::get_course_student($course, $user);
    $lessons = self::get_lessons([
      "course" => $course,
      "student" => $student['hasAccess']
    ]);

    return [
      "details" => self::get_course_details($course),
      "student" => $student,
      "description" => apply_filters('the_content', $course->post_content),
      "lessons" => $lessons,
      "teaser" => [
        "url" => learndash_get_setting($course->ID, "mk_trailer_url"),
        "poster" => self::get_image_from_settings($course->ID, "mk_trailer_poster")
      ],
      "cinematicImage" => self::get_image_from_settings($course->ID, "mk_cinematic_img"),
      "landscapeImage" => self::get_image_from_settings($course->ID, "mk_landscape_img"),
    ];
  }

  public static function get_lesson($lessonData, $user = null) {
    $lesson = null;
    $next = null;
    $lesson = null;
    $course = self::get_course_by_id(
      learndash_get_course_id($lessonData),
      false
    );
    $student = self::get_course_student($course, $user);
    $lessons = self::get_lessons([
      "course" => $course,
      "student" => $student['hasAccess'],
      "extra_fields" => ["description", "slug", "video", "status"]
    ]);

    foreach ($lessons as $_lesson) {
      if ($_lesson['dbId'] === $lessonData->ID)
        $lesson = $_lesson;
    }

    if (!$lesson) throw new \Exception("lesson.and.course.not.matched");

    foreach ($lessons as $_lesson) {
      if ($_lesson['order'] === $lesson['order'] - 1)
        $prev = (int) $_lesson['id'];

      if ($_lesson['order'] === $lesson['order'] + 1)
        $next = (int) $_lesson['id'];
    }

    return [
      "lesson" => $lesson,
      "next" => $next,
      "prev" => $prev,
      "course" => self::get_course_details($course),
      "student" => $student,
    ];
  }

  public static function get_lessons($args = []) {
    $defaults = [
      "course" => null,
      "student" => false,
      // "description", "slug", "video", "status"
      "extra_fields" => []
    ];

    $args = wp_parse_args($args, $defaults);
    $lessons = [];

    if (!$args['course'])
      return $lessons;

    $course_lessons = learndash_get_course_lessons_list($args['course'], "");
    foreach ($course_lessons as $lesson) {
      $short_title = learndash_get_setting($lesson['id'], "mk_short_title");
      $short_desc = learndash_get_setting($lesson['id'], "mk_short_desc");
      $lesson_data = [
        "dbId" => (int) $lesson['id'],
        "id" => (int) OptimusId::course_lesson()->encode($lesson['id']),
        "order" => $lesson['sno'],
        "title" => $lesson['post']->post_title,
        "shortDesc" => empty($short_desc) ? wp_trim_words($lesson['post']->post_content, 63, "...") : $short_desc,
        "isSample" => $lesson['sample'] === 'is_sample',
        "duration" => self::time_to_duration(learndash_get_setting($lesson['id'], "mk_duration")),
        "poster" => self::get_image_from_settings($lesson['id'], "mk_lesson_poster", false),
      ];

      if (!empty($short_title))
        $lesson_data['shortTitle'] = $short_title;

      if (in_array("slug", $args['extra_fields']))
        $lesson_data['slug'] = $lesson['post']->post_name;

      if (in_array("status", $args['extra_fields']))
        $lesson_data['status'] = $lesson['status'];

      if (in_array("description", $args['extra_fields']))
        $lesson_data['description'] = apply_filters('the_content', $lesson['post']->post_content);

      if (in_array("video", $args['extra_fields']) && ($args['student'] || $lesson_data['isSample']))
        $lesson_data['video'] = [
          "url" => learndash_get_setting($lesson['id'], "lesson_video_url"),
          "poster" => $lesson_data['poster']
        ];

      $lessons[] = $lesson_data;
    }

    return $lessons;
  }

  public static function paths() {
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

  public static function get_image_from_settings($id, $setting_name, $restrict = true) {
    return self::get_image_from_attachment(
      (int) learndash_get_setting($id, $setting_name),
      $restrict
    );
  }

  public static function get_image_from_attachment(int $attachment_id, $restrict = true) {
    $img = [
      "blur" => "",
      "title" => "",
      "alt" => "",
      "width" => 0,
      "height" => 0,
      "url" => "",
    ];

    if ($attachment_id > 0) {
      $image_attributes = wp_get_attachment_image_src($attachment_id, 'full');
      if ($image_attributes && isset($image_attributes[0], $image_attributes[1], $image_attributes[2])) {
        $img['url'] = $image_attributes[0];
        $img['width'] = $image_attributes[1];
        $img['height'] = $image_attributes[2];
        $img['alt'] = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $img['title'] = get_the_title($attachment_id);
      }
    }

    if (!$restrict && (empty($img['url']) || !$img['width'] || !$img['height']))
      return null;

    return $img;
  }

  public static function time_to_duration($time) {
    $response = [
      "hour" => "00",
      "minute" => "00",
      "second" => "00"
    ];

    if ($time > 0) {
      $time = learndash_convert_lesson_time_time($time);
      $response['hour'] = gmdate('H', $time);
      $response['minute'] = gmdate('i', $time);
      $response['second'] = gmdate('s', $time);
    }

    return $response;
  }
}
