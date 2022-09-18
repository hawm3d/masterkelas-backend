<?php

namespace MasterKelas;

use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

/**
 * User Agent parser
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class UserAgent {
  public DeviceDetector $detector;

  public function __construct($ua = null) {
    $ua = !$ua || empty($ua) ? $_SERVER['HTTP_USER_AGENT'] : $ua;
    $this->ua = sanitize_text_field($ua) ?? "";

    AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
    $clientHints = ClientHints::factory($_SERVER);
    $this->detector = new DeviceDetector($this->ua, $clientHints);
    $this->detector->setCache(
      new PSR16Bridge(
        MasterCache::user_agent()
      )
    );

    $this->parse();
  }

  public function parse() {
    $this->detector->parse();
  }

  public function client() {
    $client = $this->detector->getClient();
    return [
      "type" => $this->sanitize($client['type']),
      "name" => $this->sanitize($client['name']),
      "version" => $this->sanitize($client['version']),
      "family" => $this->sanitize($client['family']),
    ];
  }

  public function device() {
    return [
      "type" => $this->sanitize($this->detector->getDeviceName()),
      "brand" => $this->sanitize($this->detector->getBrandName()),
      "model" => $this->sanitize($this->detector->getModel()),
    ];
  }

  public function os() {
    $os = $this->detector->getOs();
    return [
      "name" => $this->sanitize($os['name']),
      "version" => $this->sanitize($os['version']),
      "platform" => $this->sanitize($os['platform']),
    ];
  }

  protected function sanitize($field) {
    return (!$field || empty($field)) ? null : $field;
  }
}
