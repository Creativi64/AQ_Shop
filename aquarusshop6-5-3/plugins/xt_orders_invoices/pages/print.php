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

global $display_output, $db, $store_handler, $xtLink;
$display_output = false;

$btn = $_REQUEST['button'];

if ($btn);
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/class.xt_orders_invoices.php';
    $oi = new xt_orders_invoices();

    $config = array();
    $rs = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION_MULTI . $store_handler->shop_id . " WHERE 1");
    while (!$rs->EOF) {
        $config[$rs->fields['config_key']] = $rs->fields['config_value'];
        $rs->MoveNext();
    }
    $rs->Close();

    $shopUrl = $xtLink->_link(['page' => 'index'], 'xtAdmin');


    $tpl_data = array('cart_data' => $_SESSION['cart']->show_content,
        'data_count' => count($_SESSION['cart']->show_content),
        'content_count' => $_SESSION['cart']->content_count,
        'cart_tax' =>  $_SESSION['cart']->content_tax,
        'cart_total' => $_SESSION['cart']->content_total['formated'],
        'cart_total_weight' => $_SESSION['cart']->weight,
        'show_cart_content'=>true,
        'config' => $config,
        'shopUrl' => $shopUrl,
        'logopath' => _SRV_WEBROOT.'/media/logo/'
    );

    unset($tpl_data['config']['_STORE_SMTP_AUTH']);
    unset($tpl_data['config']['_STORE_SMTP_HOST']);
    unset($tpl_data['config']['_STORE_SMTP_PORT']);
    unset($tpl_data['config']['_STORE_SMTP_USERNAME']);
    unset($tpl_data['config']['_STORE_SMTP_PASSWORD']);

    $sql = "SELECT t.template_id FROM ".TABLE_ORDERS_INVOICES_TEMPLATES." t ".
        " LEFT JOIN ".TABLE_PRINT_BUTTONS. " p ON p.template_type=t.template_type ".
        " WHERE p.buttons_code ='$btn'";
    $tplId = $db->GetOne($sql);

    $oi->_getPdfContent($tpl_data, false, $tplId);
}

//echo 'button => '.$_REQUEST['button'];