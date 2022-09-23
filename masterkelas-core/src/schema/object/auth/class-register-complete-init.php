<?php

namespace MasterKelas\Schema\Object;

/**
 * Complete Registration object type
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class RegisterCompleteInit {
  public static function register() {
    self::complete_registration_type();
  }

  public static function complete_registration_type() {
    register_graphql_object_type("RegisterCompleteInput", [
      "description" => "Complete Registration input object",
      "fields" => [
        "type" => [
          "type" => "String",
          "description" => "Input type",
        ],
        "status" => [
          "type" => "Integer",
          "description" => "Input status",
        ],
        "data" => [
          "type" => "String",
          "description" => "Input data",
        ],
      ],
    ]);

    register_graphql_object_type("RegisterCompleteInit", [
      "description" => "Complete Registration object",
      "fields" => [
        "inputs" => [
          "type" => ["list_of" => "RegisterCompleteInput"],
          "description" => "Inputs and fields of complete registration form",
        ],
      ],
    ]);
  }
}
