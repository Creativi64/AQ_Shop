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

global $db;

$ad_table = '';

//require_once _SRV_WEBROOT . '/xtFramework/library/FirePHPCore/fb.php';
if ($_SESSION['filters_customer']['filter_email'] != ""){
          
     $where_ar[] = " customers_email_address = '".$_SESSION['filters_customer']['filter_email']."'";
          
}

if(FormFilter::setTxt_XT5('filter_company', 'customer')) {
   
           $ad_table = ", ".TABLE_CUSTOMERS_ADDRESSES;
           $where_ar[] = TABLE_CUSTOMERS_ADDRESSES.".customers_id = ".TABLE_CUSTOMERS.".customers_id"; 
           $where_ar[] = " customers_company like '%".$_SESSION['filters_customer']['filter_company']."%'"; 
}

if( $_SESSION['filters_customer']['filter_customers_shop_id'] != ""){

    $where_ar[] =" shop_id in (".$_SESSION['filters_customer']['filter_customers_shop_id'].")";
}

if(FormFilter::setTxt_XT5('filter_name', 'customer')) {

           if(!$ad_table){   
               $ad_table = ", ".TABLE_CUSTOMERS_ADDRESSES;
               $where_ar[] = TABLE_CUSTOMERS_ADDRESSES.".customers_id = ".TABLE_CUSTOMERS.".customers_id"; 
           }
           $where_ar[] = " ( customers_firstname like '%".$_SESSION['filters_customer']['filter_name']."%' or  customers_lastname like '%".$_SESSION['filters_customer']['filter_name']."%' ) "; 
 }
 
       
if ($_SESSION['filters_customer']['filter_gender'] != "") {
           
           if(!$ad_table){   
               $ad_table = ", ".TABLE_CUSTOMERS_ADDRESSES;
               $where_ar[] = TABLE_CUSTOMERS_ADDRESSES.".customers_id = ".TABLE_CUSTOMERS.".customers_id"; 
           }
           $where_ar[] = " customers_gender ='".$_SESSION['filters_customer']['filter_gender']."'"; 
 }
       
       
if ($_SESSION['filters_customer']['filter_language'] != "") {
                
       $rs1 = $db->Execute("SHOW FIELDS FROM xt_customers WHERE field = 'customers_default_language'");
       if ($rs1->RecordCount() > 0){
           $where_ar[] = " customers_default_language ='".$_SESSION['filters_customer']['filter_language']."'"; 
       }
       
 }
       
       
if ($_SESSION['filters_customer']['filter_status_id'] != "") {
            
       $where_ar[] = " customers_status in (".$_SESSION['filters_customer']['filter_status_id'].")"; 
}

if (!empty($_SESSION['filters_customer']['filter_customers_status_id'])) {
    $where_ar[] = " customers_status IN (" . $_SESSION['filters_customer']['filter_customers_status_id'] . ")";
}

if( FormFilter::setTxt_XT5('filter_phone', 'customer')){

    $number = str_replace(' ','', $_SESSION['filters_customer']['filter_phone']);
    $ad_table = ', '.TABLE_CUSTOMERS_ADDRESSES.' ca';
    $where_ar[] =   "(
                    REPLACE(ca.customers_phone, ' ', '') LIKE '%". $number."%' OR 
                    REPLACE(ca.customers_mobile_phone, ' ', '') LIKE '%".$number."%' OR 
                    REPLACE(ca.customers_fax, ' ', '') LIKE '%".$number."%' 
                    ) AND ca.customers_id = ".TABLE_CUSTOMERS.".customers_id "
    ;

    $order_by = ' GROUP BY '.TABLE_CUSTOMERS.'.customers_id '.$this->_sort_order;
}

if( $_SESSION['filters_customer']['filter_zip'] != "" ){

    $ad_table = ', '.TABLE_CUSTOMERS_ADDRESSES.' ca';
    $where_ar[] =   "ca.customers_postcode = '".$_SESSION['filters_customer']['filter_zip']."' "
    ;
}

// filter_last_order_years_ago
if( $_SESSION['filters_customer']['filter_last_order_years_ago'] != "" ){

    $years = intval($_SESSION['filters_customer']['filter_last_order_years_ago']);
    if(is_int($years) && $years > 0)
    {

        $where_ar[] =   " 
        ".DB_PREFIX."_customers.customers_id in 
        (
            SELECT DISTINCT t1.customers_id
                FROM ".DB_PREFIX."_orders t1
            LEFT JOIN (
                SELECT customers_id FROM ".DB_PREFIX."_orders 
                WHERE date_purchased > DATE_SUB(now(), INTERVAL {$years} YEAR)
            ) t2 ON t2.customers_id = t1.customers_id
            WHERE t1.date_purchased <= DATE_SUB(now(), INTERVAL {$years} YEAR)
            AND t2.customers_id  IS NULL
        ) 
                 ";
    }


}

global $xtPlugin;
($plugin_code = $xtPlugin->PluginCode('class.customersPost.php:bottom')) ? eval($plugin_code) : false;      
