<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Controller\AuthController;
use MasterKelas\MasterException;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Refresh auth session and tokens
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RefreshSession {
  public static function register() {
    self::refresh_session();
  }

  public static function refresh_session() {
    Schema::query(
      'RootQuery',
      "refreshSession",
      [
        "type" => ["non_null" => "Auth"],
        "description" => "Refresh auth session and tokens",
        "resolve" => function ($_, $__, MasterContext $context) {
          try {
            if (!in_array($context->auth_error, ["token.expired", null]))
              throw new MasterException("auth.invalid.access.token");

            return AuthController::refresh($context->ua);
          } catch (\Throwable $th) {
            graphql_debug([$th->getMessage(), $context->auth_error]);
            Schema::set_status(403);
            throw new MasterException("auth.failed");
          }
        }
      ],
      // [
      //   "throttle" => [
      //     "action" => "reftoken",
      //     "limit" => 1,
      //     "interval" => MINUTE_IN_SECONDS
      //   ]
      // ]
    );
  }
}
