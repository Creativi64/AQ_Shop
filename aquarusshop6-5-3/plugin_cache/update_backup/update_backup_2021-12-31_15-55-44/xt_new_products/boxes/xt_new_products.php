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

if (ACTIVATE_XT_NEW_PRODUCTS_BOX == 1 && XT_NEW_PRODUCTS_ACTIVATED == 1){

	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_new_products/classes/class.new_products.php';

	if($params['days']){
		$days = $params['days'];
	}else{
		$days = XT_NEW_PRODUCTS_BOX_DAYS;
	}

	if($params['limit']){
		$limit = $params['limit'];
	}else{
		$limit = XT_NEW_PRODUCTS_BOX_LIMIT;
	}

	$new_products_data_array = array('limit'=> $limit,
										 'sorting' => $params['order_by'],
										 'days' => $days);

    global $current_category_id;
	$new_products_box = new new_products($current_category_id);
	$new_products_list = $new_products_box->getNewProductListing($new_products_data_array);

	if(count($new_products_list) != 0){

		if(ACTIVATE_XT_NEW_PRODUCTS_PAGE==1){
			$show_more_link = true;
		}else{
			$show_more_link = false;
		}

		$tpl_data = array('_new_products'=> $new_products_list, '_show_more_link'=>$show_more_link);
		$show_box = true;
	}else{
		$show_box = false;
	}

}else{
	$show_box = false;
}
