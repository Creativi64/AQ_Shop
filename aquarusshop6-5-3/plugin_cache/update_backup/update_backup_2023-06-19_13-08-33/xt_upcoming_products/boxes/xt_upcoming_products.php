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

if (XT_UPCOMING_PRODUCTS_ACTIVATED == 1 && ACTIVATE_XT_UPCOMING_PRODUCTS_BOX == 1){

	require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'/xt_upcoming_products/classes/class.upcoming_products.php';

	if($params['limit']){
		$limit = $params['limit'];
	}else{
		$limit = XT_UPCOMING_PRODUCTS_BOX_LIMIT;
	}

	$upcoming_products_data_array = array('limit'=> $limit,
										 'sorting' => $params['order_by']);

    global $current_category_id;
	$upcoming_products_box = new upcoming_products($current_category_id);
	$upcoming_products_list = $upcoming_products_box->getUpcomingProductListing($upcoming_products_data_array);

	if(count($upcoming_products_list) != 0){

		if(ACTIVATE_XT_UPCOMING_PRODUCTS_PAGE==1){
			$show_more_link = true;
		}else{
			$show_more_link = false;
		}

		$tpl_data = array('_upcoming_products'=> $upcoming_products_list, '_show_more_link'=>$show_more_link);
		$show_box = true;
	}else{
		$show_box = false;
	}

}else{
	$show_box = false;
}
