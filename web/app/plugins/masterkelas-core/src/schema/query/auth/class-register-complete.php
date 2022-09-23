<?php

namespace MasterKelas\Schema\Query;

use Carbon\Carbon;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\Google;
use MasterKelas\Model\OTP;
use MasterKelas\Model\UserAction;
use MasterKelas\Model\UserActionStatuses;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Register
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RegisterComplete {
  public static function register() {
    self::register_complete_field();
  }

  public static function register_complete_field() {
    Schema::query(
      'RootQuery',
      "registerComplete",
      [
        "type" => "RegisterCompleteInit",
        "description" => "Register Complete form settings",
        "resolve" => function ($_, $__, MasterContext $context) {
          $inputs = [];

          if (!$context->auth->user->action_required || empty($context->auth->user->action_required))
            return $inputs;

          $action = UserAction::find(['uid' => $context->auth->user->action_required, 'status__in' => [0, 1]]);
          if (!$action || $action->action !== 'complete-registration' || !$action->fields || empty($action->fields))
            return $inputs;

          $new_action = [];

          if ($action->status === UserActionStatuses::Waiting)
            $new_action['status'] = UserActionStatuses::Observed;

          foreach ($action->fields as $field_key => $field)
            if (isset($field['type']))
              switch ($field['type']) {
                case 'mobile':
                case 'email':
                  $inputs[] = [
                    'type' => $field['type'],
                    'status' => 0,
                    'data' => json_encode([
                      "otpLength" => OTP::get_code_length($field['type']),
                      "restrictNonIranian" => \MasterKelas\Admin::get_option("auth-{$field['type']}-restrict")
                    ])
                  ];
                  break;
                case 'nationality_validation':
                  $started_at = (int) ($field['started_at'] ?? 0);
                  $ttl = (int) $field['ttl'];
                  $max_attempts = (int) $field['max_attempts'];
                  $attempts = (int) $field['attempts'];

                  if (!$started_at) {
                    if (!isset($new_action['data']))
                      $new_action['data'] = $action->data;

                    $started_at = Carbon::now()->timestamp;
                    $new_action['data']['fields'][$field_key]['started_at'] = $started_at;
                  }

                  if (
                    Carbon::createFromTimestamp($started_at)->addSeconds($ttl)->lte(Carbon::now())
                    || $attempts + 1 >= $max_attempts
                  ) {
                    UserAction::update($action->id, ["status" => UserActionStatuses::Failed]);
                    $context->auth->user->delete_meta("action_required");
                    $context->auth->user->update_meta("nationality_status", "failed");
                    $context->auth->user->suspend_account([
                      "intensity" => "soft",
                      "reason" => "nationality_validation",
                      "action_id" => $action->id,
                    ]);

                    throw new MasterException("register.complete.nv.failed");
                  }

                  $inputs[] = [
                    'type' => $field['type'],
                    'status' => 0,
                    'data' => json_encode([
                      "question" => (string) Auth::get_nationality_question_by_uid($field['question_uid'])['question'],
                      "ttl" => $ttl,
                      "maxAttempts" => $max_attempts,
                      "attempts" => $attempts,
                      "startedAt" => (int) $started_at,
                    ])
                  ];
                  break;
              }

          if (!empty($new_action))
            UserAction::update($action->id, $new_action);

          if (empty($inputs))
            throw new MasterException("register.complete.invalid.action");

          return [
            "inputs" => $inputs,
          ];
        }
      ],
      ["zone" => "user"],
    );
  }
}
