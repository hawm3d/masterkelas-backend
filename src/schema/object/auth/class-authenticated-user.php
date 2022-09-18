<?php

namespace MasterKelas\Schema\Object;

/**
 * Authenticated user object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class AuthenticatedUser {
  public static function register() {
    self::authenticated_user_type();
  }

  public static function authenticated_user_type() {
    register_graphql_object_type("AuthenticatedUser", [
      "description" => "Authenticated user profile, session and data",
      "fields" => [
        "profile" => [
          "type" => ["non_null" => "UserProfile"],
          "description" => "User profile data",
        ],
        "session" => [
          "type" => ["non_null" => "UserSession"],
          "description" => "User session info",
        ],
        "account" => [
          "type" => ["non_null" => "UserAccount"],
          "description" => "User account settings",
        ],
        "actions" => [
          "type" => [
            "list_of" => "UserAction"
          ],
          "description" => "User actions",
        ],
        "notifications" => [
          "type" => [
            "list_of" => "Notification"
          ],
          "description" => "User unread notifications",
        ],
      ],
    ]);
  }
}
