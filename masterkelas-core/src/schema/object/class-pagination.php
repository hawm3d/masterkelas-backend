<?php

namespace MasterKelas\Schema\Object;

/**
 * Pagination object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Pagination {
  public static function register() {
    self::pagination_type();
  }

  public static function pagination_type() {
    register_graphql_object_type("Pagination", [
      "description" => "Pagination attributes",
      "fields" => [
        "current" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Current page",
        ],
        "next" => [
          "type" => "Integer",
          "description" => "Next page",
        ],
        "previous" => [
          "type" => "Integer",
          "description" => "Previous page",
        ],
        "lastPage" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Last page",
        ],
        "perPage" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Number of items per page",
        ],
        "total" => [
          "type" => ["non_null" => "Integer"],
          "description" => "Number of all items",
        ],
      ],
    ]);
  }
}
