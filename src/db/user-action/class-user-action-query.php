<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Query;

/**
 * User Action Query Builder
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class User_Action_Query extends Query {
  protected $table_name = 'mk_user_actions';

  protected $table_alias = 'ua';

  protected $table_schema = '\\MasterKelas\\Database\\User_Action_Schema';

  protected $item_name = 'user_action';

  protected $item_name_plural = 'user_actions';

  protected $item_shape = '\\MasterKelas\\Database\\User_Action_Row';
}
