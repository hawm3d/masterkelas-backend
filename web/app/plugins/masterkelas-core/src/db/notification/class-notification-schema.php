<?php

namespace MasterKelas\Database;

use BerlinDB\Database\Schema;

/**
 * Notification Query Schema
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Notification_Schema extends Schema {
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
    "uuid" => [
      "name" => "uuid",
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
    "action_id" => [
      "name" => "action_id",
      "type" => "bigint",
      "length" => "20",
      "default" => 0,
      "unsigned" => true,
      "sortable" => true,
      "cache_key" => true,
    ],
    "type" => [
      "name" => "type",
      "type" => "mediumtext",
      "searchable" => true,
      "sortable" => true
    ],
    "template" => [
      "name" => "type",
      "type" => "mediumtext",
      "allow_null" => true,
      "default" => null,
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
    "read_at" => [
      "name" => "read_at",
      "type" => "datetime",
      "allow_null" => true,
      "default" => null,
      "date_query" => true,
      "sortable" => true
    ],
  ];
}
