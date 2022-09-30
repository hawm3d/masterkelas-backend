<?php

namespace MasterKelas\Region;

use GeoIp2\Database\Reader;
use MasterKelas\Region\RegionProviderTrait;

/**
 * Maxmind Provider
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class MaxmindProvider implements RegionProviderTrait {
  public function fetch($ip) {
    $data = [];
    $reader = new Reader(PRIVATE_STORAGE_DIR . '/geolite2/country.mmdb');
    $record = $reader->country($ip);

    if (!empty($record->country->name) && !empty($record->country->isoCode)) {
      $data['name'] = esc_textarea($record->country->name);
      $data['iso_code'] = esc_textarea($record->country->isoCode);
      $data['timezone'] = esc_textarea(\DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $record->country->isoCode)[0]);
    }

    return $data;
  }
}
