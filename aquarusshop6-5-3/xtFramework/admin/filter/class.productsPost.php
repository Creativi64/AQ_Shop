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
 
require_once(_SRV_WEBROOT."xtFramework/admin/filter/class.formFilter.php");

function setFloat($key){
    
    if( (isset($_SESSION['filters_product'][$key])) && ( (float)($_SESSION['filters_product'][$key])>0 ) )  return true;
    else return false;
    
}
function setInt($key){
    
    if( (isset($_SESSION['filters_product'][$key])) && ( (int)($_SESSION['filters_product'][$key])>0 ) )  return true;
    else return false;
    
}


if(FormFilter::setTxt_XT5('filter_product_name', 'product')){

   $ad_table = ", ".TABLE_PRODUCTS_DESCRIPTION. " pd_filter ";
   $where_ar[] = " pd_filter.products_id = ".TABLE_PRODUCTS.".products_id";
   $where_ar[] = " pd_filter.products_name like '%".$_SESSION['filters_product']['filter_product_name']."%'";
}

if(FormFilter::setTxt_XT5('filter_products_model', 'product'))
{
    $where_ar[] = " products_model like '%".$_SESSION['filters_product']['filter_products_model']."%'";
}

if (setInt('filter_permission') ||  setInt('filter_shop'))
{
    $isBlacklist = constant('_SYSTEM_GROUP_PERMISSIONS') == 'blacklist' ? ' NOT ':'';

    if($isBlacklist)
    {
        $ors = array();
        if (setInt('filter_permission'))
        {
            $ors[] = " pgroup = 'group_permission_".$_SESSION['filters_product']['filter_permission']."' ";
        }
        if (setInt('filter_shop'))
        {
            $ors[] = " pgroup = 'shop_".$_SESSION['filters_product']['filter_shop']."' ";
        }
        $perm_where = implode(' OR ', $ors);

        $where_ar[] = " ".TABLE_PRODUCTS.".products_id NOT IN ( select pid from ".TABLE_PRODUCTS_PERMISSION." where ($perm_where)) ";
    }
    else {
        $ands = array();
        if (setInt('filter_permission'))
        {
            $ands[] = " ".TABLE_PRODUCTS.".products_id  IN (select pid from ".TABLE_PRODUCTS_PERMISSION." where pgroup = 'group_permission_".$_SESSION['filters_product']['filter_permission']."' ) ";
        }
        if (setInt('filter_shop'))
        {
            $ands[] = " ".TABLE_PRODUCTS.".products_id  IN (select pid from ".TABLE_PRODUCTS_PERMISSION." where pgroup = 'shop_".$_SESSION['filters_product']['filter_shop']."' ) ";
        }

        $where_ar[] = implode(' AND ', $ands);
    }
}


if(setFloat('filter_price_from') ) 
 $where_ar[] = " products_price >=  ". (float)$_SESSION['filters_product']['filter_price_from'];

if( setFloat('filter_price_to') ) 
 $where_ar[] = " products_price <= ".(float)$_SESSION['filters_product']['filter_price_to'];

if(isset($_SESSION['filters_product']['filter_manufacturer']) && (int)$_SESSION['filters_product']['filter_manufacturer'] == -1)
{
    $where_ar[] = " (manufacturers_id IS NULL OR manufacturers_id = '' OR manufacturers_id = 0) ";
}
else if (setInt('filter_manufacturer'))
{
    $where_ar[] = " manufacturers_id = ".(int)$_SESSION['filters_product']['filter_manufacturer'];
}


if(setInt('filter_stock_from'))
  $where_ar[] = " products_quantity >= ".(int)$_SESSION['filters_product']['filter_stock_from'];

if(setInt('filter_stock_to'))
  $where_ar[] = " products_quantity <= ".(int)$_SESSION['filters_product']['filter_stock_to'];
elseif ($_SESSION['filters_product']['filter_stock_to']=='0') $where_ar[] = " products_quantity = 0";

if(isset($_SESSION['filters_product']['filter_isDigital']) && $_SESSION['filters_product']['filter_isDigital'] )
  $where_ar[] = " products_digital = 1"; 

if(isset($_SESSION['filters_product']['filter_isFSK18']) && $_SESSION['filters_product']['filter_isFSK18'] )
  $where_ar[] = " products_fsk18 = 1 "; 
  
 if(setFloat('filter_weight_from') ) 
 $where_ar[] = " products_weight >=  ". (float)$_SESSION['filters_product']['filter_weight_from'];

if( setFloat('filter_weight_to') ) 
 $where_ar[] = " products_weight <= ".(float)$_SESSION['filters_product']['filter_weight_to'];
      
if(isset($_SESSION['filters_product']['filter_products_status']) && (int)$_SESSION['filters_product']['filter_products_status'] === 1 )
  $where_ar[] = " products_status = 1"; 
else if(isset($_SESSION['filters_product']['filter_products_status']) && (int)$_SESSION['filters_product']['filter_products_status'] === -1 )
    $where_ar[] = " products_status = 0";
  
if($_SESSION['filters_product']['filter_products_status'] == -1 )
  $where_ar[] = " products_status = 0"; 
  
if(isset($_SESSION['filters_product']['filter_master_slave_products']) && $_SESSION['filters_product']['filter_master_slave_products'] ==1 )
	$where_ar[] = " products_master_flag = 1";
	
if(isset($_SESSION['filters_product']['filter_master_slave_products']) && $_SESSION['filters_product']['filter_master_slave_products'] ==2 )
	$where_ar[] = " products_master_model !='' ";
	
if(isset($_SESSION['filters_product']['filter_master_slave_products']) && $_SESSION['filters_product']['filter_master_slave_products'] ==3 )
	$where_ar[] = " ((products_master_model !='') or (products_master_flag = 1) )  ";     

($plugin_code = $xtPlugin->PluginCode('class.productsPost.php:bottom')) ? eval($plugin_code) : false;
