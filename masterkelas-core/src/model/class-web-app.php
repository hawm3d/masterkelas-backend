<?php

namespace MasterKelas\Model;

use MasterKelas\Admin;
use MasterKelas\JWT;
use MasterKelas\MasterException;

/**
 * MasterKelas Frontend Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class WebApp {
  public const WEBAPP_VERSION = "1.0.0";

  public static function get_webapp_url() {
    return untrailingslashit(Admin::get_option("front-url") ?? "https://masterkelas.com");
  }

  public static function JWT() {
    $webapp_url = self::get_webapp_url();
    return (new JWT())
      ->set_issuer($webapp_url)
      ->set_audience(MASTERKELAS_WEB_APP_ID);
  }
}
