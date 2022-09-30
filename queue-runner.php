<?php

/**
 * Master Queue Runner
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */

$master_queue_autoload_path = __DIR__ . '/web/app/plugins/masterkelas-core/vendor/autoload.php';
if (!file_exists(__DIR__ . '/web/app/plugins/masterkelas-core/vendor/autoload.php')) {
  return;
}

master_queue_load_autoload();

function master_queue_load_autoload() {
  global $masterkelas_core_autoloaded;

  if (!$masterkelas_core_autoloaded) {
    require_once(__DIR__ . '/web/app/plugins/masterkelas-core/vendor/autoload.php');
  }

  $masterkelas_core_autoloaded = true;
}

function master_queue_run() {
  master_queue_load_autoload();

  $queue = \MasterKelas\Queue\Queue::get_instance();
  $queue->enable();

  return $queue->run();
}

if (!defined('PHPUNIT_RUNNER')) {
  ignore_user_abort(true);

  if (!empty($_POST) || defined('DOING_AJAX') || defined('DOING_ASYNC')) {
    die();
  }

  define('DOING_ASYNC', true);

  if (!defined('ABSPATH')) {
    if (!file_exists(dirname($_SERVER["SCRIPT_FILENAME"]) . '/web/wp/wp-load.php')) {
      error_log(
        'Master Queue Fatal Error - Cannot find wp-load.php'
      );
    }

    require_once(dirname($_SERVER["SCRIPT_FILENAME"]) . '/web/wp/wp-load.php');
  }

  master_queue_run();
}
