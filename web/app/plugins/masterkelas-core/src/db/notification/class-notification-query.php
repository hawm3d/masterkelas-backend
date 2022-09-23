<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Query;

/**
 * Notification Query Builder
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Notification_Query extends Query {
  protected $table_name = 'mk_notifications';

  protected $table_alias = 'no';

  protected $table_schema = '\\MasterKelas\\Database\\Notification_Schema';

  protected $item_name = 'notification';

  protected $item_name_plural = 'notifications';

  protected $item_shape = '\\MasterKelas\\Database\\Notification_Row';
}
