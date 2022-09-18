<?php

namespace MasterKelas\Schema\Query\Action;

use MasterKelas\Model\UserAction;
use MasterKelas\Model\UserActionStatuses;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Read Action query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class ReadAction {
  public static function register() {
    self::read_action();
  }

  public static function read_action() {
    Schema::query(
      'RootQuery',
      "readAction",
      [
        "type" => "Boolean",
        "description" => "Read actions by ids",
        "args" => [
          "id" => [
            "type" => ["list_of" => "String"],
            "description" => "List of action ids"
          ]
        ],
        "resolve" => function ($_, $args, MasterContext $context) {
          $ids = $args['id'] ?? [];
          $user = $context->auth->user;
          $readed = false;
          $readable_actions = ["welcome"];

          if (!empty($ids))
            foreach ($ids as $id) {
              $action = UserAction::find(['uid' => sanitize_text_field($id), "user_id" => $user->id]);
              if (!$action || !in_array($action->action, $readable_actions)) continue;

              if ($action->uid === $user->action_required)
                $user->delete_meta("action_required");

              $readed = true;
              UserAction::update($action->id, ["status" => UserActionStatuses::Done]);
            }

          return $readed;
        }
      ],
      ["zone" => "user"]
    );
  }
}
