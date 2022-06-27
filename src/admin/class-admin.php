<?php
namespace MasterKelas;

/**
 * The core admin class.
 *
 * Admin pages, options and forms
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Admin
{
	public static $opt_name = 'masterkelas';

	public static function setup() {
		if ( !class_exists( 'ReduxFramework' ) ) {
			require_once( dirname( __FILE__ ) . '/redux/framework.php' );
		}

		\Redux::disable_demo();

		\Redux::set_args(self::$opt_name, [
			'display_name' => "مسترکلاس",
			'display_version' => MASTERKELAS_VERSION,
			'menu_title' => "مسترکلاس",
			'customizer' => false,
			'update_notice' => false,
			'use_cdn' => false,
			'footer_credit' => " ",
		]);

		// Include sections
		\MasterKelas\Admin\Sections\CoreOptions::set(self::$opt_name);
	}
}
