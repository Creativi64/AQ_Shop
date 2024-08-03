<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;

$db->Execute("UPDATE `".TABLE_LANGUAGE_CONTENT."` SET  `class` = 'both' WHERE `language_key` = 'XT_ORDERS_INVOICES_TEMPLATES_TEXT_MISSED';");
