<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=1 WHERE config_key='XT_COUPONS_CHECKOUT_PAGE' AND config_value='true';");
$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=0 WHERE config_key='XT_COUPONS_CHECKOUT_PAGE' AND config_value='false';");

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=1 WHERE config_key='XT_COUPONS_CART_PAGE' AND config_value='true';");
$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=0 WHERE config_key='XT_COUPONS_CART_PAGE' AND config_value='false';");

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=1 WHERE config_key='XT_COUPONS_LOGIN' AND config_value='true';");
$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=0 WHERE config_key='XT_COUPONS_LOGIN' AND config_value='false';");

$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=1 WHERE config_key='XT_COUPONS_USE_LEFTOVER' AND config_value='true';");
$db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value=0 WHERE config_key='XT_COUPONS_USE_LEFTOVER' AND config_value='false';");
