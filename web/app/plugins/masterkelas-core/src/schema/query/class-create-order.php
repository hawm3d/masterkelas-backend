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
          $result = false;

          try {
            $user = $context->auth->user;
            if (wcs_user_has_subscription($user->id, '', 'active'))
              throw new \Exception("sub.exists");

            $sub_id = null;
            $subscription = null;
            $encoded_sub_id = (int) sanitize_text_field($args['subId'] ?? "");
            $subs = Subs::get();
            foreach ($subs as $sub) {
              if ($sub['id'] === $encoded_sub_id) {
                $sub_id = OptimusId::subscription()->decode($encoded_sub_id);
                $subscription = wc_get_product_object("subscription", $sub_id);
              }
            }

            if (!$sub_id || !$subscription || !\WC_Subscriptions_Product::is_subscription($subscription)) {
              throw new \Exception("invalid.sub.id");
            }

            $user = get_user_by('ID', $user->id);
            $order = wc_create_order(['customer_id' => $user->ID]);
            if (is_wp_error($order)) {
              throw new \Exception("invalid.user.id");
            }

            $address = [
              "first_name" => $user->first_name,
              "last_name" => $user->last_name,
              "email" => $user->user_email,
              "address_1" => get_user_meta($user->ID, 'billing_address_1', true),
              "address_2" => get_user_meta($user->ID, 'billing_address_2', true),
              "city" => get_user_meta($user->ID, 'billing_city', true),
              "state" => get_user_meta($user->ID, 'billing_state', true),
              "postcode" => get_user_meta($user->ID, 'billing_postcode', true),
              "country" => get_user_meta($user->ID, 'billing_country', true),
            ];
            $order->set_address($address, 'billing');
            $order->set_address($address, 'shipping');
            $order->add_product($subscription, 1);
            $order->calculate_totals();

            $start_date = wcs_get_datetime_utc_string(wcs_get_objects_property($order, 'date_created'));
            $sub = wcs_create_subscription([
              'start_date' => get_date_from_gmt($start_date),
              'order_id' => $order->get_id(),
              'customer_id' => $order->get_user_id(),
              'status' => 'pending',
              'billing_period' => \WC_Subscriptions_Product::get_period($subscription),
              'billing_interval' => \WC_Subscriptions_Product::get_interval($subscription)
            ]);

            if (is_wp_error($sub)) {
              throw new \Exception();
            }

            $dates = [
              "trial_end" => \WC_Subscriptions_Product::get_trial_expiration_date($subscription, $start_date),
              "next_payment" => \WC_Subscriptions_Product::get_first_renewal_payment_date($subscription, $start_date),
              "end" => \WC_Subscriptions_Product::get_expiration_date($subscription, $start_date),
            ];

            $sub->add_product($subscription, 1);
            $sub->update_dates($dates);
            $sub->calculate_totals();

            $order->update_status('completed', "", true);
            $sub->update_status('active', "", true);

            $order->update_meta_data("_mk_order_region", $context->region);
            $order->save();

            $result = true;
          } catch (\Throwable $th) {
            graphql_debug($th);
            //throw $th;
          }

          return $result;
        }
      ],
      ["zone" => "user"]
    );
  }

  public static function create_by_variable_product($args, MasterContext $context) {
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
}
