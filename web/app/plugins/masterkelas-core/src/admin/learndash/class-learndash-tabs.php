<?php

namespace MasterKelas\Admin\LearnDash;

/**
 * LearnDash Admin Tabs 
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class LearnDashTabs {
  public static function register() {
    add_filter('learndash_header_tab_menu', [__CLASS__, 'tabs'], 10, 3);

    if (!class_exists('LearnDashCourseDetails'))
      require_once dirname(__FILE__) . '/metabox/class-learndash-course-details.php';

    if (!class_exists('LearnDashCourseGallery'))
      require_once dirname(__FILE__) . '/metabox/class-learndash-course-gallery.php';

    if (!class_exists('LearnDashCourseBook'))
      require_once dirname(__FILE__) . '/metabox/class-learndash-course-book.php';

    if (!class_exists('LearnDashLessonDetails'))
      require_once dirname(__FILE__) . '/metabox/class-learndash-lesson-details.php';
  }

  public static function tabs($header_tabs_data, $menu_tab_key, $screen_post_type) {
    global $pagenow;

    if ($pagenow === 'post-new.php' || isset($_GET['post']) && intval($_GET['post']) > 0)
      switch ($screen_post_type) {
        case 'sfwd-courses':
          $header_tabs_data[] = [
            'id'                  => $screen_post_type . '-details',
            'name'                => 'مشخصات کلاس',
            'metaboxes'           => ['sfwd-courses', 'masterkelas-course-details'],
            'showDocumentSidebar' => 'false',
          ];

          $header_tabs_data[] = [
            'id'                  => $screen_post_type . '-gallery',
            'name'                => 'گالری کلاس',
            'metaboxes'           => ['sfwd-courses', 'masterkelas-course-gallery'],
            'showDocumentSidebar' => 'false',
          ];

          $header_tabs_data[] = [
            'id'                  => $screen_post_type . '-book',
            'name'                => 'کتاب کار کلاس',
            'metaboxes'           => ['sfwd-courses', 'masterkelas-course-book'],
            'showDocumentSidebar' => 'false',
          ];
          break;

        case 'sfwd-lessons':
          $header_tabs_data[] = [
            'id'                  => $screen_post_type . '-details',
            'name'                => 'مشخصات درس',
            'metaboxes'           => ['sfwd-lessons', 'masterkelas-lesson-details'],
            'showDocumentSidebar' => 'false',
          ];
          break;
      }

    return $header_tabs_data;
  }
}
