<?php

namespace MasterKelas;

use DateInterval;
use InvalidArgumentException;

/**
 * Cache Trait
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
trait CacheTrait {
  protected function is_valid_key_value($key): bool {
    if (!is_string($key) || empty($key)) {
      throw new InvalidArgumentException('Key must be a valid string');
    }
    return !(bool) preg_match('|[\{\}\(\)/\\\@]|', $key);
  }

  public function ttl_to_seconds($ttl): int {
    switch (true) {
      case is_a($ttl, DateInterval::class):
        $days  = (int) $ttl->format('%a');
        $hours = (int) $ttl->format('%h');
        $mins  = (int) $ttl->format('%i');
        $secs  = (int) $ttl->format('%s');

        return ($days * 24 * 60 * 60)
          + ($hours * 60 * 60)
          + ($mins * 60)
          + $secs;

      case is_numeric($ttl):
        return (int) $ttl;

      default:
        return 0;
    }
  }

  protected function all_true(array $array): bool {
    foreach ($array as $value) {
      if (!is_bool($value)) {
        return false;
      }

      if ($value === false) {
        return false;
      }
    }
    return true;
  }
}
