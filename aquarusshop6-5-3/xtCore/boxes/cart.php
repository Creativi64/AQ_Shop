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

if (is_object($_SESSION['cart']) && count($_SESSION['cart']->show_content) > 0){
	
	$tpl_data = array('cart_data' => $_SESSION['cart']->show_content,
					  'data_count' => count($_SESSION['cart']->show_content),
					  'content_count' => $_SESSION['cart']->content_count,
					  'cart_tax' =>  $_SESSION['cart']->content_tax,
					  'cart_total' => $_SESSION['cart']->content_total['formated'],
					  'cart_total_weight' => $_SESSION['cart']->weight,
					  'show_cart_content'=>true);

	global $system_shipping_link;
	$shipping_link = $system_shipping_link->shipping_link;
	if ($shipping_link!='') {
		$tpl_data = array_merge($tpl_data,array('shipping_link'=>$shipping_link));
	}

}else{

	$tpl_data = array('show_cart_content'=>false);

}

$show_box = true;

?>