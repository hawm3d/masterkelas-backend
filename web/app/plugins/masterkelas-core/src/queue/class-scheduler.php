<?php

namespace MasterKelas\Queue;

use MasterKelas\MasterLog;

/**
 * The core queue and scheduler class.
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Scheduler {
  public static $queue_name = 'masterkelas';
  public static $job_middlename = "_job_";

  public static function hooks() {
    add_action('test_queue', [__CLASS__, "test_queue"]);
    add_action('init', [__CLASS__, "disable_default_runner"], 10);

    $jobs = self::jobs();
    foreach ($jobs as $job => $action) {
      add_action(
        self::$queue_name . self::$job_middlename . $job,
        [__NAMESPACE__ . "\Job\\" . $action["class"], "process"],
        isset($action["priority"]) ? $action["priority"] : 10,
        isset($action["args"]) ? $action["args"] : 1,
      );
    }
  }

  public static function test_queue($args) {
    MasterLog::queue()->info("hi", $args);
  }

  public static function jobs() {
    return [
      'send_otp_to_email' => [
        "class" => 'OTP_Email',
      ],
      'send_otp_to_mobile' => [
        "class" => 'OTP_Mobile',
      ],
    ];
  }

  public static function disable_default_runner() {
    if (class_exists('ActionScheduler')) {
      remove_action('action_scheduler_run_queue', array(\ActionScheduler::runner(), 'run'));
    }
  }
}
