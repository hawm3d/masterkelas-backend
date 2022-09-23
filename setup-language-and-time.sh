#!/bin/bash

wp language core install fa_IR --activate
wp option update timezone_string "Asia/Tehran"
wp option update start_of_week 0