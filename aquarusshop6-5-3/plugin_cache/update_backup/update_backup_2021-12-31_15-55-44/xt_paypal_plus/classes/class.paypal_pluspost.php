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

include_once(_SRV_WEBROOT."xtFramework/admin/filter/class.formFilter.php");


if( $_SESSION['filter_paypal_plus_order_shop_id'] != ""){
    $where_ar[] = TABLE_CALLBACK_LOG.".shop_id in (".$_SESSION['filter_paypal_plus_order_shop_id'].")";
}

if($_SESSION['filter_paypal_plus_order_status_id'] != ""){
        $ad_table = ", ".TABLE_ORDERS;
        $where_ar[] = TABLE_ORDERS.".orders_id = ".TABLE_CALLBACK_LOG.".orders_id AND ".TABLE_ORDERS.".orders_status in (".$_SESSION['filter_paypal_plus_order_status_id'].")";
}


//shipping way
if($_SESSION['filter_paypal_plus_shipping_way_id'] != ""){

    $data1 = explode(",",$_SESSION['filter_paypal_plus_shipping_way_id']);
    foreach($data1 as $val) $data2[] = "'".$val."'";
    if($ad_table == ""){
        $ad_table = ", ".TABLE_ORDERS;
        $where_ar[] = TABLE_ORDERS.".orders_id = ".TABLE_CALLBACK_LOG.".orders_id AND ".TABLE_ORDERS.".shipping_code in (".implode(",",$data2).")"; 

    }else{
        $where_ar[] = TABLE_ORDERS.".shipping_code in (".implode(",",$data2).")"; 

    }
    
}

//comboxes end

if(FormFilter::setTxt('filter_paypal_plus_order_id') ){

    $where_ar[] = " orders_id ='". $_SESSION['filter_paypal_plus_order_id']."'";
}

   
if( FormFilter::setTxt('filter_paypal_plus_date_from')){

    $where_ar[] = " date_purchased >='".FormFilter:: date_trans($_SESSION['filter_paypal_plus_date_from'])."'";
}

 
if(FormFilter::setTxt('filter_paypal_plus_date_to')){

    $where_ar[] = " date_purchased <= '".FormFilter:: date_trans($_SESSION['filter_paypal_plus_date_to'])."'";
 }

 
if( FormFilter::setTxt('filter_paypal_plus_name')){
           
    $where_ar[] = " ( billing_firstname like '%".$_SESSION['filter_paypal_plus_name']."%' or  billing_lastname like '%".$_SESSION['filter_paypal_plus_name']."%' )";
           
}
    
    
if(FormFilter::setTxt('filter_paypal_plus_amount_from')){
    
       $ad_table = ", ".TABLE_ORDERS_STATS;
       $where_ar[] = TABLE_ORDERS_STATS.".orders_id = ".TABLE_ORDERS.".orders_id"; 
       $where_ar[] = " orders_stats_price >='". $_SESSION['filter_paypal_plus_amount_from']."'";
}

       
if( FormFilter::setTxt('filter_paypal_plus_amount_to')){
   
       if($ad_table == ""){
           $ad_table = ", ".TABLE_ORDERS_STATS;
           $where_ar[] = TABLE_ORDERS_STATS.".orders_id = ".TABLE_ORDERS.".orders_id"; 
       }
       
       $where_ar[] = " orders_stats_price <= '". $_SESSION['filter_paypal_plus_amount_to']."'";
}
   
 
if( FormFilter::setTxt('filter_paypal_plus_last_modify_from')){

       $where_ar[] = " last_modified >='". FormFilter::date_trans($_SESSION['filter_paypal_plus_last_modify_from'])."'";
 }

if(FormFilter::setTxt('filter_paypal_plus_last_modify_to')){
     $where_ar[] = " last_modified<= '". FormFilter::date_trans($_SESSION['filter_paypal_plus_last_modify_to'])."'";
 }
 
  
   
if( ($_SESSION['filter_paypal_plus_email'] != "") ){
    if($ad_table == ""){   
        $ad_table = ", ".TABLE_ORDERS;
    } 
    
    $where_ar[] = " customers_email_address = '".$_SESSION['filter_paypal_plus_email']."'";
}


?>