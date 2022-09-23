<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Table;

/**
 * User Action Table columns
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class User_Action_Table extends Table {
  public $name = 'mk_user_actions';

  protected $db_version_key = 'mk_user_actions_version';

  protected $version = '202207134';

  protected $upgrades = [];

  protected function set_schema() {
    $this->schema = "
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      uid varchar(100) NOT NULL,
      user_id bigint(20) unsigned NOT NULL,
      session_id bigint(20) unsigned NOT NULL default '0',
      creator_id bigint(20) unsigned NOT NULL default '0',
      action mediumtext NOT NULL,
      priority tinyint NOT NULL default '100',
      data text DEFAULT NULL,
      config text DEFAULT NULL,
      status tinyint NOT NULL default '0',
      created_at datetime NOT NULL default CURRENT_TIMESTAMP,
      updated_at datetime NOT NULL default CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uid (uid),
      KEY user_id (user_id),
      KEY session_id (session_id),
      KEY priority (priority),
      KEY status (status),
      KEY created_at (created_at),
      KEY updated_at (updated_at)
      ";
  }
}
