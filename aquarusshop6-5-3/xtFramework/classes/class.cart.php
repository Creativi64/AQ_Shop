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

class cart {

	var $show_content = array();
	var $show_sub_content = array();

	var $content = array();
	var $content_total = ['plain' => 0, 'plain_otax' => 0];
	var $content_tax = array();
	var $content_count = 0;
	var $content_weight = 0;

	var $sub_content = array();
	var $sub_content_total = ['plain' => 0, 'plain_otax' => 0];
	var $sub_content_tax = array();
	var $sub_content_count = 0;
	var $sub_content_weight = 0;

	var $type = '';

	var $total = ['plain' => 0, 'plain_otax' => 0];
	var $tax = ['plain' => 0, 'plain_otax' => 0];
	var $weight = 0;
    var $total_discount;
    var $cart_total_full_for_customers_discount = 0;

    function __construct($refresh = false) {
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:cart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if($refresh) $this->_refresh(); // passion-d

	}

	function _refresh(){
		global $xtPlugin, $price, $tax, $currency, $customers_status;

        /*
        if(empty($_REQUEST['refreshCounter'])) $_REQUEST['refreshCounter'] = 1;
            else $_REQUEST['refreshCounter'] = $_REQUEST['refreshCounter'] + 1;

        $ex = new Exception('aeaserfaew');
        error_log('## refreshCounter '.$_REQUEST['refreshCounter'] ."\n". $ex->getTraceAsString());
        */

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_refresh_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		unset($this->show_content);
		unset($this->show_sub_content);
		unset($this->tax);
		unset($this->total);
		
		$content_data = $this->_getContent();
		$sub_content_data = $this->_getSubContent();
				
		$this->show_content = $content_data['products'];

		$content_tax_sum = 0;
		$sub_content_tax_sum = 0;

        $tmp_content_tax = array();
		if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1'){
			$content_tax_data = $tax->data;

            foreach($content_tax_data as $key => $value){
				if(isset($content_data['tax'][$key]) && $content_data['tax'][$key] != 0)
                {
                    $tmp_content_tax[$key] = array('tax_value' => $price->_Format(array('price' => $content_data['tax'][$key], 'format' => true, 'format_type' => 'default')), 'tax_key' => round($value, $currency->decimals));
                    $content_tax_sum += $content_data['tax'][$key];
                }
			}
		}
				
		if($customers_status->customers_status_show_price_tax!='1' && $customers_status->customers_status_add_tax_ot=='1'){
			$add_content_tax = $content_tax_sum;
		}else{
            $add_content_tax = 0;
		}
		
		if($customers_status->customers_status_show_price_tax=='1'){
			$this->content_total = $price->_Format(array('price'=>$content_data['total']+$add_content_tax, 'price_otax'=>$content_data['total_otax'], 'format'=>true, 'format_type'=>'default'));
		}else{
			$this->content_total = $price->_Format(array('price'=>$content_data['total'], 'price_otax'=>$content_data['total_otax'], 'format'=>true, 'format_type'=>'default'));		
		}
		
		if($customers_status->customers_status_show_price_tax=='1'){
			$this->content_total_physical = $price->_Format(array('price'=>$content_data['total_physical']+$add_content_tax, 'price_otax'=>$content_data['total_otax_physical'], 'format'=>true, 'format_type'=>'default'));
		}else{
			$this->content_total_physical = $price->_Format(array('price'=>$content_data['total_physical'], 'price_otax'=>$content_data['total_otax_physical'], 'format'=>true, 'format_type'=>'default'));	
		}
		
