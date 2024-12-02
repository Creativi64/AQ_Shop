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
$exist = $db->GetOne('SELECT 1 FROM '.TABLE_PAYMENT. " WHERE payment_code = 'xt_paypal_checkout_applepay'");
$mode = $exist ? 'update' : 'insert';
ppcInstallPaymentTypes($product_id, $_plugin_code, $mode, ['applepay']);

if (!file_exists(_SRV_WEBROOT.'.well-known')) {
    mkdir(_SRV_WEBROOT.'.well-known', 0777);
}

copy(_SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/dependencies/.well-known/apple-developer-merchantid-domain-association'   , _SRV_WEBROOT.'.well-known/apple-developer-merchantid-domain-association');


if (!file_exists(_SRV_WEBROOT.'.well-known/apple-developer-merchantid-domain-association'))
{
    $hint = "Installer tried to copy to <i>.well-known/apple-developer-merchantid-domain-association</i> file but failed.<br />You have to copy manually<br/><b>plugins/xt_paypal_checkout/installer/dependencies/.well-known/apple-developer-merchantid-domain-association</b><br/> to <br/><b>.well-known/apple-developer-merchantid-domain-association</b>";

    if (!file_exists(_SRV_WEBROOT.'.well-known')) {
        $hint .= '<br> Could not create folder .well-known in shop main directory. You have to create it manually.';
    }

    if(empty($output)) $output = '';
    $output .= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>" . $hint. "</div>";
}
