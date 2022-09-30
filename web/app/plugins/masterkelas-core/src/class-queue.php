<?php

namespace MasterKelas;

use MasterKelas\Queue\Scheduler;

/**
 * The queue core class
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Queue {
  public static function get_group($group = '') {
    return Scheduler::$queue_name . (!empty($group) ? ".{$group}" : "");
  }

  public static function get_job_name($name = '') {
    return Scheduler::$queue_name . Scheduler::$job_middlename . $name;
  }

  public static function async($name, $data = [], $group = '', $hook = true) {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    if (!$hook)
      return as_enqueue_async_action($name, $data, $group);

    add_action(
      'init',
      function () use ($name, $data, $group) {
        as_enqueue_async_action($name, $data, $group);
      }
    );
  }

  public static function single($timestamp, $name, $data = [], $group = '', $hook = true) {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    if (!$hook)
      return as_schedule_single_action($timestamp, $name, $data, $group);

    add_action(
      'init',
      function () use ($timestamp, $name, $data, $group) {
        as_schedule_single_action($timestamp, $name, $data, $group);
      }
    );
  }

  public static function recurring($timestamp, $interval_in_seconds, $name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($timestamp, $interval_in_seconds, $name, $data, $group) {
        as_schedule_recurring_action($timestamp, $interval_in_seconds, $name, $data, $group);
      }
    );
  }

  public static function cron($timestamp, $schedule, $name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($timestamp, $schedule, $name, $data, $group) {
        as_schedule_cron_action($timestamp, $schedule, $name, $data, $group);
      }
    );
  }

  public static function unschedule($name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($name, $data, $group) {
        as_unschedule_action($name, $data, $group);
      }
    );
  }

  public static function unschedule_all($name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($name, $data, $group) {
        as_unschedule_all_actions($name, $data, $group);
      }
    );
  }

  public static function next($name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($name, $data, $group) {
        as_next_scheduled_action($name, $data, $group);
      }
    );
  }

  public static function has($name, $data = [], $group = '') {
    $name = self::get_job_name($name);
    $group = self::get_group($group);

    add_action(
      'init',
      function () use ($name, $data, $group) {
        as_has_scheduled_action($name, $data, $group);
      }
    );
  }
}
