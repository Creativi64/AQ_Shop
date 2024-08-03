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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/functions.php';

global $db;

/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */

$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_paypal_checkout_transactions';");
$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_paypal_checkout_refunds';");


$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`iconCls` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_paypal_checkout_transactions', 'fas fa-exchange-alt', '&plugin=xt_paypal_checkout', 'adminHandler.php', '4000', 'order', 'I', 'W');");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`iconCls` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_paypal_checkout_refunds', 'fa fa-undo', '&plugin=xt_paypal_checkout', 'adminHandler.php', '4000', 'order', 'I', 'W');");

$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_PAYPAL_CHECKOUT_REFUNDS . " (
          `orders_id` int(11) UNSIGNED NOT NULL,   
          `ppcp_order_id` varchar(64) NOT NULL,  
          `ppcp_refund_id` varchar(64)  NOT NULL, 
          `ppcp_amount` decimal(15,2) default 0,
          `ppcp_memo` varchar(255),
          `date_added` datetime default NULL,
        INDEX `idx_orders_id`  (`orders_id`),
        INDEX `idx_ppcp_order_id` (`ppcp_order_id`),
        INDEX `idx_ppcp_refund_id` (`ppcp_refund_id`)
         );");