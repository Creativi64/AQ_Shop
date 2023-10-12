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

if (isset ($_POST['action']) || isset ($_GET['action'])) {

    global $page, $xtLink, $xtPlugin, $currency, $store_handler, $language, $info;

	$post_data = array();
	$post_data = $_POST;
	$get_data = array();
	$get_data = $_GET;
		
	$data_array = array_merge($post_data, $get_data);

	($plugin_code = $xtPlugin->PluginCode('form_handler.php:data_array_top')) ? eval($plugin_code) : false;

	switch ($data_array['action']) {

		case 'change_currency' :

			if($currency->_checkStore($data_array['new_currency'], $store_handler->shop_id)==true)
			$_SESSION['selected_currency'] = $data_array['new_currency'];

			$link_array = array('page'=>$page->page_name, 'params'=>$xtLink->_getParams(array('action', 'new_currency')));
			if(isset($_POST['paction'])){
				$link_array['paction']=$_POST['paction'];
			}
            if (($page->page_name == 'checkout' || $page->page_name == 'customer' ) && _SYSTEM_SSL == true) {
                $link_array['conn']='SSL';
            }
            ($plugin_code = $xtPlugin->PluginCode('form_handler.php:change_currency_bottom')) ? eval($plugin_code) : false;
            $xtLink->_redirect($xtLink->_link($link_array));

		break;

		case 'change_lang______DISABLED' :
			if($language->_checkStore($data_array['new_lang'], $store_handler->shop_id)==true)
			$_SESSION['selected_language'] = $data_array['new_lang'];

			if($page->loaded_page == 'xtCore/pages/index.php' && $page->page_name == 'content')
			{
				$l = $xtLink->_link(array('conn'=>'SSL'));
				$l = str_replace('/index.php','', $l);
				$l = str_replace('/index','', $l);
				$xtLink->_redirect($l);
			}

			$link_array = array('page'=>$page->page_name, 'paction'=>$page->page_action,'params'=>$xtLink->_getParams(array('action', 'new_lang')), 'lang_code' => $data_array['new_lang']);

            if (($page->page_name == 'checkout' || $page->page_name == 'customer' ) && _SYSTEM_SSL == true) {
                $link_array['conn']='SSL';
            }
            ($plugin_code = $xtPlugin->PluginCode('form_handler.php:change_lang_bottom')) ? eval($plugin_code) : false;

			$l = $xtLink->_link($link_array);
			$l = str_replace('/index.php','', $l);
			$l = str_replace('/index','', $l);
			$xtLink->_redirect($l);

		break;

		case 'add_product' :
			// für mgl iterationen in form_handler.php:add_product_top damit customers_discount form_handler.php:add_product_bottom greift
            $cart_product_group_discount_allowed = false;

			($plugin_code = $xtPlugin->PluginCode('form_handler.php:add_product_top')) ? eval($plugin_code) : false;

            if(empty($data_array['qty'])) $data_array['qty'] = 1;
            //negative quantity
            if($data_array['qty'] < 0){
                $data_array['qty'] = -$data_array['qty'];
            }

            $cart_product = sessionCart()->_addCart($data_array);
			if($cart_product)
			{
			    $info->_addInfoSession(sprintf(SUCCESS_PRODUCT_ADDED, $cart_product->data['products_name']),'success');

			    // für mgl iterationen in form_handler.php:add_product_top damit customers_discount form_handler.php:add_product_bottom greift
                if($cart_product_group_discount_allowed == 1) $cart_product->data['group_discount_allowed'] = 1;

                $_SESSION['cartChanged'] = true;
            }

            $link_array = array('page'=>'cart');
            if(array_key_exists('gotoCart', $data_array) && ($data_array['gotoCart'] == '0' || empty($data_array['gotoCart'])))
            {
                $link_array = array('page'=>$page->page_name, 'params'=>$xtLink->_getParams());
            }

			($plugin_code = $xtPlugin->PluginCode('form_handler.php:add_product_bottom')) ? eval($plugin_code) : false;
            if($cart_product)
            {
                sessionCart()->_refresh();
            }
			$xtLink->_redirect($xtLink->_link($link_array));

		break;

		case 'update_product' :
			($plugin_code = $xtPlugin->PluginCode('form_handler.php:update_product_top')) ? eval($plugin_code) : false;

            if(!is_data($data_array['cart_delete']))
                $data_array['cart_delete'] = array();

			for ($i = 0, $n = sizeof($data_array['cart_delete']); $i < $n; $i++) {
                if(array_key_exists($data_array['cart_delete'][$i], sessionCart()->content))
                    sessionCart()->_deleteContent($data_array['cart_delete'][$i]);
			}

			if(!is_data($data_array['products_key']))
			$data_array['products_key'] = array();

			for ($i = 0, $n = sizeof($data_array['products_key']); $i < $n; $i++) {

					($plugin_code = $xtPlugin->PluginCode('form_handler.php:update_product_update')) ? eval($plugin_code) : false;

					if(!in_array($data_array['products_key'][$i], $data_array['cart_delete'])){
						$data = array('products_key'=>$data_array['products_key'][$i], 'qty'=>$data_array['qty'][$i]);
						if(array_key_exists($data['products_key'], sessionCart()->content))
                            sessionCart()->_updateCart($data);
					}

			}
            $_SESSION['cartChanged'] = true;

			$link_array = array('page'=>$page->page_name);
			($plugin_code = $xtPlugin->PluginCode('form_handler.php:update_product_bottom')) ? eval($plugin_code) : false;
            sessionCart()->_refresh();
			//$xtLink->_redirect($xtLink->_link($link_array));

		break;

		case 'select_address' :
			($plugin_code = $xtPlugin->PluginCode('form_handler.php:select_address_top')) ? eval($plugin_code) : false;
			sessionCustomer()->_setAdress($data_array['adID'],$data_array['adType']);

			if($data_array['adType']=='payment')
			unset($_SESSION['selected_payment']);

			if($data_array['adType']=='shipping')
			unset($_SESSION['selected_shipping']);

			$link_array = array('page'=>$page->page_name, 'paction'=>$data_array['adType'], 'conn'=>'SSL');
			($plugin_code = $xtPlugin->PluginCode('form_handler.php:select_address_bottom')) ? eval($plugin_code) : false;
			$xtLink->_redirect($xtLink->_link($link_array));

		break;

		default:

	    ($plugin_code = $xtPlugin->PluginCode('form_handler.php:data_array_bottom')) ? eval($plugin_code) : false;
	}
}
