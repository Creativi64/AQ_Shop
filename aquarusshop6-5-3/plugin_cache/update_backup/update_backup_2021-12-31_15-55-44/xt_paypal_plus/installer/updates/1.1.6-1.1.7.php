<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DELETE FROM ".TABLE_CONFIGURATION_PAYMENT."  WHERE config_key='XT_PAYPAL_PLUS_ACCOUNT_MAIL'");