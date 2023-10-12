<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

global $db;

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK. "admin/filter/class.formFilter.php";

global $db;

if(is_array($_SESSION["filters_paypal_transactions"]))
{
    $local_ad_table = [];
    $local_where = [];

    $flt = $_SESSION["filters_paypal_transactions"];

    if (FormFilter::setTxt_XT5('filter_paypal_transaction_id', 'paypal_transactions'))
    {
        $v = $db->Quote('%'.trim($flt['filter_paypal_transaction_id']).'%');
        $local_where[] = TABLE_CALLBACK_LOG . ".transaction_id LIKE {$v}";
    }

    if (FormFilter::setTxt_XT5('filter_paypal_order_id', 'paypal_transactions'))
    {
        $v = $db->Quote('%'.trim($flt['filter_paypal_order_id']).'%');
        $local_where[] = TABLE_CALLBACK_LOG . ".orders_id LIKE {$v}";
    }

    if ($flt['filter_paypal_order_shop_id'] != "")
    {
        // todo clean id-list
        $local_ad_table[]  = TABLE_ORDERS;
        $local_where[] = TABLE_ORDERS . ".shop_id in (" . $flt['filter_paypal_order_shop_id'] . ")";
    }

    if ($flt['filter_paypal_order_status_id'] != "")
    {
        // todo clean id-list
        $local_ad_table[]  = TABLE_ORDERS;
        $local_where[] = TABLE_ORDERS . ".orders_id = " . TABLE_CALLBACK_LOG . ".orders_id";
        $local_where[] = TABLE_ORDERS . ".orders_status in (" . $flt['filter_paypal_order_status_id'] . ")";
    }
    
    if (FormFilter::setTxt_XT5('filter_paypal_date_from', 'paypal_transactions'))
    {
        // todo quote
        $local_ad_table[] = TABLE_ORDERS;
        $local_where[] = TABLE_ORDERS . ".orders_id = " . TABLE_CALLBACK_LOG . ".orders_id";
        $local_where[] = TABLE_ORDERS . ".date_purchased >='" . FormFilter:: date_trans($flt['filter_paypal_date_from']) . "'";
    }

    if (FormFilter::setTxt_XT5('filter_paypal_date_to', 'paypal_transactions'))
    {
        // todo quote
        $local_ad_table[] = TABLE_ORDERS;
        $local_where[] = TABLE_ORDERS . ".orders_id = " . TABLE_CALLBACK_LOG . ".orders_id";
        $local_where[] = TABLE_ORDERS . ".date_purchased <= '" . FormFilter:: date_trans($flt['filter_paypal_date_to']) . "'";
       }
       
    if (trim($flt['filter_paypal_email'] != ""))
    {
        // todo quote
        $local_ad_table[] = TABLE_ORDERS;
        $local_where[] = TABLE_ORDERS . ".orders_id = " . TABLE_CALLBACK_LOG . ".orders_id";
        $local_where[] = TABLE_ORDERS . ".customers_email_address = '" . trim($flt['filter_paypal_email']) . "'";
    }
   
 
    if(count($local_ad_table))
    {
        $local_ad_table = array_unique(array_filter(array_map('trim', $local_ad_table)));
        $ad_table = ', '.implode(', ', $local_ad_table);
    }
    if(count($local_where))
    {
        $local_where = array_unique(array_filter(array_map('trim',$local_where)));
        if(empty($where_ar)) $where_ar = [];
        $where_ar = array_merge($where_ar, $local_where);
    } 
}
