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

class xt_cart_popup{
	
	public function getCartContent()
	{
		global $xtPlugin, $info, $system_shipping_link;;
		
		if (is_object($_SESSION['cart']) && count($_SESSION['cart']->show_content) > 0){
	
			$_SESSION['cart']->_checkCustomersStatusRange();
			$tpl_data_cart = array('cart_data' => $_SESSION['cart']->show_content,
								  'cart_tax' =>  $_SESSION['cart']->content_tax,
								  'cart_total' => $_SESSION['cart']->content_total['formated'],
								  'cart_total_weight' => $_SESSION['cart']->weight,
								  'message'=>$info->info_content,
								  'show_cart_content'=>true,
                                  'show_checkout_button'=> !array_key_exists('xt_klarna_kco', $xtPlugin->active_modules) );
		
			$shipping_link = $system_shipping_link->shipping_link;
			if ($shipping_link!='') {
				$tpl_data_cart['shipping_link'] = $shipping_link;
			}
			($plugin_code = $xtPlugin->PluginCode('xt_cart_popup.php:tpl_data')) ? eval($plugin_code) : false;
			if(isset($plugin_return_value))
			return $plugin_return_value;

		}
		else{

			if (_CUST_STATUS_SHOW_PRICE=='1') {
				$info->_addInfo(WARNING_EMTPY_CART,'warning');
			} else {
				$info->_addInfo(WARNING_NO_PRICE_ALLOWED,'warning');
			}

			$tpl_data_cart = array('show_cart_content'=>false,'message'=>$info->info_content);

		}

		$template = new Template();
		$tpl = 'ajax_cart.html';
		$template->getTemplatePath($tpl, 'xt_cart_popup', '', 'plugin');
		$content = $template->getTemplate('', $tpl, $tpl_data_cart);
		return $content;
	}
	
}
