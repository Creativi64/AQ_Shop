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

global $xtPlugin, $current_category_id, $xtLink, $brotkrumen, $page;

if(!isset($params)) $params = [];

if (XT_SPECIAL_PRODUCTS_ACTIVATED == 1
    &&
    (
        ($page->page_name == 'index' && ACTIVATE_XT_SPECIAL_PRODUCTS_START_PAGE == 1 ) ||
        ($page->page_name == 'xt_special_products' && ACTIVATE_XT_SPECIAL_PRODUCTS_PAGE == 1 ) ||
        ($params['force'] == 1 || $params['force'] == 'true')
    )
){
	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_special_products/classes/class.special_products.php';

    if (!empty($params['limit'])) {
        $limit = (int)$params['limit'];
    } else {
        $limit = XT_SPECIAL_PRODUCTS_PAGE_LIMIT;
    }
	
	$order_by = 'p.date_added DESC';
	if ($params['order_by'])
	$order_by = $params['order_by'];
	
	($plugin_code = $xtPlugin->PluginCode('plugin_xt_special_products.php:data_array')) ? eval($plugin_code) : false;

	$special_products_page_data_array = array('limit'=> $limit,
										 	  	  'sorting' => $order_by,
                                                'total' => false
                                            );


	$special_products_page = new special_products($current_category_id);
	$special_products_page_list = $special_products_page->getSpecialProductListing($special_products_page_data_array);

	$tpl_data = array('heading_text' => TEXT_SPECIAL_PRODUCTS,
						  'product_listing' => $special_products_page_list,
						  'NAVIGATION_COUNT' => $special_products_page->navigation_count,
						  'NAVIGATION_PAGES' => $special_products_page->navigation_pages,
        'error_listing' => '');


	$tpl = XT_SPECIAL_PRODUCTS_PAGE_TPL;

	if(!empty($params['tpl'])){
		$tpl = $params['tpl'];
	}else{
		$params['tpl'] = $tpl;
	}

    if((int)$special_products_page_data_array['total'] > count($special_products_page_list)
        && isset($params) && is_array($params) && isset($params['name']))
    {
        $tpl_data['code'] = $params['name'];
    }
    else {
        $tpl_data['code'] = false;
    }

	if(is_object($brotkrumen))
	    $brotkrumen->_addItem($xtLink->_link(array('page'=>'xt_special_products')),TEXT_SPECIAL_PRODUCTS);

    $tpl_data['total'] = $special_products_page_data_array['total'];
    $tpl_data['show_more'] = ACTIVATE_XT_SPECIAL_PRODUCTS_PAGE == 1 && $special_products_page_data_array['total'] > $limit;

	$template = new Template();
	($plugin_code = $xtPlugin->PluginCode('plugin_xt_special_products.php:tpl_data')) ? eval($plugin_code) : false;
	$page_data = $template->getTemplate('xt_special_products_smarty', '/'._SRV_WEB_CORE.'pages/product_listing/'.$tpl, $tpl_data);
}else if($page->page_name == 'xt_special_products'){
    if (_SYSTEM_MOD_REWRITE_404=='true') {
        header("HTTP/1.0 404 Not Found");
        $tmp_link  = $xtLink->_link(array('page'=>'404'));
    }else{
        $tmp_link  = $xtLink->_index();
    }
    $xtLink->_redirect($tmp_link);
}