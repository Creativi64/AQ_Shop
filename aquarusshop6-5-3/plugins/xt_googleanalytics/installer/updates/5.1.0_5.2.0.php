<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$upd_keys = ['XT_GOOGLE_ANALYTICS_ECOM', 'XT_GOOGLE_ANALYTICS_ANON', 'XT_GOOGLE_ANALYTICS_DO_NOT_TRACK'];
foreach ($upd_keys as $key)
{
    $db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value = 1 WHERE config_key = ? and config_value = 'true' ", [$key]);
    $db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value = 0 WHERE config_key = ? and config_value = 'false' ", [$key]);
}
