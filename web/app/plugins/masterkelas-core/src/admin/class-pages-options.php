<?php

namespace MasterKelas\Admin;

/**
 * Pages options section
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class PagesOptions {
  public static function set($opt_name) {
    \Redux::set_section($opt_name, [
      'title'  => "تنظیمات صفحات",
      'id'     => 'pages',
      // 'desc'   => "",
      'icon'   => 'fa-regular fa-page',
      'fields' => self::pages_fields()
    ]);
  }

  public static function pages_fields() {
    return [
      [
        "id" => "subscriptions",
        "type" => "select",
        "multi" => true,
        "data" => "posts",
        "args" => [
          "post_type" => ["product"], 'numberposts' => -1
        ],
        // 'ajax' => true,
        "title" => "اشتراک ها",
        "desc" => "لطفا حداقل یک اشتراک انتخاب کنید.",
        // "validate" => [
        //   "url", "not_empty"
        // ],
      ],
      [
        "id" => "policy-page",
        "type" => "select",
        "data" => "pages",
        // 'ajax' => true,
        "title" => "قوانین، ضوابط و شرایط",
        "desc" => "لطفا صفحه مربوط به قوانین و سیاست های عمومی سایت را انتخاب کنید.",
        // "validate" => [
        //   "url", "not_empty"
        // ],
      ]
    ];
  }
}
