<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Table;

/**
 * OTP Table columns
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP_Table extends Table {
  public $name = 'mk_otps';

  protected $db_version_key = 'mk_otps_version';

  protected $version = '202207134';

  protected $upgrades = [];

  protected function set_schema() {
    $this->schema = "
      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      type varchar(100) NOT NULL,
      recipient varchar(100) NOT NULL,
      token varchar(255) NOT NULL default '',
      created_at datetime NOT NULL,
      expire_at datetime NOT NULL,
      status tinyint NOT NULL default '0',
      data text DEFAULT NULL,
      PRIMARY KEY  (ID)
      ";
  }
}
