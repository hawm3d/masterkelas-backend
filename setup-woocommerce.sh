#!/bin/bash

wp plugin is-installed woocommerce && wp plugin activate woocommerce
wp option update woocommerce_allow_tracking "no"
wp option update woocommerce_currency "IRT"
wp option update woocommerce_currency_pos "left"
wp option update woocommerce_default_country "IR:THR"
wp option update woocommerce_price_num_decimals "0"
wp option update woocommerce_enable_guest_checkout "no"
wp option update woocommerce_enable_checkout_login_reminder "yes"
wp option update woocommerce_enable_signup_and_login_from_checkout "yes"
wp option update woocommerce_registration_generate_password "no"
