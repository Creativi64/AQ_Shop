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

$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_mybank'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['mybank']);

$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_ideal'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['ideal']);

$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_p24'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['p24']);

$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_blik'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['blik']);

ppcpInstallMailTemplates();

ppcpCopyPaymentLinkTemplates();

ppcInstallPaymentIcon();

ppcClearLanguageCache();