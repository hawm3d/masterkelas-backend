<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              masterkelas.com
 * @since             1.0.0
 * @package           Mk
 *
 * @wordpress-plugin
 * Plugin Name:       MasterKelas
 * Plugin URI:        masterkelas.com
 * Description:       هسته مسترکلاس
 * Version:           1.0.0
 * Author:            Hamed Ataei
 * Author URI:        masterkelas.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mk
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!class_exists('\MasterKelas')) :
	final class MasterKelas {
		/**
		 * Stores the instance of the MasterKelas class
		 *
		 * @var MasterKelas The one true MasterKelas
		 * @since  1.0.0
		 * @access private
		 */
		private static $instance;

		/**
		 * The instance of the MasterKelas object
		 *
		 * @return object|MasterKelas - The one true MasterKelas
		 * @since  1.0.0
		 * @access public
		 */
		public static function instance() {
			if (!isset(self::$instance) && !(self::$instance instanceof MasterKelas)) {
				self::$instance = new MasterKelas;
				self::$instance->setup();
				self::$instance->includes();
			}

			self::$instance->run();

			/**
			 * Return the Init_MasterKelas Instance
			 */
			return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function setup() {
			// Plugin version.
			if (!defined('MASTERKELAS_VERSION')) {
				define('MASTERKELAS_VERSION', '1.0.0');
			}

			// Admin Panel version.
			if (!defined('MASTERKELAS_ADMIN_VERSION')) {
				define('MASTERKELAS_ADMIN_VERSION', '1.0.0');
			}

			// Plugin Folder Path.
			if (!defined('MASTERKELAS_PLUGIN_DIR')) {
				define('MASTERKELAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}

			// Plugin Folder URL.
			if (!defined('MASTERKELAS_PLUGIN_URL')) {
				define('MASTERKELAS_PLUGIN_URL', plugin_dir_url(__FILE__));
			}

			// Plugin Root File.
			if (!defined('MASTERKELAS_PLUGIN_FILE')) {
				define('MASTERKELAS_PLUGIN_FILE', __FILE__);
			}

			// Whether to autoload the files or not.
			if (!defined('MASTERKELAS_AUTOLOAD')) {
				define('MASTERKELAS_AUTOLOAD', true);
			}

			// JWT auth secret
			if (!defined('MASTERKELAS_AES_128_CBC_KEY')) {
				define('MASTERKELAS_AES_128_CBC_KEY', 'w?%~?#[(??Kna???');
			}

			// Web App Id
			if (!defined('MASTERKELAS_WEB_APP_ID')) {
				define('MASTERKELAS_WEB_APP_ID', '62cb2a6d033a0c47e499faf5');
			}
		}

		/**
		 * Include required files.
		 * Uses composer's autoload
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function includes() {
			// Autoload Required Classes.
			if (defined('MASTERKELAS_AUTOLOAD') && true === MASTERKELAS_AUTOLOAD) {
				require_once(MASTERKELAS_PLUGIN_DIR . 'vendor/autoload.php');
			}
		}

		/**
		 * Initialize the plugin
		 */
		private static function run() {
			\MasterKelas\Queue\Scheduler::hooks();
			\MasterKelas\Admin::init();
			\MasterKelas\DB::install();
			\MasterKelas\GraphQL::hooks();
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single object
		 * therefore, we don't want the object to be cloned.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong(__FUNCTION__, 'MasterKelas should not be cloned.', '1.0.0');
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// De-serializing instances of the class is forbidden.
			_doing_it_wrong(__FUNCTION__, 'De-serializing instances of MasterKelas is not allowed', '1.0.0');
		}
	}
endif;

/**
 * Load Action Scheduler.
 */
function load_as() {
	require_once(plugin_dir_path(__FILE__) . 'vendor/woocommerce/action-scheduler/action-scheduler.php');
}
add_action('plugins_loaded', 'load_as', -10);

/**
 * Start MasterKelas.
 */
function init() {
	$required_plugins = [];

	if (!class_exists('\WPGraphQL')) {
		$required_plugins[] = 'WPGraphQL';
	}

	if (!class_exists('\Woocommerce')) {
		$required_plugins[] = 'Woocommerce';
	}

	if (!empty($required_plugins)) {
		add_action(
			'admin_notices',
			function () use ($required_plugins) {
?>
			<div class="error notice">
				<p>
					به منظور استفاده از افزونه مسترکلاس،
					لطفا <?php echo implode(" ،", $required_plugins) ?> را نصب کنید.
				</p>
			</div>
<?php
			}
		);

		return;
	}

	return MasterKelas::instance();
}
add_action('plugins_loaded', 'init', 1);

// /**
//  * The code that runs during plugin activation.
//  * This action is documented in src/includes/class-mk-activator.php
//  */
// function activate_mk() {
// 	\MasterKelas\MasterKelasActivator::activate();
// }

// /**
//  * The code that runs during plugin deactivation.
//  * This action is documented in src/includes/class-mk-deactivator.php
//  */
// function deactivate_mk() {
// 	\MasterKelas\MasterKelasDeactivator::deactivate();
// }

// register_activation_hook(__FILE__, 'activate_mk');
// register_deactivation_hook(__FILE__, 'deactivate_mk');
