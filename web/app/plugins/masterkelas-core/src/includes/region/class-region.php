<?php

namespace MasterKelas;

use MasterKelas\MasterCache;
use MasterKelas\MasterException;
use MasterKelas\RemoteAddress;

/**
 * User Region
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Region {

  public $ip;
  public $name;
  public $iso_code;
  public $timezone;

  public $cache;
  public $cache_ttl = DAY_IN_SECONDS;
  public $allowed_iso_codes = ["IR"];
  public $providers = [
    '\MasterKelas\Region\MaxmindProvider'
  ];

  public function __construct($ip = null) {
    $this->cache = MasterCache::region();
    $this->ip = $ip ?? RemoteAddress::Ip();
    if (!$this->ip || empty($this->ip))
      throw new MasterException("invalid.ip");

    $this->get();
  }

  public function is_allowed() {
    return in_array($this->iso_code, $this->allowed_iso_codes);
  }

  public function get() {
    $region = $this->cache->get($this->ip);
    // if (!$region)
    //   $region = $this->fetch();

    try {
      if (!$region || !isset($region['name'], $region['iso_code'], $region['timezone']))
        throw new MasterException("invalid.region");
    } catch (\Throwable $th) {
      $this->cache->delete($this->ip);
      $this->name = "Iran";
      $this->iso_code = "IR";
      $this->timezone = "Asia/Tehran";
      return;
      throw $th;
    }

    $this->name = $region['name'];
    $this->iso_code = strtoupper($region['iso_code']);
    $this->timezone = strtolower($region['timezone']);
  }

  public function fetch() {
    $country = null;
    foreach ($this->providers as $provider) {
      try {
        if (class_exists($provider))
          $provider_instance = (new $provider())->fetch($this->ip);

        if ($provider_instance && isset($provider_instance['name'], $provider_instance['iso_code'], $provider_instance['timezone'])) {
          $country = [
            "name" => $provider_instance['name'],
            "iso_code" => $provider_instance['iso_code'],
            "timezone" => $provider_instance['timezone'],
          ];
          break;
        }
      } catch (\Throwable $th) {
        continue;
      }
    }

    if (!$country || !isset($country['name'], $country['iso_code'], $country['timezone']) || empty($country['iso_code']) || empty($country['timezone']))
      return false;

    $this->cache->set($this->ip, $country, $this->cache_ttl);
    return $country;
  }
}
