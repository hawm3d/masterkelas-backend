<?php

namespace MasterKelas;

/**
 * AES-128-CBC Encryption
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class AES128CBC {
  private $encrypter;

  public static function encrypt($value) {
    return (new self())->encrypter->encrypt($value);
  }

  public static function decrypt($value) {
    return (new self())->encrypter->decrypt($value);
  }

  public function __construct() {
    $this->encrypter = new Encrypter(MASTERKELAS_AES_128_CBC_KEY);
  }
}
