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

$pid = $db->GetOne("SELECT payment_id FROM ".TABLE_PAYMENT." WHERE payment_code = 'xt_paypal_checkout_giropay'");

if($pid)
{
    $tmp_payment = new payment();
    $tmp_payment->position = 'admin';
    $tmp_payment->_unset($pid);


    $output = "<div style='padding:10px; border:1px solid #005da2; background: #AFD0E1; font-size:2em'>";
    $output .= " Die Zahlungsweise Giropay (xt_paypal_checkout_giropay) wurde entfernt.<br />
  <a href='https://developer.paypal.com/docs/checkout/apm/' target='pp-giropay-abschaltung' style='color: #337ab7'>Einstellung von Giropay</a>";
    $output .= "</div><br>";
}
