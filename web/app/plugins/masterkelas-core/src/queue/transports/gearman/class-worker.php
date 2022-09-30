<?php

namespace MasterKelas\Queue\Gearman;

use MasterKelas\MasterLog;
use \MasterKelas\Queue\Worker as BaseWorker;

/**
 * The Gearman Worker
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Worker extends BaseWorker {

  /**
   * @var GearmanWorker The libGearman Worker instance
   */
  public $gearman_worker;

  /**
   * @var array The list of Gearman servers.
   */
  public $gearman_servers;

  /**
   * Creates a Gearman Worker and initializes the servers it should
   * connect to. The callback that will execute a job's hook is also setup here.
   *
   * @return bool True if operation was successful else false.
   */
  public function register() {
    $worker = $this->get_gearman_worker();

    if ($worker !== false) {
      $servers  = $this->get_servers();
      $group    = $this->get_async_group();
      $callable = array($this, 'do_job');

      try {
        if (empty($servers)) {
          $worker->addServer();
        } else {
          $worker->addServers(implode(',', $servers));
        }

        return $worker->addFunction($group, $callable);
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
   * Pulls a job from the Gearman Queue and tries to execute it.
   * Errors are logged if the Job failed to execute.
   *
   * @return bool True if the job could be executed, else false
   */
  public function work() {
    $worker = $this->get_gearman_worker();

    try {
      $result = $worker->work();
    } catch (\Exception $e) {
      if (!defined('PHPUNIT_RUNNER')) {
        MasterLog::queue()->error('GearmanWorker->work failed: ' . $e->getMessage());
      }
      $result = false;
    }

    do_action('wp_async_task_after_work', $result, $this);

    return $result;
  }

  /* Helpers */
  /**
   * Executes a Job pulled from Gearman. On a multisite instance
   * it switches to the target site before executing the job. And the
   * site is restored once executing is finished.
   *
   * The job data contains,
   *
   * 1. hook - The name of the target hook to execute
   * 2. args - Optional arguments to pass to the target hook
   * 3. blog_id - Optional blog on a multisite to switch to, before execution
   *
   * Actions are fired before and after execution of the target hook.
   *
   * Eg:- for the action 'foo' The order of execution of actions is,
   *
   * 1. wp_async_task_before_job
   * 2. wp_async_task_before_job_foo
   * 3. foo
   * 4. wp_async_task_after_job
   * 5. wp_async_task_after_job_foo
   *
   * @param array $job The job object data.
   * @return bool True or false based on the status of execution
   */
  function do_job($job) {
    $switched = false;

    try {
      $job_data = json_decode($job->workload(), true);
      $hook     = $job_data['hook'];
      $args     = $job_data['args'];

      if (function_exists('is_multisite') && is_multisite() && $job_data['blog_id']) {
        $blog_id = $job_data['blog_id'];

        if (get_current_blog_id() !== $blog_id) {
          switch_to_blog($blog_id);
          $switched = true;
        } else {
          $switched = false;
        }
      } else {
        $switched = false;
      }

      do_action('wp_async_task_before_job', $hook, $job);
      do_action('wp_async_task_before_job_' . $hook, $job);

      do_action($hook, $args, $job);

      do_action('wp_async_task_after_job', $hook, $job);
      do_action('wp_async_task_after_job_' . $hook, $job);

      $result = true;
    } catch (\Exception $e) {
      MasterLog::queue()->error('GearmanWorker->do_job failed: ' . $e->getMessage());
      $result = false;
    }

    if ($switched) {
      restore_current_blog();
    }

    return $result;
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
   * Builds the libGearman Worker Instance if the extension is
   * installed. Once created returns the previous instance without
   * reinitialization.
   *
   * @return GearmanWorker|false An instance of GearmanWorker
   */
  function get_gearman_worker() {
    if (is_null($this->gearman_worker)) {
      if (class_exists('\GearmanWorker')) {
        $this->gearman_worker = new \GearmanWorker();
      } else {
        $this->gearman_worker = false;
      }
    }

    return $this->gearman_worker;
  }
}
