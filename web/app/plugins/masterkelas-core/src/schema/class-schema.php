<?php

namespace MasterKelas;

use GraphQLRelay\Relay;
use MasterKelas\Model\RateLimiter;
use MasterKelas\Schema\MasterContext;
use WPGraphQL\Registry\TypeRegistry;

/**
 * GraphQL Schema Access Class 
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage Mk
 * @author     Hamed Ataei <setayan.com@gmail.com>
 */
class Schema {

  const GLOBAL_ID_POS = 3;
  const GLOBAL_ID_KEY = "xI";

  public static function query(string $type_name, string $query_name, array $query_data, $config = []) {
    $resolve = $query_data['resolve'];
    $query_data["resolve"] = function ($root, $args, $context, $info) use ($resolve, $config) {
      self::middlewares($config, $context);
      return $resolve($root, $args, $context, $info);
    };

    add_action(
      get_graphql_register_action(),
      function (TypeRegistry $type_registry) use ($type_name, $query_name, $query_data) {
        $type_registry->register_field($type_name, $query_name, $query_data);
      },
      10
    );
  }

  public static function mutation(string $mutation_name, array $mutation_data, $config = []) {
    $mutateAndGetPayload = $mutation_data['mutate'];
    unset($mutation_data['mutate']);

    $mutation_data["mutateAndGetPayload"] = function ($input, $context, $info) use ($mutateAndGetPayload, $config) {
      self::middlewares($config, $context);
      return $mutateAndGetPayload($input, $context, $info);
    };

    add_action(
      get_graphql_register_action(),
      function (TypeRegistry $type_registry) use ($mutation_name, $mutation_data) {
        $type_registry->register_mutation($mutation_name, $mutation_data);
      },
      10
    );
  }

  public static function middlewares($config = [], $context) {
    $config['zone'] = $config['zone'] ?? "*";

    if (isset($config['throttle']))
      self::throttle($config['throttle']);

    if (isset($config['zone']))
      self::validate_zone($config['zone'], $context);

    if (isset($config['region']))
      self::validate_region($config['region'], $context);
  }

  public static function throttle($config) {
    if (!$config || empty($config) || !isset($config['action'], $config['limit'], $config['interval']))
      return;

    RateLimiter::throttle($config);
  }

  public static function validate_zone($zone, MasterContext $context = null) {
    switch (true) {
      case $zone === '*':
      case empty($zone):
        return;
        break;
      case $zone === 'guest' && !is_null($context->auth):
      case $zone === 'user' && is_null($context->auth):
        self::set_status(403);
        throw new MasterException("auth.{$zone}.only");
        break;
    }
  }

  public static function validate_region($region, MasterContext $context = null) {
    if (
      !empty($region)
      && ($region === 'restricted' || ($region === 'guest_restricted' && is_null($context->auth)))
      && !$context->region->is_allowed()
    ) {
      self::set_status(403);
      throw new MasterException("region.restricted");
    }

    return;
  }

  public static function set_status(Int $status) {
    add_filter('graphql_response_status_code', function () use ($status) {
      return $status;
    });
  }

  public static function append_headers($new_headers = []) {
    add_filter('graphql_response_headers_to_send', function ($headers) use ($new_headers) {
      return array_merge($headers, $new_headers);
    });
  }

  public static function toGlobalId($type, Int $id, $obf = true) {
    if ($obf)
      $id = OptimusId::encode($id);

    $encoded = Relay::toGlobalId($type, $id);
    if ($obf)
      $encoded = substr($encoded, 0, static::GLOBAL_ID_POS) . static::GLOBAL_ID_KEY . substr($encoded, static::GLOBAL_ID_POS);

    return $encoded;
  }

  public static function fromGlobalId(String $id, $obf = true) {
    if ($obf)
      $id = substr($id, 0, static::GLOBAL_ID_POS) . substr($id, static::GLOBAL_ID_POS + strlen(static::GLOBAL_ID_KEY));

    $decoded = Relay::fromGlobalId($id);
    $decoded['id'] = (int) ($decoded['id'] ?? 0);

    if ($obf && !empty($decoded['type']))
      $decoded['id'] = OptimusId::decode($decoded['id']);

    return $decoded;
  }
}
