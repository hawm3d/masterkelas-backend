<?php

namespace MasterKelas\Model;

use MasterKelas\MasterCache;
use MasterKelas\MasterException;
use MasterKelas\RemoteAddress;
use MasterKelas\Schema;

/**
 * Rate limit input and output
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class RateLimiter {

  public $consumer;
  public $action;
  public $limit;
  public $interval;
  public $rate = null;

  public $sensitive;
  public $sensitivity = 0;
  public $sensitivity_interval = 10;

  public $cache;
  public $cache_key;

  public static function throttle($args = []) {
    $limiter = new self($args);
    if (!$limiter->rate || !isset($limiter->rate['remaining'], $limiter->rate['interval']))
      return;

    $timeleft = $limiter->timeleft();
    $rate_headers = [
      "X-RateLimit-Limit" => $limiter->rate['limit'],
      "X-RateLimit-Remaining" => $limiter->rate['remaining'],
      "X-RateLimit-Reset" => $timeleft ?? 0,
    ];

    if ($limiter->rate['remaining'] < 0 && is_int($timeleft) && $timeleft > 0) {
      $rate_headers["Retry-After"] = $timeleft ?? 0;
      $rate_headers["X-RateLimit-Remaining"] = 0;

      Schema::set_status(429);
    }

    Schema::append_headers($rate_headers);

    if ($limiter->rate['remaining'] < 0)
      throw new MasterException("rate.limit.exceeded");
  }

  public function __construct($args = []) {
    $this->consumer = $args['consumer'] ?? RemoteAddress::Ip();
    $this->action = $args['action'] ?? "request";
    $this->limit = $args['limit'] ?? 500;
    $this->interval = $args['interval'] ?? HOUR_IN_SECONDS;

    $this->sensitive = !isset($args['sensitive']) || $args['sensitive'] === true;
    $this->sensitivity_interval = $args['sensitivity_interval'] ?? 10;

    $this->cache = MasterCache::rate_limiter();

    if (!$this->consumer || !$this->action || !$this->limit || !$this->interval)
      throw new MasterException("bad.request");

    $this->init();
  }

  public function init() {
    $this->cache_key = "{$this->action}:{$this->consumer}";

    if ($this->sensitive) {
      $this->sensitivity = $this->get_sensitivity();
      $this->interval += $this->calculate_sensitivity_interval();
    }

    $this->rate = $this->get_rate();
    $this->rate['remaining'] -= 1;
    $this->cache->set($this->cache_key, $this->rate, $this->timeleft());

    if ($this->sensitive && $this->rate['remaining'] <= 0) {
      $amount = 0;

      switch ($this->rate['remaining']) {
        case 0:
          $amount = 1;
          break;
        case -10:
          $amount = 2;
          break;
        case -20:
          $amount = 4;
          break;
        case -30:
          $amount = 8;
          break;
      }

      if ($amount > 0)
        $this->sensitivity = $this->increase_sensitivity($amount, ceil($this->interval + ($this->interval / 2)));
    }
  }

  public function get_rate() {
    $rate = $this->cache->get($this->cache_key);

    if (
      $rate &&
      isset($rate['start'], $rate['interval']) &&
      $this->timeleft($rate) <= 0
    ) {
      $this->cache->delete($this->cache_key);
      $rate = null;
    }

    if (!$rate)
      $rate = [
        "consumer" => (string) $this->consumer,
        "action" => (string) $this->action,
        "limit" => (int) $this->limit,
        "interval" => (int) $this->interval,
        "remaining" => (int) $this->limit,
        "start" => time(),
      ];

    return $rate;
  }

  public function get_sensitivity($create = true) {
    $sens = $this->cache->get("{$this->cache_key}_sensitivity");

    if (!$sens && !is_int($sens))
      return $create ? self::create_sensitivity() : 0;

    return (int) $sens;
  }

  public function create_sensitivity($value = 0, $expire = null) {
    $this->cache->set("{$this->cache_key}_sensitivity", $value, $expire ?? $this->interval * 2);

    return $value;
  }

  public function increase_sensitivity($value = 1, $expire = null) {
    $sens = $this->get_sensitivity(false) ?? 0;
    $value = $sens + $value;
    $expire = $expire > 0 ? $expire + ($value * MINUTE_IN_SECONDS) : null;

    return $this->create_sensitivity($value, $expire);
  }

  public function calculate_sensitivity_interval() {
    $ratio = 1;

    switch (true) {
      case $this->sensitivity >= 5 && $this->sensitivity <= 10:
        $ratio = 2;
        break;

      case $this->sensitivity > 10 && $this->sensitivity <= 15:
        $ratio = 3;
        break;

      case $this->sensitivity > 15:
        $ratio = 5;
        break;
    }

    return $this->sensitivity * ($this->interval * $ratio);
  }

  public function timeleft($rate = null) {
    $rate = $rate ?? $this->rate;
    return $rate['start'] + $rate['interval'] - time();
  }
}
