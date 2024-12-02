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
    `ap_ppcp_transaction_id` VARCHAR(32) NULL,

    PRIMARY KEY (`ap_id`),
    INDEX `idx_number` (`ap_number` ASC) ,
    INDEX `idx_order` (`ap_order_id` DESC) ,
    INDEX `idx_customer` (`ap_customer_id` ASC) ,
    INDEX `idx_address` (`ap_address_id` ASC) ,
    INDEX `idx_status` (`ap_status` ASC) ,
    INDEX `idx_time_sent` (`ap_time_sent` DESC),
    INDEX `idx_transaction_id` (`ap_ppcp_transaction_id` DESC)
) ENGINE=InnoDB; ');

$db->Execute("DELETE FROM  `".DB_PREFIX."_acl_nav` WHERE `text` = 'xt_additional_payment'; " );
$db->Execute("INSERT INTO  `".DB_PREFIX."_acl_nav` (`text`, `plugin_code`, `icon`, `url_i`, `url_d`, `url_h`, `sortorder`, `parent`, `type`, `navtype`, `iconCls`) VALUES ('xt_additional_payment', '', 'images/icons/money_euro.png', '&plugin=xt_paypal_checkout&pg=overview', 'adminHandler.php', 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/3317137413', '5', 'ordertab', 'I', 'W', 'fa fa-money-bill' ); ");

// update card card fields vs form
/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */
ppcInstallPaymentTypes($product_id, $_plugin_code, 'update', ['card']);

$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_trustly'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['trustly']);

ppcpInstallMailTemplates();

ppcInstallPaymentIcon();

ppcClearLanguageCache();