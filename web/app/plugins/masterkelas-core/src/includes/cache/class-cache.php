<?php

namespace MasterKelas;

/**
 * MasterKelas Cache
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MasterCache {

  public static function transient($group) {
    return new TransientCache($group);
  }

  public static function rate_limiter() {
    return self::transient("rate");
  }

  public static function user_agent() {
    return self::transient("ua");
  }

  public static function region() {
    return self::transient("region");
  }
}
