<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;
$sql = "DELETE FROM ".TABLE_CONFIGURATION_PAYMENT. " WHERE `config_key`='XT_PAYPAL_PLUS_PPP_COMMENT_SELECTOR'";
$db->Execute($sql);
