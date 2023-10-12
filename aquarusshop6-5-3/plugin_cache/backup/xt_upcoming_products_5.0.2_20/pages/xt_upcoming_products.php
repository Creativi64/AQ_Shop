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

if (XT_UPCOMING_PRODUCTS_ACTIVATED == 1 && ACTIVATE_XT_UPCOMING_PRODUCTS_PAGE == 1){
	global $current_category_id, $brotkrumen;
	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_upcoming_products/classes/class.upcoming_products.php';

	$limit = XT_UPCOMING_PRODUCTS_PAGE_LIMIT;
	$order_by = 'p.date_added DESC';
	
	if ($params['order_by'])
	$order_by = $params['order_by'];

	($plugin_code = $xtPlugin->PluginCode('plugin_xt_upcoming_products.php:data_array')) ? eval($plugin_code) : false;

	$upcoming_products_page_data_array = array('limit'=> $limit,
										 'sorting' => $order_by,
                                            'total' => false
                                        );

	$upcoming_products_page = new upcoming_products($current_category_id);
	$upcoming_products_page_list = $upcoming_products_page->getUpcomingProductListing($upcoming_products_page_data_array);

	$tpl_data = array('heading_text' => TEXT_UPCOMING_PRODUCTS,
						  'product_listing' => $upcoming_products_page_list,
						  'NAVIGATION_COUNT' => $upcoming_products_page->navigation_count,
						  'NAVIGATION_PAGES' => $upcoming_products_page->navigation_pages);

	$tpl = XT_UPCOMING_PRODUCTS_PAGE_TPL;

	if(!empty($params['tpl'])){
		$tpl = $params['tpl'];
	}else{
		$params['tpl'] = $tpl;
	}

    if((int)$upcoming_products_page_data_array['total'] > count($upcoming_products_page_list)
        && isset($params) && is_array($params) && isset($params['name']))
    {
        $tpl_data['code'] = $params['name'];
    }
    else {
        $tpl_data['code'] = false;
    }

    if(is_object($brotkrumen) && $page->page_name=='xt_upcoming_products'){
        $brotkrumen->_addItem($xtLink->_link(array('page'=>'xt_upcoming_products')),TEXT_UPCOMING_PRODUCTS);
	}

	$template = new Template();
	($plugin_code = $xtPlugin->PluginCode('plugin_xt_upcoming_products.php:tpl_data')) ? eval($plugin_code) : false;
	$page_data = $template->getTemplate('xt_upcoming_products_smarty', '/'._SRV_WEB_CORE.'pages/product_listing/'.$tpl, $tpl_data);
}else{
	$show_page = false;
}
