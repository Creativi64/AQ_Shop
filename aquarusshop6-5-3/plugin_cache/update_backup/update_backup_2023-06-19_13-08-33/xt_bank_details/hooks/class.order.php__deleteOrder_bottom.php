<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/constants.php';

global $db;

$sql = "DELETE FROM ".TABLE_BAD_ORDER_BANK_DETAILS." WHERE ".COL_BAD_ORDERS_ID."=?";
$db->Execute($sql, array($orders_id));