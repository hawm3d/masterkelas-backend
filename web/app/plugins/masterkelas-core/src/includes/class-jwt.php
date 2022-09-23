<?php

namespace MasterKelas;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as PHP_JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

/**
 * JSON Web Tokens (JWT) Wrapper
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class JWT {

  public $claims = [];

  private $alg = 'HS256';
  private $leeway = 60;
  private $expiration_seconds;
  private $key;
  private $validators = [];

  public function __construct($key = null) {
    $this->key = $key ?? wp_salt("auth");
    PHP_JWT::$leeway = $this->leeway;
  }

  public function set_key(String $key) {
    $this->key = $key;
    return $this;
  }

  public function set_issuer(String $issuer) {
    return $this->set_claim("iss", (string) $issuer);
  }

  public function set_subject(String $subject) {
    return $this->set_claim("sub", (string) $subject);
  }

  public function set_audience(String $audience) {
    return $this->set_claim("aud", (string) $audience);
  }

  public function set_not_before(Int $not_before) {
    return $this->set_claim("nbf", (int) $not_before);
  }

  public function set_issued_at(Int $issued_at) {
    return $this->set_claim("iat", (int) $issued_at);
  }

  public function set_expiration_seconds(Int $expiration_seconds) {
    $this->expiration_seconds = $expiration_seconds;
    return $this;
  }

  public function set_expiration_time(Int $expiration_time = 0) {
    $expiration_time = $expiration_time < time() ? $expiration_time + time() : $expiration_time;
    return $this->set_claim("exp", (int) $expiration_time);
  }

  public function set_time_claims($expiration_time, $issued_at, $not_before) {
    $issued_at = $issued_at ?: time();
    $not_before = $not_before ?: $issued_at;

    $this->set_issued_at($issued_at);
    $this->set_not_before($not_before);
    $this->set_expiration_time($expiration_time ?: $this->expiration_seconds);
  }

  public function set_claim($claim, $value) {
    if (!is_null($value))
      $this->claims[$claim] = $value;

    return $this;
  }

  public function parse_bearer($token) {
    $token = sanitize_text_field($token);

    if (empty($token))
      throw new MasterException("invalid.token.type");

    $token = explode(" ", $token);

    if (empty($token) || !isset($token[1]) || empty($token[1]))
      throw new MasterException("invalid.token.type");

    return $token[1];
  }

  public function add_validator($callback) {
    $this->validators[] = $callback;
    return $this;
  }

  private function run_validators($decoded) {
    foreach ($this->validators as $validator) {
      $validator($decoded, $this->claims);
    }
  }

  public function encode(Int $expiration_time = 0, Int $issued_at = 0, Int $not_before = 0) {
    $this->set_time_claims($expiration_time, $issued_at, $not_before);

    if (
      !$this->claims['exp']
      || $this->claims['exp'] <= $this->claims['iat']
      || $this->claims['exp'] <= $this->claims['nbf']
    ) throw new MasterException("invalid.exp");

    try {
      $encoded = PHP_JWT::encode($this->claims, $this->key, $this->alg);
    } catch (\Throwable $th) {
      $this->handle_exceptions($th);
    }

    return $encoded;
  }

  public function decode_bearer($token) {
    return $this->decode(
      $this->parse_bearer($token)
    );
  }

  public function decode($jwt) {
    try {
      $decoded = PHP_JWT::decode($jwt, new Key($this->key, $this->alg));
    } catch (\Throwable $th) {
      $this->handle_exceptions($th);
    }

    if (isset($this->claims['iss']) && $decoded->iss !== $this->claims['iss'])
      throw new MasterException("invalid.iss");

    if (isset($this->claims['aud']) && $decoded->aud !== $this->claims['aud'])
      throw new MasterException("invalid.aud");

    if (isset($this->claims['sub']) && $decoded->sub !== $this->claims['sub'])
      throw new MasterException("invalid.sub");

    $this->run_validators($decoded);

    return $decoded;
  }

  private function handle_exceptions(\Throwable $th) {
    if ($th instanceof ExpiredException)
      throw new MasterException("token.expired");

    if ($th instanceof SignatureInvalidException)
      throw new MasterException("invalid.sign");

    if ($th instanceof BeforeValidException)
      throw new MasterException("token.before.valid");

    throw $th;
  }
}
