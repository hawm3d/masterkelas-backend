<?php

namespace MasterKelas\Schema\Object;

use GraphQL\Error\Error;
use MasterKelas\Model\Auth as AuthModel;

/**
 * Auth Objext type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Auth {
  public static function register() {
    self::auth_type();
    self::auth_context_type();
  }

  public static function auth_type() {
    register_graphql_object_type("Auth", [
      "description" => "Authentication object type",
      "fields" => [
        "tokenType" => [
          "type" => ["non_null" => "String"],
          "description" => "Token Type",
        ],
        "accessToken" => [
          "type" => ["non_null" => "String"],
          "description" => "JWT Token that can be used in future requests for Authentication",
        ],
        "refreshToken" => [
          "type" => ["non_null" => "String"],
          "description" => "A JWT token that can be used in future requests to get a refreshed AuthToken",
        ],
      ],
    ]);
  }

  public static function auth_context_type() {
    register_graphql_scalar('AuthContext', [
      'description' => "Auth context scalar",
      'serialize' => function ($value) {
        return $value;
      },
      'parseValue' => function ($value) {
        return AuthModel::validate_context($value);
      },
      'parseLiteral' => function ($valueNode) {
        return AuthModel::validate_context($valueNode->value);
      }
    ]);
  }
}
