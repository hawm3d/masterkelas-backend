#!/bin/bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Language
source ${SCRIPT_DIR}/setup-language-and-time.sh

# Setup theme
source ${SCRIPT_DIR}/setup-theme.sh

# WP GraphQL
source ${SCRIPT_DIR}/setup-wp-graphql.sh

# Woo
source ${SCRIPT_DIR}/setup-woocommerce.sh

# Woo Sub
source ${SCRIPT_DIR}/setup-woocommerce-subscriptions.sh

# LearnDash
source ${SCRIPT_DIR}/setup-sfwd-lms.sh

# LearnDash Woo
source ${SCRIPT_DIR}/setup-learndash-woocommerce.sh

# Seo Rank Math
source ${SCRIPT_DIR}/setup-seo-rank-math.sh

# Redis Cache Pro
source ${SCRIPT_DIR}/setup-redis-cache-pro.sh

# Core
source ${SCRIPT_DIR}/setup-core.sh