<?php
namespace MasterKelas\Admin\Sections;

/**
 * Core options section
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class CoreOptions
{ 
	public static function set($opt_name) {
		\Redux::set_section($opt_name, [
      'title'  => "تنظیمات عمومی",
      'id'     => 'core',
      // 'desc'   => "",
      'icon'   => 'el el-home',
      'fields' => self::fields()
    ]);
	}

  public static function fields() {
    return [
      [
        "id" => "front-url",
        "type" => "text",
        "title" => "آدرس فرانت اند",
        "desc" => "آدرس را با http یا https وارد نمائید.",
        "subtitle" => "",
        "validate" => [
          "url", "not_empty"
        ]
      ]
    ];
  }
}
