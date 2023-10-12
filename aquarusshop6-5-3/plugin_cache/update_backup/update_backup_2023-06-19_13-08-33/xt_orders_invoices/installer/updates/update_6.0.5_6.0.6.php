<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;

if(!$this->_FieldExists('invoice_company',TABLE_ORDERS_INVOICES))
{
    $db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` ADD COLUMN `invoice_company` varchar(64) DEFAULT NULL AFTER `customers_id`;");
}

$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` CHANGE COLUMN `invoice_firstname` `invoice_firstname` varchar(64) DEFAULT NULL;");
$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` CHANGE COLUMN `invoice_lastname` `invoice_lastname` varchar(64) DEFAULT NULL;");
