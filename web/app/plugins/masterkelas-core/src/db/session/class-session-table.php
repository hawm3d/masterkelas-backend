<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Table;

/**
 * Session Table columns
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Session_Table extends Table {
  public $name = 'mk_sessions';

  protected $db_version_key = 'mk_sessions_version';

  protected $version = '202207134';

  protected $upgrades = [];

  protected function set_schema() {
    $this->schema = "
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      user_id bigint(20) unsigned NOT NULL default '0',
      app_id bigint(20) unsigned NOT NULL default '0',
      terminator_id bigint(20) unsigned DEFAULT NULL,
      fingerprint varchar(100) NOT NULL,
      ua text DEFAULT NULL,
      data text DEFAULT NULL,
      status tinyint NOT NULL default '0',
      created_at datetime NOT NULL default CURRENT_TIMESTAMP,
      terminated_at datetime default NULL,
      last_activity datetime default NULL,
      PRIMARY KEY (id),
      KEY user_id (user_id),
      KEY app_id (app_id),
      KEY terminator_id (terminator_id),
      UNIQUE KEY fingerprint (fingerprint),
      KEY status (status),
      KEY created_at (created_at),
      KEY terminated_at (terminated_at),
      KEY last_activity (last_activity)
      ";
  }
}
