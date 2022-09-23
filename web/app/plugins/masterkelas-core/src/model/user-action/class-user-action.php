<?php

namespace MasterKelas\Model;

use MasterKelas\Database\User_Action_Query;
use MasterKelas\MasterException;
use MasterKelas\NanoId;

abstract class UserActionStatuses {
  const Waiting = 0;
  const Observed = 1;
  const Done = 2;
  const Canceled = 3;
  const Ignored = 4;
  const Failed = 5;
}

/**
 * User Action Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class UserAction {

  public $user;
  public $session;

  public static $actions_namespace = "\MasterKelas\Model\Actions\\";
  public static $actions = [
    'complete-registration',
    'welcome'
  ];

  public function __construct($args = []) {
    $user = $args['user'] ?? null;
    $session = $args['session'] ?? null;

    if ($user && isset($user->id))
      $this->user = $user;

    if ($session && isset($session->ID)) {
      $this->user = $session->user();
      $this->session = $session;
    }

    if (!$user)
      throw new MasterException("user.required");
  }

  // public function get_handler_class($action) {
  //   $action_handler = self::$actions_namespace . $action;
  //   if (class_exists($action_handler))
  //     return $action_handler;

  //   return null;
  // }

  public static function find($args = []) {
    $defaults = [
      "number" => 1,
      "orderby" => "created_at",
    ];

    $args = wp_parse_args($args, $defaults);

    $number = $args['number'] ?? null;
    if ($args['number'] === -1)
      unset($args['number']);

    $query = new User_Action_Query($args);

    if (empty($query->items))
      return $number === 1 ? null : [];

    return $number === 1 ? $query->items[0] : $query->items;
  }

  public static function assign($args = []) {
    if (!isset($args['user_id'], $args['action'], $args['priority']))
      throw new MasterException("invalid.args");

    if (!in_array($args['action'], self::$actions))
      throw new MasterException("invalid.action");

    $args["uid"] = NanoId::generate();

    if (isset($args['data']))
      $args['data'] = self::to_string($args['data']);

    if (isset($args['config']))
      $args['config'] = self::to_string($args['config']);

    $created = (new User_Action_Query())->add_item($args);
    return [$created, $args["uid"]];
  }

  public static function update($id, $user_action) {
    if (isset($user_action['data']))
      $user_action['data'] = self::to_string($user_action['data']);

    if (isset($user_action['config']))
      $user_action['config'] = self::to_string($user_action['config']);

    $updated = (new User_Action_Query())->update_item($id, $user_action);
    return $updated;
  }

  public static function to_string($input) {
    if (empty($input))
      return null;

    return !is_string($input) ? json_encode($input) : $input;
  }
}
