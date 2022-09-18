<?php

namespace MasterKelas;

use Hidehalo\Nanoid\Client;

/**
 * Generate Nano Id
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/includes
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class NanoId {
  public static function generate($size = 21) {
    $client = new Client();
    return $client->generateId($size, Client::MODE_DYNAMIC);
  }
}
