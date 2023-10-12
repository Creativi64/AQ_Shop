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

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (ACTIVATE_XT_NEW_PRODUCTS_PAGE == 1 && XT_NEW_PRODUCTS_ACTIVATED == 1){
	global $current_category_id;
	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_new_products/classes/class.new_products.php';

    if (!empty($params['limit'])) {
        $limit = (int)$params['limit'];
    } else {
        $limit = XT_NEW_PRODUCTS_PAGE_LIMIT;
    }

	$days = XT_NEW_PRODUCTS_PAGE_DAYS;
	$order_by = 'p.date_added DESC';
	
	if ($params['order_by'])
	$order_by = $params['order_by'];

	($plugin_code = $xtPlugin->PluginCode('plugin_xt_new_products.php:data_array')) ? eval($plugin_code) : false;

	$new_products_page_data_array = array('limit'=> $limit,
										 'sorting' => $order_by,
										 'days' => $days,
	                                        'total' => false);

	$new_products_page = new new_products($current_category_id);
	$new_products_page_list = $new_products_page->getNewProductListing($new_products_page_data_array);

	$tpl_data = array('heading_text' => TEXT_NEW_PRODUCTS,
						  'product_listing' => $new_products_page_list,
						  'NAVIGATION_COUNT' => $new_products_page->navigation_count,
						  'NAVIGATION_PAGES' => $new_products_page->navigation_pages);

	$tpl = XT_NEW_PRODUCTS_PAGE_TPL;

	if(!empty($params['tpl'])){
		$tpl = $params['tpl'];
	}else{
		$params['tpl'] = $tpl;
	}

	if($current_category_id=='' && is_object($brotkrumen)){
		$brotkrumen->_addItem($xtLink->_link(array('page'=>'xt_new_products')),TEXT_NEW_PRODUCTS);
	}

    if((int)$new_products_page_data_array['total'] > count($new_products_page_list)
        && isset($params) && is_array($params) && isset($params['name']))
    {
        $tpl_data['code'] = $params['name'];
    }
    else {
        $tpl_data['code'] = false;
    }

	$template = new Template();
	($plugin_code = $xtPlugin->PluginCode('plugin_xt_new_products.php:tpl_data')) ? eval($plugin_code) : false;
	$page_data = $template->getTemplate('xt_new_products_smarty', '/'._SRV_WEB_CORE.'pages/product_listing/'.$tpl, $tpl_data);
}else{
	$show_page = false;
}
