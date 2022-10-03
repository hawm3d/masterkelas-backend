<?php

namespace MasterKelas\Schema\Query;

use MasterKelas\OptimusId;
use MasterKelas\RemoteAddress;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * Subs query
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class Subs {
  public static function register() {
    self::subs();
  }

  public static function subs() {
    Schema::query(
      'RootQuery',
      "Subs",
      [
        "type" => ["list_of" => "Subscription"],
        "description" => "Subscriptions",
        "resolve" => function ($_, $__, MasterContext $context) {
          try {
            return self::get();
          } catch (\Throwable $th) {
            graphql_debug($th);
          }

          return [];
        }
      ],
    );
  }

  public static function get() {
    $subs = self::get_subscriptions();
    if (empty($subs))
      $subs = self::get_variable_subscriptions();

    return $subs;
  }

  public static function get_subscriptions() {
    $subs = [];
    $subs_ids = \MasterKelas\Admin::get_option("subscriptions");
    if (empty($subs_ids)) return $subs;

    foreach ($subs_ids as $sub_id) {
      $sub = wc_get_product_object("subscription", $sub_id);
      if ($sub && \WC_Subscriptions_Product::is_subscription($sub)) {
        $period = \WC_Subscriptions_Product::get_period($sub);
        $length = intval(\WC_Subscriptions_Product::get_length($sub));
        $price = intval(\WC_Subscriptions_Product::get_price($sub));
        $days = self::period_to_day($period) * $length;

        if ($days > 0 && $price > 0)
          $subs[] = [
            "id" => OptimusId::subscription()->encode((int) $sub->id),
            "length" => $length,
            "price" => $price,
            "period" => $period,
            "days" => $days
          ];
      }
    }

    return $subs;
  }

  public static function period_to_day($period) {
    switch ($period) {
      case 'year':
        return 365;
        break;

      case 'month':
        return 30;
        break;

      case 'week':
        return 7;
        break;

      case 'day':
      default:
        return 1;
        break;
    }
  }

  public static function get_variable_subscriptions() {
    $subs = [];
    $product = get_page_by_path('subscriptions', OBJECT, 'product');
    if (empty($product)) return $subs;

    $product = wc_get_product($product);
    $vars = $product->get_available_variations();
    foreach ($vars as $sub) {
      $subs[] = [
        "id" => OptimusId::subscription()->encode((int) $sub['variation_id']),
        "days" => (int) ($sub['attributes']['attribute_pa_mk-subscription'] ?? 30),
        "price" => (int) ($sub['display_price'] ?? 0)
      ];
    }

    return $subs;
  }
}
