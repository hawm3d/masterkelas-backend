<?php

namespace MasterKelas\Schema\Object;

/**
 * User account object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class UserAccount {
  public static function register() {
    self::user_account_type();
  }

  public static function user_account_type() {
    register_graphql_object_type("UserSubscription", [
      "description" => "User subscription",
      "fields" => [
        "active" => [
          "type" => ["non_null" => "Boolean"],
          "description" => "Subscription status",
        ],
        "timeLeft" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Subscription timeLeft",
        ],
      ],
    ]);
    register_graphql_object_type("UserAccount", [
      "description" => "User account settings",
      "fields" => [
        "subscription" => [
          "type" => ["non_null" => "UserSubscription"],
          "description" => "User subscription",
        ],
        "suspension" => [
          "type" => ["non_null" => "UserSuspension"],
          "description" => "User suspension status",
        ],
        "classes" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Number of classes",
        ],
        "actionRequired" => [
          "type" => ["non_null" => "String"],
          "description" => "Action required",
        ],
      ],
    ]);
  }
}
