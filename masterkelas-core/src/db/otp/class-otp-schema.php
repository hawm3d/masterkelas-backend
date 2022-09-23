<?php
namespace MasterKelas\Database;

use BerlinDB\Database\Schema;

/**
 * OTP Query Schema
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class OTP_Schema extends Schema
{
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
    "type" => [
      "name" => "type",
      "type" => "varchar",
      "length" => "100",
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "recipient" => [
      "name" => "recipient",
      "type" => "varchar",
      "length" => "100",
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "token" => [
      "name" => "token",
      "type" => "varchar",
      "length" => "255",
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "created_at" => [
      "name" => "created_at",
      "type" => "datetime",
      "date_query" => true,
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "expire_at" => [
      "name" => "expire_at",
      "type" => "datetime",
      "date_query" => true,
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "status" => [
      "name" => "status",
      "type" => "tinyint",
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
    "data" => [
      "name" => "data",
      "type" => "text",
      "unsigned" => true,
      "searchable" => true,
      "sortable" => true
    ],
  ];
}
