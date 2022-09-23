<?php
namespace MasterKelas\Database;

use BerlinDB\Database\Query;

/**
 * Session Query Builder
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Session_Query extends Query
{
  protected $table_name = 'mk_sessions';

  protected $table_alias = 'se';

  protected $table_schema = '\\MasterKelas\\Database\\Session_Schema';

  protected $item_name = 'session';

  protected $item_name_plural = 'sessions';

  protected $item_shape = '\\MasterKelas\\Database\\Session_Row';
}
