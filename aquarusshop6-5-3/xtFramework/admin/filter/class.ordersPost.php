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

global $db, $xtPlugin;

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK."admin/filter/class.formFilter.php";

$ad_table = [];

if($_SESSION['filters_order']['filter_order_status_id'] ?? '' != ""){
    
    $where_ar[] =" orders_status in (".$_SESSION['filters_order']['filter_order_status_id'].")";
}

if( $_SESSION['filters_order']['filter_order_shop_id'] ?? '' != ""){
    
    $where_ar[] =" shop_id in (".$_SESSION['filters_order']['filter_order_shop_id'].")";
}

//payment way
if($_SESSION['filters_order']['filter_payment_way_id'] ?? '' != ""){
    
    $payments = explode(',',$_SESSION['filters_order']['filter_payment_way_id']);
    $payments = array_filter($payments);
    $payments = array_map('trim', $payments);
    $payments = array_unique($payments);
    $p_codes = array();
    $p_ids = array();
    foreach($payments as $p)
    {
        if (is_numeric($p))
        {
            $p_ids[] = $p;
            continue;
        }
        $p_codes[] = $p;
    }

    if(count($p_ids))
    {
        $sql = "SELECT payment_code FROM " . TABLE_PAYMENT . " WHERE payment_id IN (" .implode(',', $p_ids). ")";
    $rs1 = $db->Execute($sql);
    $data1 = array();
        if ($rs1->RecordCount() > 0)
        {
            while (!$rs1->EOF)
            {
                $data1[] =  "'".$rs1->fields['payment_code']."'";
                $rs1->MoveNext();
            }
    }
    }
    if(count($p_codes))
    {
        foreach($p_codes as $code)
        {
            $data1[] = "'" . $code . "'";
        };
    }
    $where_ar[] = " payment_code in (".implode(",",$data1).")";
}
//shipping way
if($_SESSION['filters_order']['filter_shipping_way_id'] ?? ''  != ""){

    $data1 = explode(",",$_SESSION['filters_order']['filter_shipping_way_id']);
    foreach($data1 as $val) $data2[] = "'".$val."'";
    $where_ar[] = " shipping_code in (".implode(",",$data2).")";
    
}

//comboxes end



 if(FormFilter::setTxt_XT5('filter_id_from', 'order') ){

       $where_ar[] = TABLE_ORDERS.".orders_id >='". $_SESSION['filters_order']['filter_id_from']."'"; 
   }

       
if( FormFilter::setTxt_XT5('filter_id_to', 'order') ){

       $where_ar[] = TABLE_ORDERS.". orders_id <= '". $_SESSION['filters_order']['filter_id_to']."'"; 
   }
   
   
if( FormFilter::setTxt_XT5('filter_date_from', 'order')){

       $where_ar[] = " DATE_FORMAT(date_purchased,'%Y-%m-%d') >='".FormFilter:: date_trans($_SESSION['filters_order']['filter_date_from'])."'";
 }

 
if(FormFilter::setTxt_XT5('filter_date_to', 'order')){

	    $where_ar[] = " DATE_FORMAT(date_purchased,'%Y-%m-%d') <= '".FormFilter:: date_trans($_SESSION['filters_order']['filter_date_to'])."'";
 }

 
if( FormFilter::setTxt_XT5('filter_name', 'order')){
           
           $where_ar[] = " ( billing_firstname like '%".$_SESSION['filters_order']['filter_name']."%' or  billing_lastname like '%".$_SESSION['filters_order']['filter_name']."%' )"; 
               
}
    
$table_stats_added = false;
if(FormFilter::setTxt_XT5('filter_amount_from', 'order')){
    
   $ad_table[] = TABLE_ORDERS_STATS;
    $table_stats_added = true;
       $where_ar[] = TABLE_ORDERS_STATS.".orders_id = ".TABLE_ORDERS.".orders_id"; 
       $where_ar[] = " orders_stats_price >='". $_SESSION['filters_order']['filter_amount_from']."'"; 
}

       
if( FormFilter::setTxt_XT5('filter_amount_to', 'order')){
   
    if($table_stats_added == false)
    {
    $ad_table[] = TABLE_ORDERS_STATS;
           $where_ar[] = TABLE_ORDERS_STATS.".orders_id = ".TABLE_ORDERS.".orders_id"; 
    }
       $where_ar[] = " orders_stats_price <= '". $_SESSION['filters_order']['filter_amount_to']."'"; 
}
   
 
if( FormFilter::setTxt_XT5('filter_last_modify_from', 'order')){

       $where_ar[] = " last_modified >='". FormFilter::date_trans($_SESSION['filters_order']['filter_last_modify_from'])."'"; 
 }

  if(FormFilter::setTxt_XT5('filter_last_modify_to', 'order')){
       $where_ar[] = " last_modified<= '". FormFilter::date_trans($_SESSION['filters_order']['filter_last_modify_to'])."'"; 
   }
 
  
   
if( ($_SESSION['filters_order']['filter_email'] ?? '' != "") ){

       $where_ar[] = " customers_email_address = '".$_SESSION['filters_order']['filter_email']."'"; 
}

if( ($_SESSION['filters_order']['filter_products_model'] ?? '' != "") ){

    $ad_table[] = TABLE_ORDERS_PRODUCTS;
    $where_ar[] = TABLE_ORDERS_PRODUCTS.".products_model = '".$_SESSION['filters_order']['filter_products_model']."' AND ".TABLE_ORDERS_PRODUCTS.".orders_id = ".TABLE_ORDERS.".orders_id ";
}

if( FormFilter::setTxt_XT5('filter_phone', 'order')){

    $number = str_replace(' ','', $_SESSION['filters_order']['filter_phone']);
    $where_ar[] =   "(
                    REPLACE(delivery_phone, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(delivery_mobile_phone, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(delivery_fax, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(billing_phone, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(billing_mobile_phone, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(billing_fax, ' ', '') LIKE '%".$number."%' 
                    )"
    ;
}

if( $_SESSION['filters_order']['filter_zip'] ?? '' != "" ){

    $where_ar[] =   "( 
                     delivery_postcode = '".$_SESSION['filters_order']['filter_zip']."' OR 
                     billing_postcode = '".$_SESSION['filters_order']['filter_zip']."' 
                     )"
    ;
}

// filter_no_valid_customer_assoc
if( ($_SESSION['filters_order']['filter_no_valid_customer_assoc'] ?? '' != "") )
{

    $where_ar[] = "
    ".DB_PREFIX."_orders.orders_id in 
    (
    SELECT t1.orders_id
        FROM ".DB_PREFIX."_orders t1
        LEFT JOIN (
            SELECT customers_id FROM ".DB_PREFIX."_customers
        ) t2 ON t2.customers_id = t1.customers_id
        WHERE t2.customers_id  IS NULL
    ) 
    ";
}

($plugin_code = $xtPlugin->PluginCode('orderPosts:bottom')) ? eval($plugin_code) : false;
