<?php

namespace MasterKelas\Model;

use MasterKelas\Database\Notification_Query;
use MasterKelas\Database\User_Action_Query;
use MasterKelas\Schema;

/**
 * User Model
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class User {
  public $wp_user;

  public $id;
  public $name;
  public $first_name;
  public $last_name;
  public $email;
  public $mobile;
  public $action_required;
  public $nationality_status;
  public $suspension;
  public $suspended;
  public $registered;
  public $region;
  public $policy;
  public $avatar;

  public $actions = [];
  public $notifications = [];

  public function __construct($wp_user) {
    if (is_int($wp_user))
      $wp_user = self::find_by(["id" => $wp_user]);

    if (!$wp_user || is_wp_error($wp_user))
      return;

    $this->wp_user = $wp_user;
    $this->id = $wp_user->ID;
    $this->first_name = $wp_user->first_name;
    $this->last_name = $wp_user->last_name;
    $this->email = $wp_user->email;
    $this->mobile = $wp_user->mobile;

    $this->init();
  }

  public function init() {
    $this->init_name();
    $this->init_suspension();

    $this->policy = $this->get_meta("policy");
    $this->nationality_status = $this->get_meta("nationality_status");
    $this->action_required = $this->get_meta("action_required");
  }

  public function actions($args = []) {
    $this->actions = new User_Action_Query($args);
    return $this->actions;
  }

  public function notifications($args = []) {
    $this->notifications = new Notification_Query($args);
    return $this->notifications;
  }

  public function init_name() {
    if ($this->first_name && $this->last_name) {
      $this->name = "$this->first_name $this->last_name";
    } else if ($this->first_name) {
      $this->name = $this->first_name;
    } else if ($this->last_name) {
      $this->name = $this->last_name;
    } else {
      $this->name = $this->mobile ?? $this->email;
    }
  }

  public function init_suspension() {
    $this->suspension = $this->get_meta("suspension");
    if ($this->suspension && isset($this->suspension['intensity']))
      $this->suspended = true;
    else
      $this->suspended = false;
  }

  public function get_id() {
    return Schema::toGlobalId("user", $this->id);
  }

  public function suspend_account($args = []) {
    $defaults = [
      "intensity" => "soft",
      "reason" => null,
      "action_id" => null,
      "lockdown" => null
    ];

    $args = wp_parse_args($args, $defaults);
    $saved = $this->update_meta("suspension", $args);
    if ($saved)
      $this->suspended = true;
  }

  public function is_active() {
    return !in_array($this->policy, ["disabled", "restricted", "blocked"]);
  }

  public static function get_meta_key($key) {
    return "masterkelas:{$key}";
  }

  public function get_meta($key) {
    return get_user_meta($this->id, $this->get_meta_key($key), true);
  }

  public function update_meta($key, $value, $prefix = true) {
    $result = update_user_meta($this->id, !$prefix ? $key : $this->get_meta_key($key), $value);
    if ($result && property_exists($this, $key))
      $this->$key = $value;

    return $result;
  }

  public function delete_meta($key, $value = '') {
    $result = delete_user_meta($this->id, $this->get_meta_key($key), $value);
    if ($result && property_exists($this, $key))
      $this->$key = null;

    return $result;
  }

  public static function find($args = []) {
    $defaults = [
      "number" => 1,
      "fields" => "ids"
    ];

    $args = wp_parse_args($args, $defaults);
    $users_ids = get_users($args);
    $users = [];

    if (empty($users_ids)) return $args['number'] > 1 ? [] : null;

    foreach ($users_ids as $user_id) {
      $users[] = get_userdata($user_id);
    }

    return $args['number'] > 1 ? $users : $users[0];
  }

  public static function find_by($query, $args = []) {
    $defaults = [
      "include" => [],
      "meta_query" => []
    ];

    $args = wp_parse_args($args, $defaults);

    foreach ($query as $field => $value) {
      switch ($field) {
        case 'id':
        case 'ID':
          $args['include'][] = $value;
          break;

        case 'email':
        case 'user_email':
          $args['search'] = $value;
          $args['search_columns'] = ['user_email'];
          break;

        case 'user_nicename':
        case 'user_login':
          $args['search'] = $value;
          $args['search_columns'] = [$field];
          break;

        default:
          $args['meta_query'][] = [
            "key" => $field,
            "value" => $value
          ];
          break;
      }
    }

    if (empty($args['include']))
      unset($args['include']);

    if (empty($args['meta_query']))
      unset($args['meta_query']);

    return self::find($args);
  }
}
