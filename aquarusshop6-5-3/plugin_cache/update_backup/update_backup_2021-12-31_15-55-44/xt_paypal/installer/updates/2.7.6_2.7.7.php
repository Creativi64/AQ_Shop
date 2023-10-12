<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('UPDATE '.TABLE_CONFIGURATION_PAYMENT." SET config_value='autodetect', type='hidden' WHERE config_key='XT_PAYPAL_SSL_VERSION'");
$db->Execute('UPDATE '.TABLE_CONFIGURATION_PAYMENT." SET config_value='autodetect', type='hidden' WHERE config_key='XT_PAYPAL_CIPHER_LIST'");
