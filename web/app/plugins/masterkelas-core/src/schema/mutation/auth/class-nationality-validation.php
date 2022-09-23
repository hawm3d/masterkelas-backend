<?php

namespace MasterKelas\Schema\Mutation;

use Carbon\Carbon;
use MasterKelas\MasterException;
use MasterKelas\Model\Auth;
use MasterKelas\Model\UserAction;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Nationality validation Mutation
 *
 * Send and verify OTP
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class NationalityValidation {
  public static function register() {
    Schema::mutation(
      "nationalityValidation",
      [
        "inputFields" => self::input_fields(),
        "outputFields" => self::output_fields(),
        "mutate" => self::mutate(),
      ],
      ["zone" => "user"],
    );
  }

  public static function input_fields() {
    return [
      "answer" => [
        "type" => ["non_null" => "String"],
        "description" => "Answer of validation"
      ],
    ];
  }

  public static function output_fields() {
    return [
      "startedAt" => [
        "type" => "Integer",
        "description" => "Validation start timestamp",
      ],
      "ttl" => [
        "type" => "Integer",
        "description" => "Validation TTL",
      ],
      "attempts" => [
        "type" => "Integer",
        "description" => "Remaining attempts",
      ],
      "maxAttempts" => [
        "type" => "Integer",
        "description" => "Remaining maxAttempts",
      ],
      "status" => [
        "type" => "Integer",
        "description" => "Validation status",
      ],
    ];
  }

  public static function mutate() {
    return function ($input, MasterContext $context) {
      if (!$context->auth->user->action_required || empty($context->auth->user->action_required))
        throw new MasterException("invalid.request");

      if (!isset($input['answer']) || empty($input['answer']))
        throw new MasterException("invalid.input");

      $action = UserAction::find(['uid' => $context->auth->user->action_required, 'status__in' => [0, 1]]);
      if (!$action || $action->action !== 'complete-registration' || !$action->fields || empty($action->fields))
        throw new MasterException("invalid.request");

      $action_field = null;
      foreach ($action->fields as $field)
        if (isset($field['type']) && $field['type'] === 'nationality_validation')
          $action_field = $field;

      if (!$action_field || !isset($action_field['question_uid'], $action_field['ttl'], $action_field['max_attempts'], $action_field['attempts']))
        throw new MasterException("invalid.action");

      $started_at = $action_field['started_at'] ?? Carbon::now()->timestamp;
      $ttl = (int) ($action_field['ttl'] ?? MINUTE_IN_SECONDS * 100);
      $attempts = (int) $action_field['attempts'];
      $max_attempts = (int) $action_field['max_attempts'];
      $response = [
        "startedAt" => $started_at,
        "ttl" => $ttl / 2,
        "maxAttempts" => $max_attempts,
        "attempts" => $attempts + 1,
        "status" => 0,
      ];

      if (
        Carbon::createFromTimestamp($started_at)->addSeconds($ttl)->lte(Carbon::now())
        || $attempts + 1 >= $max_attempts
      ) {
        $response['status'] = 5;
        $context->auth->user->delete_meta("action_required");
        $context->auth->user->update_meta("nationality_status", "failed");
        $context->auth->user->suspend_account([
          "intensity" => "soft",
          "reason" => "nationality_validation",
          "action_id" => $action->id,
        ]);
      }

      if (in_array($response['status'], [0, 1]) && Auth::validate_nationality_question($action_field['question_uid'], sanitize_text_field($input['answer']))) {
        $response['status'] = 2;
        $context->auth->user->delete_meta("action_required");
        $context->auth->user->update_meta("nationality_status", "passed");

        $action = UserAction::assign([
          "user_id" => $context->auth->user->id,
          "action" => "welcome",
          "priority" => 100,
        ]);

        if (is_wp_error($action[0]) || $action[0] <= 0)
          $context->auth->user->delete_meta("action_required");
        else
          $context->auth->user->update_meta("action_required", $action[1]);
      }

      $data = $action->data;
      foreach ($data['fields'] as $key => $f) {
        if (isset($f['type'], $f['attempts']) && $f['type'] === 'nationality_validation') {
          if (!isset($data['fields'][$key]["started_at"]) || empty($data['fields'][$key]["started_at"]))
            $data['fields'][$key]["started_at"] = $started_at;

          $data['fields'][$key]["attempts"] = $response['attempts'];
        }
      }

      UserAction::update($action->id, [
        "status" => $response['status'],
        "data" => $data
      ]);

      return $response;
    };
  }
}
