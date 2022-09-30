<?php

namespace MasterKelas\Queue\RabbitMQ;

use MasterKelas\MasterLog;
use \MasterKelas\Queue\Worker as BaseWorker;

/**
 * The RabbitMQ Worker
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Worker extends BaseWorker {

  /**
   * Instance of Connection class
   *
   * @var Connection
   */
  public $connection = null;

  /**
   * Connect to host and channel
   */
  private function connect() {
    if (null !== $this->connection) {
      return $this->connection;
    }

    try {
      $this->connection = new Connection();
    } catch (\Exception $e) {
      return false;
    }

    return $this->connection;
  }

  public function register() {
    // Do nothing
  }

  public function work() {
    if (!$this->connect()) {
      return false;
    }

    $this->connection->get_channel()->basic_consume('wordpress', '', false, true, false, false, function ($message) {
      try {
        $job_data = json_decode($message->body, true);
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

        do_action('wp_async_task_before_job', $hook, $message);
        do_action('wp_async_task_before_job_' . $hook, $message);

        do_action($hook, $args, $message);

        do_action('wp_async_task_after_job', $hook, $message);
        do_action('wp_async_task_after_job_' . $hook, $message);

        $result = true;
      } catch (\Exception $e) {
        MasterLog::queue()->error('RabbitMQWorker->do_job failed: ' . $e->getMessage());
        $result = false;
      }

      if ($switched) {
        restore_current_blog();
      }
    });

    while (count($this->connection->get_channel()->callbacks)) {
      $this->connection->get_channel()->wait();
    }
  }
}
