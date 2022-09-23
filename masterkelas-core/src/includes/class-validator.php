<?php

namespace MasterKelas;

use MasterKelas\Admin;

/**
 * Validate attributes
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Validator {
  public static function name($value) {
    return (bool) preg_match("/^[\x{600}-\x{6FF}\x{200c}\x{064b}\x{064d}\x{064c}\x{064e}\x{064f}\x{0650}\x{0651}\s]+$/u", $value);
  }

  public static function mobile($value = '') {
    return preg_match('/^((0)(9){1}[0-9]{9})+$/', $value);
  }

  public static function email($value = '', $skip_provider = false) {
    if (!is_email($value)) return false;

    $providers = Admin::get_option("auth-email-allowed-providers");
    $providers = !is_array($providers) ? [] : $providers;

    if (empty($providers) || $skip_provider) return true;

    foreach ($providers as $provider) {
      $value_provider = explode("@", $value)[1];
      if ($value_provider === $provider) return true;
    }

    return false;
  }
}
