<?php

namespace MasterKelas;

use MasterKelas\Model\WebApp;
use WP_Error;

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
class Admin {
	public static $opt_name = 'masterkelas';

	public static function init() {
		// Register Hooks
		self::hooks();

		// Config redux and include sections and fields
		self::setup_redux();
	}

	public static function hooks() {
		\MasterKelas\Admin\LearnDash::init();
		\MasterKelas\Admin\ConfigureUserFields::hooks();

		add_action("admin_enqueue_scripts", [__CLASS__, 'enqueue_scripts']);
		add_action("plugins_loaded", [__CLASS__, 'disable_rest_routes']);

		remove_action('rest_api_init', 'create_initial_rest_routes', 0);
		add_action("after_setup_theme", [__CLASS__, 'disable_json_api']);
		add_filter('action_scheduler_retention_period', [__CLASS__, 'adjust_action_scheduler_log_retention']);

		add_filter('post_type_link', [__CLASS__, 'post_type_link'], 10, 2);
		// add_action("save_post_product", [__CLASS__, 'save_post_product']);
	}

	// public static function save_post_product($post_ID, $post) {
	// 	if ($post->post_name === 'subscriptions') {
	// 		$webapp_url = WebApp::get_webapp_url();
	// 	}
	// 	error_log($post_ID);
	// }

	public static function post_type_link($link, \WP_Post $post) {
		$webapp_url = WebApp::get_webapp_url();

		if (!$webapp_url || empty($webapp_url)) return $link;

		if ($post->post_type === 'sfwd-courses') {
			return "{$webapp_url}/classes/{$post->post_name}";
		}

		if ($post->post_type === 'sfwd-lessons') {
			$id = OptimusId::course()->encode($post->ID);
			$course_slug = get_post_field("post_name", learndash_get_course_id($post->ID));
			return "{$webapp_url}/classes/{$course_slug}/{$id}";
		}

		return $link;
	}

	public static function adjust_action_scheduler_log_retention() {
		return 1 * DAY_IN_SECONDS;
	}

	public static function disable_rest_routes() {
		// remove_action('rest_api_init', array(\LearnDash_REST_API::get_instance(), 'rest_api_init'), 10);
		// remove_action('init', array(\WooCommerce::instance(), 'load_rest_api'));
	}

	public static function disable_json_api() {
		// Remove the REST API lines from the HTML Header
		remove_action('wp_head', 'rest_output_link_wp_head', 10);
		remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

		// Remove the REST API endpoint.
		remove_action('rest_api_init', 'wp_oembed_register_route');

		// Turn off oEmbed auto discovery.
		add_filter('embed_oembed_discover', '__return_false');

		// Don't filter oEmbed results.
		remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

		// Remove oEmbed discovery links.
		remove_action('wp_head', 'wp_oembed_add_discovery_links');

		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action('wp_head', 'wp_oembed_add_host_js');

		// Remove all embeds rewrite rules.
		// add_filter('rewrite_rules_array', 'disable_embeds_rewrites');


		add_filter('json_enabled', '__return_false');
		add_filter('json_jsonp_enabled', '__return_false');

		// add_filter('rest_enabled', '__return_false');
		add_filter('rest_authentication_errors', function () {
			if (is_user_logged_in())
				return true;

			return new WP_Error('REST_DISABLED');
		});
		add_filter('rest_jsonp_enabled', '__return_false');
	}

	public static function setup_redux() {
		if (!class_exists('ReduxFramework')) {
			require_once(dirname(__FILE__) . '/redux/framework.php');
		}

		\Redux::disable_demo();

		\Redux::set_args(self::$opt_name, [
			'display_name' => "مسترکلاس",
			'display_version' => MASTERKELAS_VERSION,
			'menu_title' => "مسترکلاس",
			'customizer' => false,
			'update_notice' => false,
			'use_cdn' => false,
			'hide_expand' => true,
			'footer_credit' => " ",
		]);

		// Include sections
		\MasterKelas\Admin\CoreOptions::set(self::$opt_name);
		\MasterKelas\Admin\PagesOptions::set(self::$opt_name);
		\MasterKelas\Admin\AuthOptions::set(self::$opt_name);
	}

	public static function get_option($id = '') {
		return \Redux::get_option(self::$opt_name, $id);
	}

	public static function enqueue_scripts() {
		wp_enqueue_style('masterkelas_admin_styles', plugins_url("/admin/assets/css/masterkelas.css", __FILE__), false, MASTERKELAS_ADMIN_VERSION);
	}
}
