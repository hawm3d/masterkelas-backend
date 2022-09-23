<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\MasterException;
use MasterKelas\Model\Course;
use MasterKelas\Model\Page;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Page head query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class PageHead {
  public static function register() {
    self::page_head();
  }

  public static function page_head() {
    Schema::query(
      'RootQuery',
      "pageHead",
      [
        "type" => "MetaTags",
        "description" => "Meta tags of each route and page",
        "args" => [
          "context" => [
            "type" => "String",
            "description" => "Page context"
          ],
          "route" => [
            "type" => "String",
            "description" => "Page route"
          ],
        ],
        "resolve" => function ($_, $args, MasterContext $_context) {
          $context = sanitize_text_field($args['context'] ?? 'page');
          $route = sanitize_text_field($args['route'] ?? 'index');
          $available_contexts = ['class', 'page', 'category', 'lesson'];
          $pages = ['index', 'login', 'register'];
          $meta_tags = [];

          try {
            if (
              !in_array($context, $available_contexts)
              || empty($route)
              || ($context === 'page' && !in_array($route, $pages))
            )
              throw new MasterException("invalid.args");

            switch ($context) {
              case 'page':
                $meta_tags = Page::get_head($route);
                break;
              case 'class':
                $meta_tags = Course::get_head($route);
                break;
              case 'lesson':
                $meta_tags = Course::get_lesson_head($route);
                break;
              case 'category':
                $meta_tags = Course::get_category_head($route);
                break;
            }
          } catch (\Throwable $th) {
            //throw $th;
          }

          if (!$meta_tags || !isset($meta_tags['title']) || empty($meta_tags['title']))
            $meta_tags = Page::get_index_head();

          return [
            "title" => $meta_tags["title"] ?: "",
            "description" => $meta_tags["description"] ?: "",
          ];
        }
      ]
    );
  }
}
