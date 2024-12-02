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

if (!$this->_FieldExists('ppcp_order_id', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `ppcp_order_id` VARCHAR(32) NULL DEFAULT NULL ,
           ADD INDEX `idx_ppcp_order_id` (`ppcp_order_id`) ;");
}
if (!$this->_FieldExists('ppcp_transaction_id', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `ppcp_transaction_id` VARCHAR(32) NULL DEFAULT NULL ,
           ADD INDEX `idx_ppcp_transaction_id` (`ppcp_transaction_id`) ;");
}
if (!$this->_FieldExists('ppcp_order_status', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `ppcp_order_status` VARCHAR(32) NULL DEFAULT NULL ,
           ADD INDEX `idx_ppcp_order_status` (`ppcp_order_status`) ;");
}
if (!$this->_FieldExists('ppcp_express', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `ppcp_express` TINYINT(1) NULL DEFAULT 0 ,
           ADD INDEX `idx_ppcp_express` (`ppcp_express`) ;");
}

if(!$this->_FieldExists('ppcp_customer_id', TABLE_CUSTOMERS))
{
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS." ADD COLUMN `ppcp_customer_id` VARCHAR(10) NULL DEFAULT NULL;");
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS." ADD INDEX `idx_ppcp_customer_id` (`ppcp_customer_id` DESC);");
}

$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_paypal_checkout_signup';");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`,`handler` ,`iconCls`)
        VALUES (NULL , 'xt_paypal_checkout_signup', '', '../plugins/xt_paypal_checkout/signup/xt_paypal_onboarding.php', '', '1', 'config', 'I', 'W','clickHandler2','fab fa-paypal');");

$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`iconCls` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_paypal_checkout_transactions', 'fa fa-exchange', '&plugin=xt_paypal_checkout', 'adminHandler.php', '4000', 'order', 'I', 'W');");
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

$db->Execute('CREATE TABLE IF NOT EXISTS  `'.DB_PREFIX.'_additional_payments` (
    `ap_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ap_number` VARCHAR(32) NULL,
    `ap_order_id` INT UNSIGNED NULL,
    `ap_customer_id` INT UNSIGNED  NULL,
    `ap_address_id` INT UNSIGNED NULL,
    `ap_status` TINYINT UNSIGNED NULL DEFAULT 1,
    `ap_description` TEXT NULL,
    `ap_amount_total` DECIMAL(10,2) NULL DEFAULT 0,
    `ap_tax_class_id` TINYINT UNSIGNED NULL DEFAULT 0,
    `ap_times_sent` TINYINT UNSIGNED NULL DEFAULT 0,
    `ap_time_sent` TIMESTAMP NULL DEFAULT NULL,
    `ap_ppcp_order_id` VARCHAR(32) NULL,
    `ap_ppcp_order_status` VARCHAR(32) NULL,
    `ap_ppcp_transaction_id` VARCHAR(32) NULL,

    PRIMARY KEY (`ap_id`),
    INDEX `idx_number` (`ap_number` ASC) ,
    INDEX `idx_order` (`ap_order_id` DESC) ,
    INDEX `idx_customer` (`ap_customer_id` ASC) ,
    INDEX `idx_address` (`ap_address_id` ASC) ,
    INDEX `idx_status` (`ap_status` ASC) ,
    INDEX `idx_time_sent` (`ap_time_sent` DESC),
    INDEX `idx_ppcp_order_id` (`ap_ppcp_order_id` DESC),
    INDEX `idx_ppcp_order_status` (`ap_ppcp_order_status` DESC),
    INDEX `idx_ppcp_transaction_id` (`ap_ppcp_transaction_id` DESC)
); ');

if (defined('TABLE_SHIPPER') && !$this->_FieldExists('shipper_code_ppcp', TABLE_SHIPPER))
{
    $db->Execute("ALTER TABLE `" . TABLE_SHIPPER . "` ADD COLUMN `shipper_code_ppcp` VARCHAR(128) NULL DEFAULT NULL;");
}

$db->Execute("DELETE FROM  `".DB_PREFIX."_acl_nav` WHERE `text` = 'xt_additional_payment'; " );
$db->Execute("INSERT INTO  `".DB_PREFIX."_acl_nav` (`text`, `plugin_code`, `icon`, `url_i`, `url_d`, `url_h`, `sortorder`, `parent`, `type`, `navtype`, `iconCls`) VALUES ('xt_additional_payment', '', 'images/icons/money_euro.png', '&plugin=xt_paypal_checkout&pg=overview', 'adminHandler.php', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/3317137413', '5', 'ordertab', 'I', 'W', 'fa fa-money-bill' ); ");


/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */
ppcInstallPaymentTypes($product_id, $_plugin_code, $this->mode);

//ppcInstallMailTemplates();

ppcInstallPaymentIcon();

ppcFixDependencies();

ppcUpdateRefundStatus();

ppcpInstallMailTemplates();

ppcpCopyPaymentLinkTemplates();

ppcClearLanguageCache();

if (!file_exists(_SRV_WEBROOT.'.well-known')) {
    mkdir(_SRV_WEBROOT.'.well-known', 0777);
}

copy(_SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/dependencies/.well-known/apple-developer-merchantid-domain-association'   , _SRV_WEBROOT.'.well-known/apple-developer-merchantid-domain-association');

if (!file_exists(_SRV_WEBROOT.'.well-known/apple-developer-merchantid-domain-association'))
{
    $hint = "Installer tried to copy to <i>.well-known/apple-developer-merchantid-domain-association</i> file but failed.<br />You have to copy manually<br/><b>plugins/xt_paypal_checkout/installer/dependencies/.well-known/apple-developer-merchantid-domain-association</b><br/> to <br/><b>.well-known/apple-developer-merchantid-domain-association</b>";

    if (!file_exists(_SRV_WEBROOT.'.well-known')) {
        $hint .= '<br> Could not create folder .well-known in shop main directory You have to create it manually.';
    }

    echo "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    echo $hint;
    echo "</div>";
}

