<?php

namespace MasterKelas\Schema\Query;

use Carbon\Carbon;
use MasterKelas\MasterException;
use MasterKelas\OptimusId;
use MasterKelas\Schema;
use MasterKelas\Schema\MasterContext;

/**
 * CreateOrder
 *
 * @since      1.0.0
 * @package    MasterKelas
 * @subpackage MasterKelas/Schema/Object
 * @author     hawm3d <setayan.com@gmail.com>
 */
class CreateOrder {
  public static function register() {
    self::create_order_field();
  }

  public static function create_order_field() {
    Schema::query(
      'RootQuery',
      "createOrder",
      [
        "type" => "Boolean",
        "description" => "Create Order",
        "args" => [
          "subId" => [
            "type" => "Integer",
            "description" => "Sub Id"
          ]
        ],
        "resolve" => function ($_, $args, MasterContext $context) {
          $user = $context->auth->user;
          $result = false;
          $subscription = [];
          $subId = OptimusId::subscription()->decode((int) sanitize_text_field($args['subId'] ?? ""));

          if (wcs_user_has_subscription($user->id, '', 'active'))
            throw new MasterException("sub.exists");

          try {
            $product = get_page_by_path('subscriptions', OBJECT, 'product');
            if (empty($product)) throw new \Exception();

            $product = wc_get_product($product);
            $vars = $product->get_available_variations();
            foreach ($vars as $sub) {
              if ((int) ($sub['variation_id']) === $subId)
                $subscription['variation'] = $sub['attributes'];
            }

            // graphql_debug($subscription);
            if (!$subscription || !count($subscription)) throw new \Exception("sub.invalid.var");

            $varProduct = new \WC_Product_Variation($subId);
            if (!function_exists('wc_create_order') || !function_exists('wcs_create_subscription') || !class_exists('WC_Subscriptions_Product')) {
              throw new \Exception();
            }

            $order = wc_create_order(array('customer_id' => $user->id));

            if (is_wp_error($order)) {
              throw new \Exception();
            }

            $user = get_user_by('ID', $user->id);

            $fname     = $user->first_name;
            $lname     = $user->last_name;
            $email     = $user->user_email;
            $address_1 = get_user_meta($user->ID, 'billing_address_1', true);
            $address_2 = get_user_meta($user->ID, 'billing_address_2', true);
            $city      = get_user_meta($user->ID, 'billing_city', true);
            $postcode  = get_user_meta($user->ID, 'billing_postcode', true);
            $country   = get_user_meta($user->ID, 'billing_country', true);
            $state     = get_user_meta($user->ID, 'billing_state', true);

            $address         = array(
              'first_name' => $fname,
              'last_name'  => $lname,
              'email'      => $email,
              'address_1'  => $address_1,
              'address_2'  => $address_2,
              'city'       => $city,
              'state'      => $state,
              'postcode'   => $postcode,
              'country'    => $country,
            );

            $order->set_address($address, 'billing');
            $order->set_address($address, 'shipping');
            $order->add_product($varProduct, 1, $subscription);
            $order->calculate_totals();

            $sub = wcs_create_subscription(array(
              'order_id' => $order->get_id(),
              'status' => 'pending', // Status should be initially set to pending to match how normal checkout process goes
              'billing_period' => \WC_Subscriptions_Product::get_period($product),
              'billing_interval' => \WC_Subscriptions_Product::get_interval($product)
            ));

            if (is_wp_error($sub)) {
              throw new \Exception();
            }

            // Modeled after WC_Subscriptions_Cart::calculate_subscription_totals()
            $start_date = gmdate('Y-m-d H:i:s');
            // Add product to subscription
            $sub->add_product($varProduct, 1, $subscription);

            $dates = array(
              'trial_end'    => \WC_Subscriptions_Product::get_trial_expiration_date($product, $start_date),
              'next_payment' => \WC_Subscriptions_Product::get_first_renewal_payment_date($product, $start_date),
              'end'          => Carbon::now()->addDays($subscription['variation']['attribute_pa_mk-subscription'])->format("Y-m-d H:i:s"),
            );

            $sub->update_dates($dates);
            $sub->calculate_totals();

            // Update order status with custom note
            $order->update_status('completed', "", true);
            // Also update subscription status to active from pending (and add note)
            $sub->update_status('active', "", true);
            $result = true;
          } catch (\Throwable $th) {
            //throw $th;
          }

          return $result;
        }
      ],
      ["zone" => "user"]
    );
  }
}
