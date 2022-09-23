<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Schema;

/**
 * User Action Query Schema
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class User_Action_Schema extends Schema {
  public $columns = [
    "id" => [
      "name" => "id",
      "type" => "bigint",
      "length" => "20",
      "unsigned" => true,
      "extra" => "auto_increment",
      "primary" => true,
      "sortable" => true
    ],
    "uid" => [
      "name" => "uid",
      "type" => "varchar",
      "length" => "100",
      "searchable" => true,
      "sortable" => true,
      "cache_key" => true
    ],
    "user_id" => [
      "name" => "user_id",
      "type" => "bigint",
      "length" => "20",
      "unsigned" => true,
      "sortable" => true,
      "cache_key" => true,
    ],
    "session_id" => [
      "name" => "session_id",
      "type" => "bigint",
      "length" => "20",
      "default" => 0,
      "unsigned" => true,
      "sortable" => true,
      "cache_key" => true,
    ],
    "creator_id" => [
      "name" => "creator_id",
      "type" => "bigint",
      "length" => "20",
      "default" => 0,
      "unsigned" => true,
      "sortable" => true,
    ],
    "action" => [
      "name" => "action",
      "type" => "mediumtext",
      "searchable" => true,
      "sortable" => true
    ],
    "priority" => [
      "name" => "priority",
      "type" => "tinyint",
      "default" => 100,
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "data" => [
      "name" => "data",
      "type" => "text",
      "allow_null" => true,
      "default" => null,
      "searchable" => true,
      "sortable" => true
    ],
    "config" => [
      "name" => "config",
      "type" => "text",
      "allow_null" => true,
      "default" => null,
      "searchable" => true,
      "sortable" => true
    ],
    "status" => [
      "name" => "status",
      "type" => "tinyint",
      "default" => 0,
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "created_at" => [
      "name" => "created_at",
      "type" => "datetime",
      "default" => "",
      "created" => true,
      "date_query" => true,
      "sortable" => true
    ],
    "updated_at" => [
      "name" => "updated_at",
      "type" => "datetime",
      "default" => "",
      "modified" => true,
      "date_query" => true,
      "sortable" => true
    ],
  ];
}
