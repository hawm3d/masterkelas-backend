<?php

namespace MasterKelas\Queue;

use MasterKelas\MasterLog;

/**
 * Queue Task Operations
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Task {
  public static function send_otp($type, $data = []) {
    MasterLog::queue("send_otp", [$type, $data]);
    return self::add("masterkelas_job_send_otp_to_{$type}", $data, 'high');
  }

  public static function add($hook, $args = array(), $priority = 'normal') {
    $queue = \MasterKelas\Queue\Queue::get_instance();
    return $queue->add($hook, $args, $priority);
  }
}
