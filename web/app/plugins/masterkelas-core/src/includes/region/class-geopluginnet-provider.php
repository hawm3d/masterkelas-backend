<?php

namespace MasterKelas\Region;

use MasterKelas\Region\RegionProviderTrait;

/**
 * GeoPluginNet Provider
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes/cache
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class GeoPluginNetProvider implements RegionProviderTrait {
  public function fetch($ip) {
    $data = [];
    $client = new \GuzzleHttp\Client([
      'curl' => array(CURLOPT_SSL_VERIFYPEER => false),
      'headers' => ['Content-Type' => 'application/json'],
    ]);

    $response = $client->request('GET', 'http://www.geoplugin.net/json.gp', [
      'http_errors' => false,
      'query' => [
        'ip' => $ip
      ]
    ]);

    if ($response->getStatusCode() == 200) {
      $data = json_decode($response->getBody(), true);

      if (
        is_array($data) &&
        !empty($data) &&
        isset($data['geoplugin_request'], $data['geoplugin_countryName'], $data['geoplugin_countryCode'], $data['geoplugin_timezone']) &&
        esc_textarea($data['geoplugin_request']) === $ip
      ) {
        $data['name'] = esc_textarea($data['geoplugin_countryName']);
        $data['iso_code'] = esc_textarea($data['geoplugin_countryCode']);
        $data['timezone'] = esc_textarea($data['geoplugin_timezone']);
      }
    }

    return $data;
  }
}
