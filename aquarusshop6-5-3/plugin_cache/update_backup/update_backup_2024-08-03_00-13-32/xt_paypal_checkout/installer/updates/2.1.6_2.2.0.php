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

/* werte einsammeln */
$express_settings = $db->GetArray('SELECT config_value, shop_id FROM '.TABLE_CONFIGURATION_PAYMENT." WHERE config_key = 'XT_PAYPAL_CHECKOUT_PAYPAL_EXPRESS_ACTIVATED'");

/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */
ppcInstallPaymentTypes($product_id, $_plugin_code, $this->mode, ['paypal']);

/* werte anwenden */
foreach ($express_settings as $es)
{
    $db->Execute('UPDATE '.TABLE_CONFIGURATION_PAYMENT." SET config_value = ? WHERE shop_id = ? AND config_key = 'XT_PAYPAL_CHECKOUT_PAYPAL_EXPRESS_CART_PAGE_ACTIVATED'",    [$es['config_value'], $es['shop_id'] ]);
    $db->Execute('UPDATE '.TABLE_CONFIGURATION_PAYMENT." SET config_value = ? WHERE shop_id = ? AND config_key = 'XT_PAYPAL_CHECKOUT_PAYPAL_EXPRESS_PRODUCT_PAGE_ACTIVATED'", [$es['config_value'], $es['shop_id'] ]);
}
