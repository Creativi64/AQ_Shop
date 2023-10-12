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

if (XT_SPECIAL_PRODUCTS_ACTIVATED ==1 && ACTIVATE_XT_SPECIAL_PRODUCTS_BOX == 1){

	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_special_products/classes/class.special_products.php';

	if($params['limit']){
		$limit = $params['limit'];
	}else{
		$limit = XT_SPECIAL_PRODUCTS_BOX_LIMIT;
	}
	
	$order_by = 'p.date_added DESC';
	if ($params['order_by'])
		$order_by = $params['order_by'];
	
	$special_products_data_array = array('limit'=> $limit,
										 	 'sorting' => $order_by);
    global $current_category_id;
	$special_products_box = new special_products($current_category_id);
	$special_products_list = $special_products_box->getSpecialProductListing($special_products_data_array);

	if(count($special_products_list) != 0){

		if(ACTIVATE_XT_SPECIAL_PRODUCTS_PAGE==1){
			$show_more_link = true;
		}else{
			$show_more_link = false;
		}

		$tpl_data = array('_special_products'=> $special_products_list, '_show_more_link'=>$show_more_link);
		$show_box = true;
	}else{
		$show_box = false;
	}

}else{
	$show_box = false;
}
