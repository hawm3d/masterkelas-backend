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
    self::Subs();
  }

  public static function Subs() {
    Schema::query(
      'RootQuery',
      "Subs",
      [
        "type" => ["list_of" => "Subscription"],
        "description" => "Subscriptions",
        "resolve" => function ($_, $__, MasterContext $context) {
          $subs = [];
          try {
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
          } catch (\Throwable $th) {
            //throw $th;
          }

          return $subs;
        }
      ],
    );
  }
}
