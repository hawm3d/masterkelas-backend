<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Queue\Task;
use MasterKelas\RemoteAddress;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Boot query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Boot {
  public static function register() {
    self::boot();
  }

  public static function boot() {
    Schema::query(
      'RootQuery',
      "boot",
      [
        "type" => ["non_null" => "VisitorInformation"],
        "description" => "Visitor Information",
        "resolve" => function ($_, $__, MasterContext $context) {
          graphql_debug($_SERVER);

          return [
            'ip' => (string) $context->region->ip,
            'region' => (string) $context->region->iso_code,
            'timezone' => (string) $context->region->timezone,
            'bot' => (bool) $context->ua->detector->isBot(),
            'authenticated' => (bool) $context->auth,
            'policy' => (string) ($context->region->is_allowed() || ($context->auth && $context->auth->user->is_active())) ? "*" : "restricted",
          ];
        }
      ],
    );
  }
}
