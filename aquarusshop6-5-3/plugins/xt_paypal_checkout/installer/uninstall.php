<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */


defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/class.xt_paypal_checkout.php';

global $db, $store_handler;

// ########################### Westnavi
$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` LIKE 'xt_paypal_checkout%';");



// ########################### Tables

// cleanup order columns, dont drop ppc_order_id
$drops = array();
foreach($drops as $col)
{
    if ($this->_FieldExists($col, TABLE_ORDERS))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` DROP COLUMN `".$col."` ;");
    }
}
// cleanup order indexes
$drops = array();
$idxs = $db->GetArray('SHOW INDEX FROM '. TABLE_ORDERS);
foreach($idxs as $idx)
{
    if (in_array($idx['Key_name'], $drops))
    {
        $db->Execute("DROP INDEX `" . $idx['Key_name'] . "` ON `".TABLE_ORDERS."` ;");
    }
    if (in_array($idx['key_name'], $drops))
    {
        $db->Execute("DROP INDEX `" . $idx['key_name'] . "` ON `".TABLE_ORDERS."` ;");
    }
    if (in_array($idx['KEY_NAME'], $drops))
    {
        $db->Execute("DROP INDEX `" . $idx['KEY_NAME'] . "` ON `".TABLE_ORDERS."` ;");
    }
}

/** @var $plugin_id int */
$payment_methods = $db->GetAssoc("SELECT * FROM ".TABLE_PAYMENT." WHERE payment_code LIKE 'xt_paypal_checkout_%'");

if(is_array($payment_methods))
{
    $payment = new payment();
    foreach ($payment_methods as $payment_id => $payment_data)
    {
        $payment->setPosition('admin');
        if (method_exists($payment,'cleanupData')) { // v5 backwards compatiblility
            $payment->cleanupData($payment_id);
        }
    }
}

try
{
    $ppcp = paypal_checkout::getPaypalCheckout();
    $ppcp->deleteAllWebhooks();
}
catch(Exception $e)
{
    paypal_checkout::log('uninstall.php','','', $e);
}
