<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\OptimusId;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Course Categories query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Categories {
  public static function register() {
    self::categories();
  }

  public static function categories() {
    Schema::query(
      'RootQuery',
      "courseCategories",
      [
        "type" => ["list_of" => "CourseCategory"],
        "description" => "Course Categories",
        "resolve" => function ($_, $__, MasterContext $context) {
          $categories = [];
          $terms = get_terms([
            'taxonomy' => 'ld_course_category',
            'hide_empty' => false,
            'orderby' => 'count',
            'order' => 'DESC'
          ]);

          if (!is_wp_error($terms) && !empty($terms))
            foreach ($terms as $term)
              if ($term->term_id > 0 && !empty($term->name) && $term->slug !== 'uncategorized')
                $categories[] = [
                  "id" => (int) OptimusId::course_category()->encode($term->term_id),
                  "name" => (string) $term->name,
                  "slug" => (string) $term->slug,
                  "icon" => null,
                  "image" => null,
                  "description" => (string) $term->description,
                ];

          return $categories;
        }
      ],
    );
  }
}
