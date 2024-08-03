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

if (!isset($xtPlugin->active_modules['xt_last_viewed_products'])) {
    // Box nicht anzeigen
	$show_box = false;
}
else
{
	include_once 'plugins/xt_last_viewed_products/classes/class.xt_last_viewed_products.php';

	$last_viewed_products_obj = new last_viewed_products();
	$last_viewed_products = $last_viewed_products_obj->getLastViewedProductListing();
	if(count($last_viewed_products) != 0){
		$tpl_data = array('_last_viewed_products'=> $last_viewed_products);
		$show_box = true;
	}
	else {
		$show_box = false;
	}
}
