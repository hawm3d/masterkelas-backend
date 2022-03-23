<?php
namespace MasterKelas;

/**
 * The core graphql class.
 *
 * Graphql schema, types and context
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class GraphQL
{
  public static function hooks() {
    add_action('graphql_register_types', [__CLASS__, 'init'], 10, 1);
  }

  public static function init() {
  }
}
