<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Table;

/**
 * Notification Table columns
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Notification_Table extends Table {
  public $name = 'mk_notifications';

  protected $db_version_key = 'mk_notifications_version';

  protected $version = '202207134';

  protected $upgrades = [];

  protected function set_schema() {
    $this->schema = "
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      uuid varchar(100) NOT NULL,
      user_id bigint(20) unsigned NOT NULL,
      action_id bigint(20) unsigned NOT NULL default '0',
      type mediumtext NOT NULL,
      template mediumtext default NULL,
      data text DEFAULT NULL,
      status tinyint NOT NULL default '0',
      created_at datetime NOT NULL default CURRENT_TIMESTAMP,
      read_at datetime default NULL,
      PRIMARY KEY (id),
      UNIQUE KEY uuid (uuid),
      KEY user_id (user_id),
      KEY action_id (action_id),
      KEY status (status),
      KEY created_at (created_at),
      KEY read_at (read_at)
      ";
  }
}
