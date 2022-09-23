<?php

namespace MasterKelas\Region;

/**
 * Region Provider Trait
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
interface RegionProviderTrait {
  public function fetch(String $ip);
}
