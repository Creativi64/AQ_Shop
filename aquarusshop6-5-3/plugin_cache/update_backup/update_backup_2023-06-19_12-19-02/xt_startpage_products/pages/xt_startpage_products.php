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

global $store_handler, $xtPlugin;

if (ACTIVATE_XT_STARTPAGE_PRODUCTS_PAGE == 1){

	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_startpage_products/classes/class.xt_startpage_products.php';

    /** @var $params array */
    if(!array_key_exists('limit', $params)) $params['limit'] = false;
    if(!array_key_exists('order_by', $params)) $params['order_by'] = false;
    if(!array_key_exists('tpl', $params)) $params['tpl'] = false;

	if($params['limit']){
		$limit = $params['limit'];
	}else{
		$limit = XT_STARTPAGE_PRODUCTS_PAGE_LIMIT;
	}
	
	if($params['order_by']){
		$order_by = $params['order_by'];
	}
	else{
		$order_by = 'startpage_products_sort';
	}

	($plugin_code = $xtPlugin->PluginCode('plugin_xt_startpage_products.php:data_array')) ? eval($plugin_code) : false;

	$startpage_products_page_data_array = array(
			'limit'=> $limit,
			'sorting' => $order_by
	);

	$startpage_products_page = new xt_startpage_products();
	$startpage_products_page_list = $startpage_products_page->getStartPageProductListing(empty($current_category_id) ? 0 : $current_category_id, $startpage_products_page_data_array);

	$tpl_data = array('heading_text' => '',
        'product_listing' => $startpage_products_page_list,
        'error_listing' => '');


	$tpl = XT_STARTPAGE_PRODUCTS_PAGE_TPL;

	if(!empty($params['tpl'])){
		$tpl = $params['tpl'];
	}else{
		$params['tpl'] = $tpl;
	}

	$template = new Template();
	($plugin_code = $xtPlugin->PluginCode('plugin_xt_startpage_products.php:tpl_data')) ? eval($plugin_code) : false;
	$page_data = $template->getTemplate('xt_startpage_products_smarty', '/'._SRV_WEB_CORE.'pages/product_listing/'.$tpl, $tpl_data);
}else{
	$show_page = false;
}
