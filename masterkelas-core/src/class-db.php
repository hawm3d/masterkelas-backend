<?php

namespace MasterKelas;

use MasterKelas\Database\Notification_Table;
use MasterKelas\Database\OTP_Table;
use MasterKelas\Database\Session_Table;
use MasterKelas\Database\User_Action_Table;

/**
 * Instantiate database tables
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class DB {
  public static function install() {
    self::otps_table();
    self::sessions_table();
    self::user_actions_table();
    self::notification_table();
  }

  public static function otps_table() {
    $table = new OTP_Table;

    if (!$table->exists()) {
      $table->install();
    }
  }

  public static function sessions_table() {
    $table = new Session_Table;

    if (!$table->exists()) {
      $table->install();
    }
  }

  public static function user_actions_table() {
    $table = new User_Action_Table;

    if (!$table->exists()) {
      $table->install();
    }
  }

  public static function notification_table() {
    $table = new Notification_Table;

    if (!$table->exists()) {
      $table->install();
    }
  }
}
