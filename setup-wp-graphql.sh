#!/bin/bash

wp plugin is-installed wp-graphql && wp plugin activate wp-graphql
wp option update wp-graphql_allow_tracking "no"
wp option update wp-graphql_tracking_notice "hide"
wp option update wp-graphql_tracking_skipped "yes"
wp option update graphql_general_settings '{"graphql_endpoint":"gql","restrict_endpoint_to_logged_in_users":"off","batch_queries_enabled":"off","batch_limit":"10","query_depth_enabled":"off","query_depth_max":"10","graphiql_enabled":"on","show_graphiql_link_in_admin_bar":"on","delete_data_on_deactivate":"on","debug_mode_enabled":"off","tracing_enabled":"off","tracing_user_role":"administrator","query_logs_enabled":"off","query_log_user_role":"administrator","public_introspection_enabled":"on"}' --format=json
