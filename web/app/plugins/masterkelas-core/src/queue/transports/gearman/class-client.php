<?php

namespace MasterKelas\Queue\Gearman;

use MasterKelas\MasterLog;
use \MasterKelas\Queue\Client as BaseClient;

/**
 * The Gearman Client
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Client extends BaseClient {

  /**
   * @var GearmanClient The libGearman Client instance
   */
  public $gearman_client;

  /**
   * @var array The list of Gearman Servers to connect to.
   */
  public $gearman_servers;

  /**
   * Creates a new libGearman Client instances and configures the
   * servers that it should connect to.
   *
   * @return bool True or false if successful
   */
  public function register() {
    $client = $this->get_gearman_client();

    if ($client !== false) {
      $servers = $this->get_servers();

      try {
        if (empty($servers)) {
          return $client->addServer();
        } else {
          return $client->addServers(implode(',', $servers));
        }
      } catch (\GearmanException $e) {
        $servers = implode(',', $servers);

        if (!defined('PHPUNIT_RUNNER')) {
          MasterLog::queue()->error("Fatal Gearman Error: Failed to register servers ($servers)");
          MasterLog::queue()->error("  Cause: " . $e->getMessage());
        }

        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Adds a Job to the libGearman Client's Queue.
   *
   * @param string $hook The action hook name for the job
   * @param array $args Optional arguments for the job
   * @param string $priority Optional priority of the job
   * @return bool true or false depending on the Client
   */
  public function add($hook, $args = array(), $priority = 'normal') {
    $job_data = array(
      'hook'    => $hook,
      'args'    => $args,
      'blog_id' => $this->get_blog_id(),
    );

    $client = $this->get_gearman_client();

    if ($client !== false) {
      $payload  = json_encode($job_data);
      $method   = $this->get_background_method($priority);
      $group    = $this->get_async_group();
      $callable = array($client, $method);

      return call_user_func($callable, $group, $payload);
    } else {
      return false;
    }
  }

  /* Helpers */
  /**
   * Returns the libGearman function to use based on the specified
   * priority.
   *
   * @param string $priority low, normal or high
   * @return string The corresponding method name
   */
  function get_background_method($priority) {
    switch (strtolower($priority)) {
      case 'high':
        $method = 'doHighBackground';
        break;

      case 'low':
        $method = 'doLowBackground';
        break;

      case 'normal':
      default:
        $method = 'doBackground';
        break;
    }

    return $method;
  }

  /**
   * The Function Group used to split libGearman functions on a
   * multi-network install.
   *
   * @return string The prefixed group name
   */
  function get_async_group() {
    $key = 'MK';

    if (defined('MASTER_QUEUE_GROUP')) {
      $key = MASTER_QUEUE_GROUP;
    }

    return $key;
  }

  /**
   * The Gearman Servers to connect to as defined in wp-config.php.
   *
   * If absent the default server will be used.
   *
   * @return array The list of servers for this Worker.
   */
  function get_servers() {
    if (is_null($this->gearman_servers)) {
      global $gearman_servers;

      if (!empty($gearman_servers)) {
        $this->gearman_servers = $gearman_servers;
      } else {
        $this->gearman_servers = array();
      }
    }

    return $this->gearman_servers;
  }

  /**
   * Builds the libGearman Client Instance if the extension is
   * installed. Once created returns the previous instance without
   * reinitialization.
   *
   * @return GearmanClient|false An instance of GearmanClient
   */
  function get_gearman_client() {
    if (is_null($this->gearman_client)) {
      if (class_exists('\GearmanClient')) {
        $this->gearman_client = new \GearmanClient();
      } else {
        $this->gearman_client = false;
      }
    }

    return $this->gearman_client;
  }

  /**
   * Caches and returns the current blog id for adding to the Job meta
   * data. False if not a multisite install.
   *
   * @return int|false The current blog ids id.
   */
  function get_blog_id() {
    return function_exists('is_multisite') && is_multisite() ? get_current_blog_id() : false;
  }
}
