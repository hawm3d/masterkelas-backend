<?php

namespace MasterKelas\Model;

use Google_Client;
use MasterKelas\Admin;
use MasterKelas\MasterException;

/**
 * Google APIs and Services
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Google {
  public static function get_client_id() {
    return (string) Admin::get_option("auth-google-client-id");
  }

  public static function get_client_secret() {
    return (string) Admin::get_option("auth-google-client-secret");
  }

  public static function oauth($payload) {
    $client_id = self::get_client_id();
    $client_secret = self::get_client_secret();
    $redirect_uri = WebApp::get_webapp_url();

    if (empty($client_id) || empty($client_secret) || empty($redirect_uri))
      throw new MasterException("google.unavailable");

    try {
      $code = sanitize_text_field(isset($payload['code']) ? $payload['code'] : "");

      if (empty($code))
        throw new \Error();

      $client = new Google_Client([
        'client_id' => $client_id,
        'client_secret' => $client_secret,
      ]);

      $guzzleClient = new \GuzzleHttp\Client([
        "curl" => [
          CURLOPT_SSL_VERIFYPEER => false
        ],
        'http_errors' => false,
      ]);

      $client->setHttpClient($guzzleClient);
      $client->setRedirectUri($redirect_uri);
      $client->setScopes('email');

      $token = $client->fetchAccessTokenWithAuthCode($code);
      if (empty($token) || !isset($token['id_token']))
        throw new \Error();

      $client->setAccessToken($token);
      $response = $client->verifyIdToken();

      if (!isset($response['azp'], $response['email'], $response['given_name'], $response['family_name'], $response['exp']) || $response['azp'] !== $client_id)
        throw new \Error();
    } catch (\Throwable $th) {
      throw new MasterException("google.invalid.code");
    }

    if (time() - intval($response['exp']) >= 0)
      throw new MasterException("google.code.expired");

    return [
      'email' => $response['email'],
      'first_name' => $response['given_name'],
      'last_name' => $response['family_name'],
      'picture' => $response['picture'],
      'sub' => $response['sub'],
    ];
  }
}
