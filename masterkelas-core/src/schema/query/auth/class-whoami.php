<?php

namespace MasterKelas\Schema\Query;

use Carbon\Carbon;
use MasterKelas\Model\User;
use MasterKelas\Model\UserAction;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Return User and Session Info
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Whoami {
  public static function register() {
    self::whoami();
  }

  public static function whoami() {
    Schema::query(
      'RootQuery',
      "whoami",
      [
        "type" => ["non_null" => "AuthenticatedUser"],
        "description" => "Authenticated user data",
        "resolve" => function ($_, $__, MasterContext $context) {
          $user = $context->auth->user;
          $session = $context->auth->session;
          $subscription = [
            "timeLeft" => 0,
            "active" => false
          ];
          $actions = [];
          $user_actions = UserAction::find(['user_id' => $user->id, 'session_id__in' => [$session->id, 0], 'status__in' => [0, 1], "number" => -1]);

          if (!empty($user_actions))
            foreach ($user_actions as $action)
              $actions[] = [
                "id" => (string) $action->uid,
                "action" => (string) $action->action,
                "priority" => (int) $action->priority,
              ];

          if (wcs_user_has_subscription($user->id, '', 'active')) {
            $subscriptions = wcs_get_users_subscriptions($user->id);
            foreach ($subscriptions as $sub) {
              $_subscription = wcs_get_subscription($sub->ID);
              if ($_subscription->get_status() === 'active') {
                $subscription['active'] = true;
                $endDate = Carbon::parse($_subscription->get_date("end"))->diffInDays();
                $subscription['timeLeft'] = $endDate > 0 ? $endDate : 0;
              }
            }
          }

          return [
            "profile" => [
              "id" => $user->get_id(),
              "name" => $user->name ?? "",
              "firstName" => $user->first_name ?? "",
              "lastName" => $user->last_name ?? "",
            ],
            "session" => [
              "status" => "",
              "active" => $session->is_active,
              "locked" => $session->is_locked,
              "terminated" => $session->is_terminated,
              "lastActivity" => time(),
            ],
            "account" => [
              "subscription" => $subscription,
              "suspension" => [
                "suspended" => $user->suspended,
                "intensity" => isset($user->suspension['intensity']) ? (string) $user->suspension['intensity'] : null,
                "reason" => isset($user->suspension['reason']) ? (string) $user->suspension['reason'] : null,
                "lockdown" => isset($user->suspension['lockdown']) ? (int) $user->suspension['lockdown'] : null,
              ],
              "classes" => 0,
              "actionRequired" => $user->action_required,
            ],
            "actions" => $actions,
            "notifications" => [],
          ];
        }
      ],
      ["zone" => "user"]
    );
  }
}
