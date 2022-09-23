<?php

namespace MasterKelas\Admin;

/**
 * Config LearnDash 
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class LearnDash {

  public static function init() {
    // Disable update
    add_filter('learndash_updates_enabled', function ($enabled) {
      return $enabled;
    });

    \MasterKelas\Admin\LearnDash\LearnDashTabs::register();
  }
}
