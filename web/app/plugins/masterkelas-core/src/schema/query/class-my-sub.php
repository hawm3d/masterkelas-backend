<?php

namespace MasterKelas\Schema\Query;

use Carbon\Carbon;
use MasterKelas\MasterException;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * MySub
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class MySub {
  public static function register() {
    self::my_sub_field();
  }

  public static function my_sub_field() {
    register_graphql_object_type("MySub", [
      "description" => "MySub attributes",
      "fields" => [
        "timeLeft" => [
          "type" => "Integer"
        ],
        "status" => [
          "type" => "String"
        ],
      ],
    ]);

    Schema::query(
      'RootQuery',
      "mySub",
      [
        "type" => "MySub",
        "resolve" => function ($_, $args, MasterContext $context) {
          $res = [
            "timeLeft" => 0,
            "status" => "inactive"
          ];

          $user = $context->auth->user;
          if (!wcs_user_has_subscription($user->id, '', 'active')) return $res;

          $date = null;
          $subscriptions = wcs_get_users_subscriptions($user->id);
          foreach ($subscriptions as $sub) {
            $subscription = wcs_get_subscription($sub->ID);
            if ($subscription->get_status() === 'active') {
              $res['status'] = "active";
              $endDate = Carbon::parse($subscription->get_date("end"));
              graphql_debug([$endDate->diffInDays()]);
            }
          }

          return $res;
        }
      ],
      ["zone" => "user"]
    );
  }
}
