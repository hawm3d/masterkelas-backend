<?php
namespace MasterKelas\Database;

use BerlinDB\Database\Query;

/**
 * OTP Query Builder
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP_Query extends Query
{
  protected $table_name = 'mk_otps';

  protected $table_alias = 'ot';

  protected $table_schema = '\\MasterKelas\\Database\\OTP_Schema';

  protected $item_name = 'otp';

  protected $item_name_plural = 'otps';

  protected $item_shape = '\\MasterKelas\\Database\\OTP_Row';
}