		if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1'){
			$this->content_tax = $tmp_content_tax;
		}else{
			unset($this->content_tax);
		}

		$this->content_weight = $content_data['weight'];
		$this->content_weight_physical = $content_data['weight_physical'];

		$this->content_count = $content_data['count'];

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_refresh_content')) ? eval($plugin_code) : false;

        if (isset($content_data['discount'])) {
            $this->discount = $price->_Format(array('price'=>$content_data['discount'], 'format'=>true, 'format_type'=>'default'));
        } else {
            $this->discount = 'false';
        }
		// Add SubContent:
		
		$this->show_sub_content = $sub_content_data['content'];

        $tmp_sub_content_tax = array();
		if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1'){
			$sub_content_tax_data = $tax->data;
	
			foreach($sub_content_tax_data as $key => $value){
				if(isset($sub_content_data['tax'][$key]) && $sub_content_data['tax'][$key] != 0)
                {
                    $tmp_sub_content_tax[$key] = array('tax_value' => $price->_Format(array('price' => $sub_content_data['tax'][$key], 'format' => true, 'format_type' => 'default')), 'tax_key' => round($value, $currency->decimals));
                    $sub_content_tax_sum += $sub_content_data['tax'][$key];
                }
			}
		}
		
		if($customers_status->customers_status_show_price_tax!='1' && $customers_status->customers_status_add_tax_ot=='1'){
			$add_sub_content_tax = $sub_content_tax_sum;
		}else{
			$add_sub_content_tax = 0;
		}
		
		if($customers_status->customers_status_show_price_tax=='1'){
			$this->sub_content_total = $price->_Format(array('price'=>$sub_content_data['total']+$add_sub_content_tax, 'price_otax'=>$sub_content_data['total_otax'], 'format'=>true, 'format_type'=>'default'));
		}else{
			$this->sub_content_total = $price->_Format(array('price'=>$sub_content_data['total'], 'price_otax'=>$sub_content_data['total_otax'], 'format'=>true, 'format_type'=>'default'));
		}		
		
		if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1'){
			$this->sub_content_tax = $tmp_sub_content_tax;
		}else{
			unset($this->sub_content_tax);
		}
		
		$this->sub_content_weight = $sub_content_data['weight'];
		$this->sub_content_count = $sub_content_data['count'];

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_refresh_sub_content')) ? eval($plugin_code) : false;

		// Add Total:

        $tmp_tax = array();
		$tax_content_tax_data = $tax->data;
		foreach($tax_content_tax_data as $key => $value){
		    if(isset($this->content_tax[$key]) || isset($this->sub_content_tax[$key]))
            {
                if ($this->content_tax[$key]['tax_value']['plain'] != 0 || $this->sub_content_tax[$key]['tax_value']['plain'] != 0)
                {
                    $tax_key_price = (isset($this->content_tax[$key]) ? $this->content_tax[$key]['tax_value']['plain'] : 0);
                    $tax_key_price += (isset($this->sub_content_tax[$key]) ? $this->sub_content_tax[$key]['tax_value']['plain'] : 0);
                    $tmp_tax[$key] = array(
                        'tax_value' => $price->_Format(
                            array(
                            'price' => $tax_key_price,
                            'format' => true,
                            'format_type' => 'default')),
                            'tax_key' => round($value, $currency->decimals));
                }
            }
		}

		if($customers_status->customers_status_show_price_tax=='1'){
			$this->total = $price->_Format(array('price'=>$this->content_total['plain'] + $this->sub_content_total['plain'], 'format'=>true, 'format_type'=>'default'));
			$this->total_physical = $price->_Format(array('price'=>$this->content_total_physical['plain'] + $this->sub_content_total['plain'], 'format'=>true, 'format_type'=>'default'));
		}else{
			$this->total = $price->_Format(array('price'=>$this->content_total['plain'] + $this->sub_content_total['plain'] + $add_content_tax + $add_sub_content_tax, 'format'=>true, 'format_type'=>'default'));
			$this->total_physical = $price->_Format(array('price'=>$this->content_total_physical['plain'] + $this->sub_content_total['plain'] + $add_content_tax + $add_sub_content_tax, 'format'=>true, 'format_type'=>'default'));
		}

		if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1')
		$this->tax = $tmp_tax;

		$this->weight = $this->content_weight + $this->sub_content_weight;
		$this->addCartStat();
		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_refresh_bottom')) ? eval($plugin_code) : false;
	}

	/**
	 * check if cart amount is in price range set for customers group, redirect to cart if position != cart
	 *
	 * @param string $position
	 * @return array
	 */
	function _checkCustomersStatusRange($position='cart') {
		global $xtPlugin,$customers_status,$price,$info,$xtLink;
        
		$total_value = $this->content_total['plain'];

		$min_order_value = $customers_status->customers_status_min_order;
		$max_order_value = $customers_status->customers_status_max_order;

		// convert to other currency if other selected
		$min_order_value = $price->_calcCurrency($min_order_value);
		$max_order_value = $price->_calcCurrency($max_order_value);

		$block = false;

        // check if min order value has been reached
		if ($min_order_value>0) {
			if ($total_value<$min_order_value) {
				$amount_left = $min_order_value-$total_value;
				$amount_left = $price->_StyleFormat($amount_left);
				$min_order_value = $price->_StyleFormat($min_order_value);
				$info->_addInfo(sprintf(__text('ERROR_MIN_ORDER_VALUE'),$min_order_value,$amount_left),'error');
				$block = true;
			}
		}

        // check if max order value has been reached
		if ($max_order_value>0) {
			if ($total_value>$max_order_value) {
				$amount_left = $total_value-$max_order_value;
				$amount_left = $price->_StyleFormat($amount_left);
				$max_order_value = $price->_StyleFormat($max_order_value);
				$info->_addInfo(sprintf(__text('ERROR_MAX_ORDER_VALUE'),$max_order_value,$amount_left),'error');
				$block = true;
			}
		}

        // redirect to cart if one max/min value has been reached
		if ($position!='cart' && $block==true) {
			$xtLink->_redirect($xtLink->_link(array('page'=>'cart')));
		}
		if ($block) return 'true';
		return 'false';

	}

    /**
    * add product to cart
    * 
    * @param mixed $data
    */
	function _addCart($data){
		global $xtPlugin;

        $data['product'] = (int) $data['product'];
        if($data['product'] < 1) return false;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$check_type = $this->_checkAddType($data);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addCart_Check')) ? eval($plugin_code) : false;

        // set qty to 1 if qty entered is < 0
		if ($data['qty']<0 || (isset($data['products_digital']) && $data['products_digital'] ==1) ) $data['qty']=1;
		
		if($check_type['type']=='insert'){  // insert new product
			$p = $this->_addToCart($data);
		}elseif($check_type['type']=='update'){    // update product
			
			$data['qty'] = $data['qty'] + $this->content[$check_type['key']]['products_quantity'];
			$p= product::getProduct($this->content[$check_type['key']]['products_id']);

			if ($p->data['products_digital']==1)$data['qty']=1;
			    $data['products_key'] = $check_type['key'];

			$this->_updateCart($data);

			$this->addCartStat();
		}else{
			return false;
		}

		return $p;
	}

    /**
    * check if given product is allready in cart
    *
    * @param mixed $data
    */
	private function _checkAddType($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_checkAddType_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		// set default type => insert
		$check_type = array('type'=>'insert');

		$new_products_key = $this->_genProductsKey($data);

		$contents = $this->content;

		// check if actual $new_products_key set
        foreach($contents as $key => $value) {
			if($new_products_key == $value['products_key']){
				// product is on cart
				$check_type = array('type'=>'update', 'key'=>$new_products_key);
				break;
			}
		}

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_checkAddType_bottom')) ? eval($plugin_code) : false;

		return $check_type;
	}

	/**
	 * determine if cart content is physical or virtual
	 *
	 * @param unknown_type $product_digital
	 */
	private function _checkCartContent($product_digital=0) {
		if ($product_digital=='1') {
			if ($this->type=='') $this->type='virtual';
			if ($this->type=='physical') $this->type = 'mixed';
		} else {
			if ($this->type=='') $this->type='physical';
			if ($this->type=='virtual') $this->type='mixed';
		}
	}

	/**
	 * add product to cart (check for stock etc)
	 *
	 * @param int $data
	 */
	private function _addToCart($data){
		global $xtPlugin, $price, $store_handler, $db,$info,$xtLink;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addToCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;


		$data['qty'] = $this->_filter_qty($data['qty']);
		if ($data['qty']<0) $data['qty']=1;

		if(!empty($data['product'])){
			$cart_product = product::getProduct($data['product'], 'default');

			$data['type'] = 'product';
			$data['status'] = 1;
			if ( $cart_product->data['products_digital'] ==1) $data['qty']=1;
		}

		if (USER_POSITION == 'store' && ($cart_product->data['allow_add_cart']===false || $cart_product->is_product===false)) {
			$info->_addInfoSession(__text('ERROR_NOT_ALLOWED_TO_PURCHASE'),'error');
			$link_array = array('page'=>'cart');
			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addToCart_notallowed')) ? eval($plugin_code) : false;
			$xtLink->_redirect($xtLink->_link($link_array));
		}

		// check content type
		$this->_checkCartContent($cart_product->data['products_digital']);

		$stock = new stock();
		$new_stock=$stock->stockCheck($cart_product,$data['qty'], false);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addToCart_stockcheck')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if ($new_stock != 0){
			$data['qty'] = $new_stock;
		}else{
			return false;
		}

		if(!empty($data['shop'])){
			$data['shop'] = $data['shop'];
		}else{
			$data['shop'] = $store_handler->shop_id;
		}

		if(!empty($data['customer_id'])){
			$data['customer_id'] = $data['customer_id'];
		}else{
			$data['customer_id'] = isset($_SESSION['registered_customer']) ? $_SESSION['registered_customer'] : null;
		}

		if(empty($data['products_key']))
		$data['products_key'] = $this->_genProductsKey($data);

		if(!empty($data['products_info']))
		$data['products_info'] = serialize($data['products_info']);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addToCart_before_data')) ? eval($plugin_code) : false;

		$this->content[$data['products_key']] = array(
			'customers_id' => $data['customer_id'],
			'products_id'=> $data['product'],
			'products_key'=> $data['products_key'],
			'products_quantity' => $data['qty'],
			'products_info' => isset($data['products_info']) ? $data['products_info'] : null,
			'products_digital'=>$cart_product->data['products_digital'],
			'type' => $data['type'],
			'status' => $data['status'],
			'sort_order' => isset($data['sort_order']) ? $data['sort_order'] : 0,
			'shop_id' => $data['shop']
		);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addToCart_data')) ? eval($plugin_code) : false;

		if( ! empty($_SESSION['registered_customer']))
		{
			$insert_record = array('date_added'=>$db->BindTimeStamp(time()));
			$record = array_merge($insert_record, $this->content[$data['products_key']]);
			if( ! $this->_existBacketID($record))
				$db->AutoExecute(TABLE_CUSTOMERS_BASKET, $record, 'INSERT');
		}
		$this->addCartStat();

        return $cart_product;
	}

	function addCartStat() {
		if(empty($_SESSION['registered_customer'])) {
			return;
		}
		global $db;
		$data =  array();
		$total_qty = 0;
		if(is_array($_SESSION['cart']->content))
		{
			foreach ($_SESSION['cart']->content as $product_data) {
				$total_qty += $product_data['products_quantity'];
			}
		}
		$query = "DELETE FROM ".TABLE_SALES_STATS." WHERE customers_id=? AND date_added>=? AND sales_stat_type=0";
		$db->Execute(
			$query,
			array($_SESSION['customer']->customers_id, date('Y-m-d h:i:s', strtotime('-24 hours')))
		);
		if ($total_qty <= 0) {
			return;
		}
		$data['sales_stat_type'] = 0; // Not checkouted
		$data['shop_id'] = $_SESSION['customer']->customer_info['shop_id'];
		$data['customers_status'] = $_SESSION['customer']->customers_status;
		$data['customers_id'] = $_SESSION['customer']->customers_id;
		$data['products_count'] = $total_qty;
		$data['date_added'] = date('Y-m-d h:i:s', strtotime('now'));
		$db->AutoExecute(TABLE_SALES_STATS, $data, 'INSERT');
	}

	function _updateCart($data){
		global $xtPlugin, $db,$info;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_updateCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		//__debug($data);
		if (isset($_SESSION['selected_shipping'])) unset($_SESSION['selected_shipping']);
		if (is_array($this->content[$data['products_key']])) $data = array_merge($this->content[$data['products_key']], $data);
		$data['qty'] = $this->_filter_qty($data['qty']);
		$p= product::getProduct($this->content[$data['products_key']]['products_id']);

		if ($data['qty']<0 || ($p->data['products_digital']==1)) $data['qty']=1;


		if(!empty($data['customer_id'])){
			$data['customer_id'] = $data['customer_id'];
		}else{
			$data['customer_id'] = isset($_SESSION['registered_customer']) ? $_SESSION['registered_customer'] : null;
		}

		$this->content[$data['products_key']]['products_quantity'] = $data['qty'];

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_updateCart_data')) ? eval($plugin_code) : false;

		if(!empty($_SESSION['registered_customer'])){
			$data_array = array('products_quantity'=>$data['qty']);

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_updateCart_dbdata')) ? eval($plugin_code) : false;
			$db->AutoExecute(TABLE_CUSTOMERS_BASKET,$data_array, 'UPDATE', "products_key=".$db->Quote($data['products_key'])." and customers_id=".(int)$_SESSION['registered_customer']);
		}

	}

	function _genProductsKey($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_genProductsKey_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$product_data = '_XT';
		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_genProductsKey_data')) ? eval($plugin_code) : false;

		$products_key = $data['product'].$product_data;

		return $products_key;
	}


	function _getContent($store='')
	{
		global $xtPlugin, $product, $price, $tax, $customers_status,$info, $currency, $db, $language, $store_handler, $order_edit_controller;;

		$discount = 0;

		$quantites = array();

		$this->cart_total_full = 0;
		$this->total_discount = 0;
		$content_data = $this->content;

		foreach ($content_data as $k => $p)
		{
		    if(!isset($quantites[$p['products_id']]))
                $quantites[$p['products_id']] = 0;
			$quantites[$p['products_id']] += $p['products_quantity'];
		}

		if (isset($quantites) && is_array($quantites))
		{
			foreach ($quantites as $key => $q)
			{
				$content_product = new product($key, 'default', $q);
				$stock = new stock();
				$new_stock = $stock->stockCheck($content_product, $q, false);
				// false wg. erneutem stockCheck weiter unten.

				if($content_product->is_product!=true)
				{
					if ( ! empty($content_product->data['products_name']))
					{
						$pname = $content_product->data['products_name'];
					} else {
						$record = $db->Execute(
							"SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = ? AND language_code = ? AND products_store_id = ?",
							array((int)$content_product->pID, $language->code, $store_handler->shop_id)
						);
						$pname = ($record->RecordCount() == 1)
							? $record->fields['products_name']
							: '';
					}
					$removeKey = $a = $this->_genProductsKey(array('product' =>  $content_product->pID));
					$this->_deleteContent($removeKey);
					unset($content_data[$removeKey]);
					// meldung __text('ERROR_STOCK_REDUCED_AMOUNT') nur wenn > 0, ohne check kam zB '..wurde um 3 stück reduziert' und gleichzeitig '..wurde um 0 stück reduziert'
					if($new_stock>0) {
					    $info->_addInfo(sprintf(__text('ERROR_STOCK_REDUCED_AMOUNT'), $pname, $new_stock), 'info');
                    }
					continue;
				}

				if ($new_stock != 0 && $q!=$new_stock)
				{
					$content_product = new product($key, 'default', $new_stock);
				}

				$cart_products[$key] = $content_product;
			}
		}

        foreach($content_data as $key => $value)
		{
			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_pre_value')) ? eval($plugin_code) : false;

//			$content_product = product::getProduct($value['products_id'], 'default', $value['products_quantity']);
			$content_product = $cart_products[$value['products_id']];
			$product_data = $content_product->data;
			unset($product_data['products_quantity']);

			// product->builddata im cart wenn postion=admin

			if ( ! is_array($cart_products[$value['products_id']]->data['products_price']))
			{
				global $currency;

				if(!$order_edit_controller || !$order_edit_controller->isActive())
				    $tmpProduct = product::getProduct($value['products_id'], 'default', $cart_products[$value['products_id']]->qty);
				else
                    $tmpProduct = new product($value['products_id'], 'default', $cart_products[$value['products_id']]->qty);
				$tmpProduct->buildData();

				$cart_products[$value['products_id']] = $tmpProduct;
				$product_data = $tmpProduct->data;
				unset($product_data['products_quantity']);
			}

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_value_data')) ? eval($plugin_code) : false;

			$value = array_merge($value, $product_data);
			$this->cart_total_full += $value['products_price']['plain_otax']*$value['products_quantity'];
		}

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_top')) ? eval($plugin_code) : false;
		if (isset($plugin_return_value)) return $plugin_return_value;

		$content_data_products = array();
		$content_data_tax = array();
		$content_data_total = 0;
		$content_data_total_physical = 0;
		$content_data_weight = 0;
		$content_data_weight_physical = 0;

		$content_data = $this->content;
		$this->type = '';

		$content_data_total_otax = 0;
		$content_data_count = 0;
		$content_data_total_otax_physical = 0;

        foreach($content_data as $key => $value) {

            $discount = 0;

  			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_before_product')) ? eval($plugin_code) : false;

			$content_product = $cart_products[$value['products_id']];
			$product_data = $content_product->data;

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_after_product')) ? eval($plugin_code) : false;

			$stock = new stock();
			$new_stock=$stock->stockCheck($content_product, $value['products_quantity'],true);

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_stockcheck')) ? eval($plugin_code) : false;
			if(isset($plugin_return_value))
			return $plugin_return_value;

			if ($product_data['products_digital']) {
				$value['products_quantity'] = 1;
			}

			if ($new_stock != 0) {
				$value['products_quantity'] = $new_stock;
				($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_newStock_before_product')) ? eval($plugin_code) : false;
				/*
					$content_product = product::getProduct($value['products_id'], 'default', $value['products_quantity']);
				 */
				$product_data = $content_product->data;
				($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_newStock_after_product')) ? eval($plugin_code) : false;
				$content_data[$key]['products_quantity']=$value['products_quantity'];
				$this->content[$key]['products_quantity']=$value['products_quantity'];
			} else {
				$this->_deleteContent($key);
				unset($content_data[$key]);
				continue;
			}

			unset($product_data['products_quantity']);

			if ($content_product->data['allow_add_cart']==false) {
				$this->_deleteContent($key);
				unset($content_data[$key]);
				continue;
			}

			if (!isset($value['products_id'])) {
				$this->_deleteContent($key);
				unset($content_data[$key]);
				continue;
			}

			$this->_checkCartContent($product_data['products_digital']);

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_product_unset')) ? eval($plugin_code) : false;

			$value = array_merge($value, $product_data);

			if($customers_status->customers_status_show_price_tax=='1' || $customers_status->customers_status_add_tax_ot=='1'){
				$products_tax = $tax->data[$value['products_tax_class_id']];
			}

			$order_edit_controller->hook_cart_getContent_data($this);

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_data')) ? eval($plugin_code) : false;

            $oldPrice = $value['products_price']['original_price'];
            $oldPrice_otax = $value['products_price']['original_price_otax'];
            if($value["price_flag_graduated_".$price->p_group] == 1)
            {
                if(empty($value["products_price"]["old_plain"]))
                {
                    $oldPrice = $value["products_price"]["plain"];
                    $oldPrice_otax = $value["products_price"]["plain_otax"];
                }
                else if($oldPrice > $value["products_price"]["old_plain"])
                {
                    $oldPrice = $value["products_price"]["old_plain"];
                    $oldPrice_otax = $value["products_price"]["old_plain_otax"];
                }
            }


			$value['_original_products_price'] = $price->_Format(array('price'=>$oldPrice, 'price_otax'=>$oldPrice_otax, 'format'=>true, 'format_type'=>'default'));

           // echo $discount;
            if (isset($_SESSION['selected_payment_discount']) && isset ($_SESSION['selected_payment'])) {
                if (isset($_SESSION['selected_payment_discount'][$_SESSION['selected_payment']])) {
                    $discount+=$_SESSION['selected_payment_discount'][$_SESSION['selected_payment']];
                }
            }

			$regular_price = $value['products_price']['plain_otax'];
            //$regular_price_tax = $price->_calcTax($regular_price, $products_tax);
			$regular_price_tax = round($price->_calcTax($regular_price, $products_tax),2);
			$regular_price_final_tax = $regular_price_tax * $value['products_quantity'];

            // calculate discount
           // echo $discount;
            $value['products_price_before_discount']['plain_otax'] = $value['products_price']['plain_otax'];
            $value['products_price_before_discount']['plain'] = $value['products_price']['plain'];
            if ($discount>0 /*&& $value['flag_has_specials']!=true*/) {
            // save regular price, replace plain_otax with discount price


                if(($order_edit_controller && $order_edit_controller->isActive()) || USER_POSITION == 'store')
                {
                    $value['products_price']['plain_otax'] = $price->_getPriceDiscount($value['products_price']['plain_otax'],$discount);
                }
             }

			// Calc Price
			// dev7 $value['products_price_otax'] = $value['products_price']['plain_otax'];
            $precision = 6;
            if($order_edit_controller->isActive()) $precision = 3;
            $value['products_price_otax'] = round($value['products_price']['plain_otax'],$precision );
			$value['products_final_price_otax'] = $value['products_price_otax'] * $value['products_quantity'];
			$value['products_final_price_otax'] = $value['products_final_price_otax'] + (isset($value['add_single_price']) ? $value['add_single_price'] : 0);

			if ($customers_status->customers_status_show_price_tax == '1') {
				$value['products_price'] = $price->_AddTax($value['products_price_otax'], $products_tax);
				$value['products_price'] = $price->_roundPrice($value['products_price'],2);
				$value['add_single_price'] = $price->_AddTax(isset($value['add_single_price']) ? $value['add_single_price'] : 0, $products_tax);
				$value['products_final_price']  = $value['products_price'] * $value['products_quantity'];
				$value['products_final_price']  = $value['products_final_price'] + (isset($value['add_single_price']) ? $value['add_single_price'] : 0);
			}else{
				$value['products_price'] = $value['products_price_otax'];
				$value['products_price'] = $price->_roundPrice($value['products_price'], 2);
				$value['products_final_price']  = $value['products_price'] * $value['products_quantity'];
				$value['products_final_price']  = $value['products_final_price'] + $value['add_single_price'];
			}

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_price')) ? eval($plugin_code) : false;

            if ($value['flag_has_specials']==true)
            {
                $priceBeforeSpecial = $value['_original_products_price']['plain'] ? $value['_original_products_price']['plain'] : round(($regular_price+$regular_price_tax),2);
                if($value['_original_products_price']['plain'])
                {
                    $saving = ($regular_price+$regular_price_tax - $priceBeforeSpecial             ) * $value['products_quantity'];
                }
                else {
                    $saving = ($regular_price+$regular_price_tax - $value['special_price']['plain']) * $value['products_quantity'];
                }

                $this->total_discount+= $saving;
            }
            else if (isset($value['group_price']) && is_array($value['group_price']))
            {
                $saving = ($regular_price+$regular_price_tax -$oldPrice) * $value['products_quantity'];

                $this->total_discount+= $saving;
            }

            // calculate Total Discount made in cart
            if ($discount>0 /*&& $value['flag_has_specials']!=true*/) {
                $final = $regular_price*$value['products_quantity'];
                if($customers_status->customers_status_show_price_tax == 1)  $final += $regular_price_final_tax;
                $saving = $value['products_final_price']-$final;
                $this->total_discount+=$saving;
            }

            // Calc Tax
            if($customers_status->customers_status_show_price_tax=='1'){
            	$value['products_tax_value'] = $products_tax;
            	//$value['products_tax'] = round($value['products_price'] - $price->_removeTax($value['products_price'], $products_tax), 2);
                //$value['products_tax_single'] = round($price->_calcTax($value['add_single_price'], $products_tax), 2);
                // juli 2023
                $value['products_tax'] = round($value['products_price'] - $value["products_price_otax"], 2);
                $value['products_tax_single'] = round($value['products_price'] - $value["products_price_otax"], 2);
            	$value['products_final_tax'] = $value['products_tax_single'] * $value['products_quantity'];;
            }elseif( $customers_status->customers_status_add_tax_ot=='1'){
            	$value['products_tax_value'] = $products_tax;
            	$value['products_tax'] = round($price->_calcTax($value['products_price_otax'], $products_tax),2);
            	$value['products_tax_single'] = round($price->_calcTax($value['add_single_price'], $products_tax),2);
            	$value['products_final_tax'] = $value['products_tax'] * $value['products_quantity'];
            	//$value['products_final_tax'] = $value['products_final_tax'] + $value['products_tax_single'];
            }else{
            	$value['products_tax'] = 0;
            	$value['products_final_tax'] = 0;
            }

            ($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_tax')) ? eval($plugin_code) : false;

			// Format Price

			$value['products_tax'] = $price->_Format(array('price'=>$value['products_tax'], 'format'=>true, 'format_type'=>'default'));
			$value['products_final_tax'] = $price->_Format(array('price'=>$value['products_final_tax'], 'format'=>true, 'format_type'=>'default'));

			$value['products_price'] = $price->_Format(array('price'=>$value['products_price'], 'price_otax'=>$value['products_price_otax'], 'format'=>true, 'format_type'=>'default'));
			$value['products_final_price'] = $price->_Format(array('price'=>$value['products_final_price'], 'price_otax'=>$value['products_final_price_otax'], 'format'=>true, 'format_type'=>'default'));


			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_data_format')) ? eval($plugin_code) : false;

			if($value['products_info']){
				$arr = unserialize($value['products_info']);
				$value['products_info_data'] = $arr;

				if (is_array($arr))
				foreach ($arr as $tkey => $tval) {
					$value['products_info_options'][] = array('text'=>'TEXT_'.strtoupper($tkey), 'data'=>$tkey,  'value'=>$tval);
				}

                ($plugin_code = $xtPlugin->PluginCode('class.cart.php:_products_info_options')) ? eval($plugin_code) : false;
			}

            // price discount ?
            if ($oldPrice_otax>$value['products_price']['plain_otax'])
			{
				$product_discount = abs((100*($value['products_price']['plain_otax']-$oldPrice_otax)) / $oldPrice_otax);
				$product_discount = round($product_discount,2);


                $decimals = ceil($product_discount) != $product_discount ? 1 : 0;
				$product_discount_formated = number_Format($product_discount, $decimals, $currency->dec_point, $currency->thousands_sep);

                $value['_cart_discount'] = $product_discount_formated;
                $value['_cart_discount_exact']=number_Format($product_discount, 2, $currency->dec_point, $currency->thousands_sep);
                $value['_cart_discount_data'] = array(
                    'plain' => $product_discount,
                    'formated' => $product_discount_formated);
            }

			$content_data_products[$key] = $value;

			if(!isset($content_data_tax[$value['products_tax_class_id']]))
                $content_data_tax[$value['products_tax_class_id']] = 0;
			$content_data_tax[$value['products_tax_class_id']] += $value['products_final_tax']['plain'];
			$content_data_total += $value['products_final_price']['plain'];
			$content_data_total_otax += $value['products_final_price']['plain_otax'];
			$content_data_weight += ($value['products_weight']*$value['products_quantity']);
			$content_data_count += $value['products_quantity'];

			// physical product ?
			if ($product_data['products_digital']!=1) {

				$content_data_total_physical += $value['products_final_price']['plain'];
				$content_data_total_otax_physical += $value['products_final_price']['plain_otax'];
				$content_data_weight_physical += ($value['products_weight']*$value['products_quantity']);

			}

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_data_bottom')) ? eval($plugin_code) : false;

		}
		$content_data = array(
			'products' => $content_data_products,
			'tax' => $content_data_tax,
			'total' => $content_data_total,
			'total_physical' => $content_data_total_physical,
			'total_otax' => $content_data_total_otax,
			'total_otax_physical'=>$content_data_total_otax_physical,
			'weight' => $content_data_weight,
			'weight_physical'=>$content_data_weight_physical,
			'count' => $content_data_count
		);


		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getContent_bottom')) ? eval($plugin_code) : false;

        if ($this->total_discount<0) {
            $this->total_discount = abs($this->total_discount);
            $content_data = array_merge($content_data,array('discount'=>$this->total_discount));
        }
	//	__debug($content_data);
        return $content_data;
	} // _getContent()

	function _addSubContent($data){
		global $xtPlugin, $price, $tax;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addSubContent_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if (empty($data['type'])) return false;

		if(empty($data['status']))
		$data['status'] = 0;

		$this->sub_content[$data['type']] = array(
			'customers_id' => $data['customer_id'],
			'products_name' => $data['name'],
			'products_key'=> $data['type'],
			'products_key_id'=> $data['key_id'],
			'products_model' => $data['model'],
			'products_quantity' => $data['qty'],
			'products_price' => $data['price'],
			'products_tax_class' => $data['tax_class'],
			'products_discount' => $data['dicount'],
			'type' => $data['type'],
			'status' => $data['status'],
			'sort_order' => $data['sort_order'],
			'shop_id' => $data['shop']
		);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_addSubContent_bottom')) ? eval($plugin_code) : false;

	}

	function _getSubContent($store=''){
		global $xtPlugin, $product, $price, $tax, $customers_status, $language;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$sub_content_data = array();
		$sub_content_data_tax = array();
		$sub_content_data_total = 0;
		$sub_content_data_weight = 0;

		$sub_content = $this->sub_content;
		$sub_content_data_total_otax = 0;
		$sub_content_data_count = 0;

        foreach($sub_content as $key => $value) {

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_data')) ? eval($plugin_code) : false;

			if(empty($value['products_quantity']))
			$value['products_quantity'] = 1;

            $value['products_name'] = $this->_updateProductName($value['type'], $value['products_key_id'], $language->code, $value['products_name']);

            $products_tax = $tax->data[$value['products_tax_class']];

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_data')) ? eval($plugin_code) : false;

			// Calc Price
			$value['products_price_otax'] = $value['products_price'];
			$value['products_final_price_otax'] = $value['products_price'] * $value['products_quantity'];
			if ($customers_status->customers_status_show_price_tax == '1') {
				$value['products_price'] = $price->_AddTax($value['products_price_otax'], $products_tax);
				$value['products_price'] = $price->_roundPrice($value['products_price']);
				$value['products_final_price']  = $value['products_price'] * $value['products_quantity'];
			}else{
				$value['products_price'] = $price->_roundPrice($value['products_price_otax']);
				$value['products_final_price']  = $value['products_price'] * $value['products_quantity'];
			}


			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_price')) ? eval($plugin_code) : false;

			// Calc Tax
			if($customers_status->customers_status_show_price_tax=='1'){
				$value['products_tax_value'] = $products_tax;
				$value['products_tax'] = $value['products_price'] - $price->_removeTax($value['products_price'], $products_tax);
				$value['products_final_tax'] = $value['products_final_price'] - $price->_removeTax($value['products_final_price'], $products_tax);
			}elseif( $customers_status->customers_status_add_tax_ot=='1'){
            	$value['products_tax_value'] = $products_tax;
            	$value['products_tax'] = $price->_roundPrice($price->_calcTax($value['products_price'], $products_tax));
            	$value['products_tax_single'] = $price->_roundPrice($price->_calcTax($value['add_single_price'], $products_tax));
            	$value['products_final_tax'] = $value['products_tax'] * $value['products_quantity'];
            	$value['products_final_tax'] = $value['products_final_tax'] + $value['products_tax_single'];
            }else{
            	$value['products_tax'] = 0;
            	$value['products_final_tax'] = 0;
            }

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_tax')) ? eval($plugin_code) : false;

			// Format Price

			$value['products_tax'] = $price->_Format(array('price'=>$value['products_tax'], 'format'=>true, 'format_type'=>'default'));
			$value['products_final_tax'] = $price->_Format(array('price'=>$value['products_final_tax'], 'format'=>true, 'format_type'=>'default'));

			$value['products_price'] = $price->_Format(array('price'=>$value['products_price'], 'price_otax'=>$value['products_price_otax'], 'format'=>true, 'format_type'=>'default'));
			$value['products_final_price'] = $price->_Format(array('price'=>$value['products_final_price'], 'price_otax'=>$value['products_final_price_otax'], 'format'=>true, 'format_type'=>'default'));

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_data_format')) ? eval($plugin_code) : false;

			$sub_content_data[$key] = $value;

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_data_bottom')) ? eval($plugin_code) : false;

			$sub_content_data_tax[$value['products_tax_class']] += $value['products_final_tax']['plain'];
			$sub_content_data_total += $value['products_final_price']['plain'];
			$sub_content_data_total_otax += $value['products_final_price']['plain_otax'];
			$sub_content_data_weight += $value['products_weight'];
			$sub_content_data_count += $value['products_quantity'];
		}

		$content_data = array(
			'content' => $sub_content_data,
			'tax' => $sub_content_data_tax,
			'total' => $sub_content_data_total,
			'total_otax' => $sub_content_data_total_otax,
			'weight' => $sub_content_data_weight,
			'count' =>$sub_content_data_count
		);

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_getSubContent_bottom')) ? eval($plugin_code) : false;
		return $content_data;
	}

    public function _updateProductName($type, $id, $lang_code, $old_name){
        global $db;

        $sub_content_pname = $old_name;
        switch ($type){
            case 'shipping':
                $rs = $db->Execute(
					"SELECT * FROM ".TABLE_SHIPPING_DESCRIPTION . " WHERE shipping_id=? AND language_code=? LIMIT 1",
					array($id, $lang_code)
				);
                if($rs->RecordCount() > 0){
                    $sub_content_pname = $rs->fields['shipping_name'];
                }
                break;
            case 'payment' :
                $rs = $db->Execute(
					"SELECT * FROM ".TABLE_PAYMENT_DESCRIPTION . " WHERE payment_id=? AND language_code=? LIMIT 1",
					array($id, $lang_code)
				);
                if($rs->RecordCount() > 0){
                    $sub_content_pname = $rs->fields['payment_name'];
                }
                break;
        }
        return $sub_content_pname;
    }

	function _resetCart(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_resetCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		unset($_SESSION['cart']);

		if(!empty($_SESSION['registered_customer'])){
			$db->Execute("DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE customers_id = ?", array((int)$_SESSION['registered_customer']));
			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_resetCart_db_bottom')) ? eval($plugin_code) : false;
		}

	}

	function _deleteContent($key){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_deleteContent_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		unset($_SESSION['cart']->content[$key]);

		if(!empty($_SESSION['registered_customer'])){
			$db->Execute(
				"DELETE FROM ".TABLE_CUSTOMERS_BASKET." WHERE customers_id = ? and products_key =?",
				array((int)$_SESSION['registered_customer'], $key)
			);
			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_deleteContent_db_bottom')) ? eval($plugin_code) : false;
			$this->_deleteSubContent('shipping');
		}
	}

	function _deleteSubContent($key){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_deleteSubContent_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		unset($_SESSION['cart']->sub_content[$key]);
	}

	function _restore(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_restore_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->_syncCart();
		$this->_rewriteCart();
	}

	function _syncCart(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_syncCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data = $this->content;

        foreach($data as $key => $value) {

			$record = $db->Execute(
				"select * from ".TABLE_CUSTOMERS_BASKET." WHERE customers_id = ? and products_key = ?",
				array((int)$_SESSION['registered_customer'], $value['products_key'])
			);
			if($record->RecordCount() > 0){

				if(!empty($value['products_quantity'])){
					$new_qty = $value['products_quantity']+$record->fields['products_quantity'];
				}else{
					$new_qty = $record->fields['products_quantity'];
				}

				$upd_data = array('products_key'=>$value['products_key'], 'qty'=>$new_qty);
				($plugin_code = $xtPlugin->PluginCode('class.cart.php:_syncCart_update')) ? eval($plugin_code) : false;
				$this->_updateCart($upd_data);

			}
			elseif (is_array($data[$key]))
			{
					$insert_record = array('customers_id'=>$_SESSION['registered_customer'], 'date_added'=>$db->BindTimeStamp(time()));
					$record = array_merge($data[$key], $insert_record);
					($plugin_code = $xtPlugin->PluginCode('class.cart.php:_syncCart_insert')) ? eval($plugin_code) : false;

					if( ! $this->_existBacketID($record))
						$db->AutoExecute(TABLE_CUSTOMERS_BASKET,$record, 'INSERT');
			}

			($plugin_code = $xtPlugin->PluginCode('class.cart.php:_syncCart_bottom')) ? eval($plugin_code) : false;
		}
	}

	function _rewriteCart(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_rewriteCart_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->content = array();

		$sql = "select * from ".TABLE_CUSTOMERS_BASKET." WHERE customers_id = ?";

		($plugin_code = $xtPlugin->PluginCode('class.cart.php:_rewriteCart_sql')) ? eval($plugin_code) : false;

		$record = $db->Execute($sql, array((int)$_SESSION['registered_customer']));
		if($record->RecordCount() > 0){
			while(!$record->EOF){

				// check if product still available for customer group
				// but omit product->_buidData
				$tmp_p = product::getProduct(0);
				$tmp_p->pID = $record->fields['products_id'];
				$tmp_p->sql_products = new getProductSQL_query();
				$is_product = $tmp_p->getProductData('price','') != false;

				if($is_product)
				{
					if ($record->fields['products_quantity'] == round($record->fields['products_quantity']))
						$record->fields['products_quantity'] = (int)$record->fields['products_quantity'];
					$this->content[$record->fields['products_key']] = $record->fields;
				}

				$record->MoveNext();
			}$record->Close();
		}else{
			return false;
		}
		$this->_refresh();
	}

	function _filter_qty($qty){

		if(_STORE_ALLOW_DECIMAL_QUANTITIY=='true'){
			$qty = str_replace(',', '.', $qty);
		}else{
			$qty = (int)$qty;
		}

		return $qty;
	}

    public function _existBacketID($data){
        global $db;

        if(isset($data['basket_id'])){
            $sql = "SELECT * FROM ".TABLE_CUSTOMERS_BASKET." WHERE basket_id = ?";
            $record = $db->Execute($sql, array((int)$data['basket_id']));
            if($record->RecordCount() > 0){
                return true;
            }
        }

        return false;
    }
}