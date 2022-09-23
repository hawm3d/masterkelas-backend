<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\Controller\AuthController;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Logout query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Logout {
  public static function register() {
    self::logout();
  }

  public static function logout() {
    Schema::query(
      'RootQuery',
      "logout",
      [
        "type" => "String",
        "description" => "Logout authenticated user",
        "resolve" => function ($_, $__, MasterContext $context) {
          return AuthController::terminate($context->auth);
        }
      ],
      ["zone" => "user"]
    );
  }
}
