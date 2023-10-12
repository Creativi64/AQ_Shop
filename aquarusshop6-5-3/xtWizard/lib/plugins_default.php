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

$default_plugins['ew_evelations_plugin'] = array('plugin'=>'ew_evelations_plugin','active'=>'1');
$default_plugins['xt_auto_cross_sell'] = array('plugin'=>'xt_auto_cross_sell','active'=>'1');
$default_plugins['xt_bank_details'] = array('plugin'=>'xt_bank_details','active'=>'1');
$default_plugins['xt_bestseller_products'] = array('plugin'=>'xt_bestseller_products','active'=>'1');
$default_plugins['xt_cleancache'] = array('plugin'=>'xt_cleancache','active'=>'1');
$default_plugins['xt_cookie_consent'] = array('plugin'=>'xt_cookie_consent','active'=>'0');
$default_plugins['xt_cross_selling'] = array('plugin'=>'xt_cross_selling','active'=>'1');
$default_plugins['xt_customersdiscount'] = array('plugin'=>'xt_customersdiscount','active'=>'1');
$default_plugins['xt_google_product_categories'] = array('plugin'=>'xt_google_product_categories','active'=>'1');
$default_plugins['xt_im_export'] = array('plugin'=>'xt_im_export','active'=>'1');
$default_plugins['xt_coupons'] = array('plugin'=>'xt_coupons','active'=>'1');
$default_plugins['xt_last_viewed_products'] = array('plugin'=>'xt_last_viewed_products','active'=>'1');
$default_plugins['xt_master_slave'] = array('plugin'=>'xt_master_slave','active'=>'1');
$default_plugins['xt_canonical'] = array('plugin'=>'xt_canonical','active'=>'1');
$default_plugins['xt_new_products'] = array('plugin'=>'xt_new_products','active'=>'1');
$default_plugins['xt_orders_invoices'] = array('plugin'=>'xt_orders_invoices','active'=>'1');
$default_plugins['xt_payment_restriction'] = array('plugin'=>'xt_payment_restriction','active'=>'1');
$default_plugins['xt_prepayment'] = array('plugin'=>'xt_prepayment','active'=>'1');
$default_plugins['xt_privacycheck'] = array('plugin'=>'xt_privacycheck','active'=>'1');
$default_plugins['xt_rescission'] = array('plugin'=>'xt_rescission','active'=>'1');
$default_plugins['xt_special_products'] = array('plugin'=>'xt_special_products','active'=>'1');
$default_plugins['xt_startpage_products'] = array('plugin'=>'xt_startpage_products','active'=>'1');
$default_plugins['xt_upcoming_products'] = array('plugin'=>'xt_upcoming_products','active'=>'1');
$default_plugins['xt_ship_and_track'] = array('plugin'=>'xt_ship_and_track','active'=>'1');
// xt_api muss immer als letztes installiert werden, wegen feldern anderer plugins
$default_plugins['xt_api'] = array('plugin'=>'xt_api','active'=>'1');
