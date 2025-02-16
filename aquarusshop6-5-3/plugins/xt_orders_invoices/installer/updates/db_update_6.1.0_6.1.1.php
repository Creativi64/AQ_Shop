<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

if (!$this->_FieldExists('profile_e_invoice', TABLE_CUSTOMERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_CUSTOMERS . "` ADD COLUMN `profile_e_invoice` tinyint(1) NULL DEFAULT 0");
}

if (!$this->_FieldExists('customer_gln', TABLE_CUSTOMERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_CUSTOMERS . "` ADD COLUMN `customer_gln` varchar(32) NULL DEFAULT NULL");
}

if (!$this->_FieldExists('customer_leitweg_id', TABLE_CUSTOMERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_CUSTOMERS . "` ADD COLUMN `customer_leitweg_id` varchar(50) NULL DEFAULT NULL");
}


if (!$this->_FieldExists('profile_e_invoice', TABLE_CUSTOMERS_STATUS))
{
    $db->Execute("ALTER TABLE `" . TABLE_CUSTOMERS_STATUS . "` ADD COLUMN `profile_e_invoice` tinyint(1) NULL DEFAULT 0");
}


if (!$this->_FieldExists('customer_leitweg_id', TABLE_ORDERS_INVOICES))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS_INVOICES . "` ADD COLUMN `customer_leitweg_id` varchar(50) NULL DEFAULT NULL");
}

