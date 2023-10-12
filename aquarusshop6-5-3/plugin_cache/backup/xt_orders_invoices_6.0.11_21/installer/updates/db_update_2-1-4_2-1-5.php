<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='invoice_status' AND TABLE_NAME='".TABLE_ORDERS_INVOICES."'");
if ($colExists)
{
    $db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` CHANGE COLUMN `invoice_status` `invoice_status` tinyint(4) NOT NULL DEFAULT 1 ");
}
