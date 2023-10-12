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

global $brotkrumen, $xtLink, $xtPlugin, $manufacturer;

if(isset($_GET['mnf']) && is_int($_GET['mnf'])){
  	$current_manufacturer_id = (int)$_GET['mnf'];
}
  	
if (empty($current_manufacturer_id)) {

$xtLink->_redirect($xtLink->_link(array('page'=>'index')));

} else {

    ($plugin_code = $xtPlugin->PluginCode('module_manufacturers.php:top')) ? eval($plugin_code) : false;
    if(isset($plugin_return_value))
        return $plugin_return_value;
	
	$listingtemplate = '/'._SRV_WEB_CORE.'pages/product_listing/'._STORE_TEMPLATE_PRODUCT_LISTING_MANUFACTURERS;
	
	$template = new Template();
	
	$template->setAdditionalCacheID('mnf_'.$current_manufacturer_id);
	
	if ($template->isTemplateCache($listingtemplate)) {
        /** @var $tpl_data array */
		$products_listing = $template->getTemplate('listing_smarty',$listingtemplate, $tpl_data);
	} else {

        $list = new products_list('');

		($plugin_code = $xtPlugin->PluginCode('module_manufacturers.php:top')) ? eval($plugin_code) : false;
		 
		$man = array('manufacturers_id' => $current_manufacturer_id);
		$man_data = $manufacturer->buildData($man);
		if (!is_array($man_data)) {
			if (_SYSTEM_MOD_REWRITE_404 == 'true') header("HTTP/1.0 404 Not Found");
			$tmp_link = $xtLink->_link(array('page'=>'404'));
			$xtLink->_redirect($tmp_link);
		}

        /** @var $data array */
        if(is_null($data))$data=array();
        $data = array_merge($data, array('MANUFACTURER' => $man_data));
		
		$manufacturer_data = $data;
		
        if($xtPlugin->active_modules['xt_master_slave'])
        {
		    $list->sql_products->a_sql_where .= " AND (products_master_model IS NULL OR products_master_model='') ";
        }

        if(!empty($_REQUEST['withStockOnly']) || (!empty($_SESSION['session_withStockOnly']) && array_key_exists('session_withStockOnly', $_REQUEST)))
        {
            $list->sql_products->setFilter('Stock');
            $_SESSION['session_withStockOnly'] = 1;
        }
        else {
            $_SESSION['session_withStockOnly'] = 0;
        }

		$tpl_product_listing = $list->getProductListing();
		
		$heading_text= $man_data['manufacturers_name'];
		
		$brotkrumen->_addItem($xtLink->_link(array('page'=>'manufacturers', 'params'=>'mnf='.$current_manufacturer_id,'seo_url' => $man_data['url_text'])),$man_data['manufacturers_name']);
		
		$sort_dropdown = is_array($tpl_product_listing) ? $list->getSortDropdown():'';
		if(isset($_GET['sorting']) && is_array($sort_dropdown) && $list->isSortDropdownDefault($sort_dropdown, $_GET['sorting'])){
			$sort_default = $_GET['sorting'];
		}
		else {
			$sort_default = '';
		}
		 
		$tpl_data = array('categories' => $categories_listing ?? '',
				'heading_text' => $heading_text,
				'manufacturer'=>$data,
				'current_manufacturer_id'=>$current_manufacturer_id,
				'product_listing' => $tpl_product_listing,
				'MANUFACTURER_DROPDOWN' => $manufacturers_dropdown ?? false,
				'NAVIGATION_COUNT' => $list->navigation_count,
				'NAVIGATION_PAGES' => $list->navigation_pages,
				'mnf_page' => true,
				'sort_default' => $sort_default,
				'sort_dropdown' => $sort_dropdown);
		
		
		($plugin_code = $xtPlugin->PluginCode('module_manufacturers.php:tpl_data')) ? eval($plugin_code) : false;
		
		$products_listing = $template->getTemplate('listing_smarty',$listingtemplate, $tpl_data);
		
	}

  	$page_data = $products_listing;

}
