<?php

namespace MasterKelas\Model;

use MasterKelas\RankMathPaper;

/**
 * MasterKelas Page
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Page {
  public static function get_head($page = null) {
    switch ($page) {
      case 'index':
      default:
        return self::get_index_head();
        break;
    }
  }

  public static function get_index_head() {
    $front_id = get_option('page_on_front', 0);
    if (!$front_id)
      throw new \Exception("invalid.front.page.id");

    $page = get_post($front_id);
    if (!$page || is_wp_error($page))
      throw new \Exception("invalid.front.page");

    rank_math()->variables->setup();
    $paper = RankMathPaper::get($page, [
      "Singular" => true
    ]);

    return [
      "title" => $paper->get_title() ?: null,
      "description" => $paper->get_description() ?: null,
    ];
  }
}
