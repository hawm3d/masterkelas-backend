<?php

declare(strict_types=1);

namespace MasterKelas;

use GraphQL\Error\ClientAware;
use RuntimeException;

/**
 * MasterKelas Exception Class
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MasterException extends RuntimeException implements ClientAware
{
  /**
   * @return bool
   */
  public function isClientSafe() {
    return true;
  }

  /**
   * @return string
   */
  public function getCategory()
  {
    return 'masterkelas';
  }
}
