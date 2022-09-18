<?php
namespace MasterKelas\Schema\Input;

/**
 * OTP Input fields
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class OTP
{
  public static function register() {
    register_graphql_input_type(
      "OTPInput",
      [
        "description" => "Fields for requesting OTP",
        "fields" => [
          
        ]
      ]
    );
  }

}