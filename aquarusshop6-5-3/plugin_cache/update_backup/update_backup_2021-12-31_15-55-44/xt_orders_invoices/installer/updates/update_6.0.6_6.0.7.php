<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;

if($this->_FieldExists('invoice_fistname',TABLE_ORDERS_INVOICES))
{
    $db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` CHANGE COLUMN `invoice_fistname` `invoice_firstname` varchar(64) DEFAULT NULL;");
}
else if($this->_FieldExists('invoice_firstname',TABLE_ORDERS_INVOICES))
{
    $db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` CHANGE COLUMN `invoice_firstname` `invoice_firstname` varchar(64) DEFAULT NULL;");
}
else
    $db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES."` ADD COLUMN `invoice_firstname` varchar(64) DEFAULT NULL;");


