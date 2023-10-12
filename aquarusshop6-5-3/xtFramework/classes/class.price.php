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

class price {

    var $force_currency;

	function __construct($price_group, $master_price_group, $force_currency = '')
	{
		global $xtPlugin, $order_edit_controller;

		$order_edit_controller->hook_price_top($price_group, $master_price_group, $force_currency);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:price_top')) ? eval($plugin_code) : false;
		if (isset($plugin_return_value)) return $plugin_return_value;

		$this->price_group = $price_group;
        $this->master_price_group = $master_price_group;

        if ( ! empty($force_currency)) $this->force_currency = $force_currency;

        $price_group = empty($master_price_group)
            ? $price_group
            : $master_price_group;
		$this->p_group = $price_group;
	}

	function _setCurrency($curr){
		global $currency;
		
		$this->force_currency = $curr;
		$currency = new currency($curr);
		
	}

	function _getPrice($data){
		global $xtPlugin, $tax, $customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getPrice_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$products_tax = $tax->data[$data['tax_class']];

		if ($customers_status->customers_status_show_price_tax == '0')
		$products_tax = '';
		
		// Products Price
		$price = $data['price'];
		$format_type = 'default';

		// Check Currency
		if($data['curr']=='true')
		$price = $this->_calcCurrency($price);

		// Set Price without Tax
		$price_otax = $price;

		// Add Tax
		if(!empty($products_tax))
		$price = $this->_AddTax($price, $products_tax);
		
		$price = $this->_roundPrice($price);
		
		if(!empty($data['qty'])){
			$price = $price * $data['qty'];
			$price_otax = $price_otax * $data['qty'];
		}
		
		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getPrice_afterProductsPrice')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;


		$format_array = array('price'=>$price, 'price_otax'=>$price_otax, 'format'=>$data['format'], 'format_type' => 'default');
		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getPrice_Format')) ? eval($plugin_code) : false;
		$price_data = $this->_Format($format_array);
		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getPrice_bottom')) ? eval($plugin_code) : false;

		
		return $price_data;
	}

	function _AddTax($price, $tax) {
		global $xtPlugin;

		if(empty($price)) $price = 0;
        if(empty($tax)) $tax = 0;

        $price = floatval($price);
        $tax = floatval($tax);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_AddTax_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$price = $price + $price / 100 * $tax;
		return $price;
	}

	function _calcTax($price, $tax) {
		global $xtPlugin;

        if(empty($price)) $price = 0;
        if(empty($tax)) $tax = 0;

        $price = floatval($price);
        $tax = floatval($tax);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_calcTax_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		return $price * $tax / 100;
	}

	function _removeTax($price, $tax) {
		global $xtPlugin;

        if(empty($price)) $price = 0;
        if(empty($tax)) $tax = 0;

        $price = floatval($price);
        $tax = floatval($tax);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_removeTax_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$price = ($price / (($tax +100) / 100));
		return $price;
	}

	function _getTax($price, $tax) {
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getTax_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$tax = $price - $this->_removeTax($price, $tax);
		return $tax;
	}

	function _calcCurrency($price){
		global $xtPlugin, $currency;

        if(empty($price)) $price = 0;
        $price = floatval($price);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_calcCurrency_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if ($this->force_currency!='') {
			$currency = new currency($this->force_currency);
		}

		$multiplicator = floatval($currency->value_multiplicator);
		if($currency->default_currency == $currency->code){
            $price = $price*$multiplicator;
            return $price;
		}else{
			$price = $price*$multiplicator;
			return $price;
		}

	}

	function _getPriceDiscount($price,$discount) {
		global $xtPlugin;

        if(empty($price)) $price = 0;
        if(empty($discount)) $discount = 0;

        $price = floatval($price);
        $discount = floatval($discount);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getPriceDiscount_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if($discount==0) return $price;

		$price = $price - ($price/100*$discount);

		return $price;
	}

	function _getDiscount($price,$discount) {
		global $xtPlugin;

        if(empty($price)) $price = 0;
        if(empty($discount)) $discount = 0;

        $price = floatval($price);
        $discount = floatval($discount);

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_getDiscount_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        if($discount==0) return $price;

		$discount = $price/100*$discount;

		return $discount;
	}

	function _BuildPrice($pprice, $tax_class, $type='show', $roundPrecision = 2){
		global $tax;

		$tax_percent = $tax->data[$tax_class];

		$pprice = str_replace(',', '.', $pprice);

		if(_SYSTEM_USE_PRICE=='true'){

			if($type=='show'){
				$pprice = $this->_AddTax($pprice, $tax_percent);
				$pprice = $this->_roundPrice($pprice, $roundPrecision);
			}elseif($type=='save'){
				$pprice = $this->_removeTax($pprice, $tax_percent);
			}

		}
		
		return $pprice;
	}

	function getTaxClass($get_id, $table, $where_id, $where_value){
		global $db;

		$query = "SELECT ".$get_id." FROM ".$table."  WHERE ".$where_id."='".$where_value."'";
		$rs = $db->Execute($query);
		if ($rs->RecordCount()>0) {
			return $rs->fields[$get_id];
		}
	}

	function _roundPrice($price, $precision = false){
		global $xtPlugin, $currency;

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_calcCurrency_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!$precision)
		{
			$precision = 6;
		}
		$price = round($price, $precision);

		return $price;
	}	
	
	function _Format($data) {
		global $xtPlugin, $currency;

        /**
        static $format_counter;
        if(!$format_counter) $format_counter = 0;
        $format_counter++;
        $ex = new Exception('aeaserfaew');
        error_log('## format_counter '.$format_counter."\n". $ex->getTraceAsString());
         */

		($plugin_code = $xtPlugin->PluginCode('class.price.php:_Format_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        $price_plain = $data['price'];

		if($data['format']== true){

			switch ($data['format_type']) {

				case 'default':

					$price_plain = $data['price'];
					$price_otax = isset($data['price_otax']) ? $data['price_otax'] : 0;

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$tpl_data = array('PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax, 'date_available' => '', 'date_expired' => ''));
					$tpl = 'price.html';
				break;

				case 'special':

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];
					$old_price = $data['old_price'];
					$old_price_otax = $data['old_price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$old_price = (string)$old_price;
					$old_price = (float)$old_price;
					$old_Fprice= $this->_StyleFormat($old_price);

					$old_price_otax = (string)$old_price_otax;
					$old_price_otax = (float)$old_price_otax;
					$old_Fprice_otax= $this->_StyleFormat($old_price_otax);
					
					$saving_price = $old_price-$price_plain;
					$saving_price = (string)$saving_price;
					$saving_price = (float)$saving_price;
					$saving_Fprice= $this->_StyleFormat($saving_price);


					$tpl_data = array('SPECIAL_PRICE_SAVE'=>array('formated'=>$saving_Fprice,'plain'=>$saving_price),'SPECIAL_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'SPECIAL_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'OLD_PRICE'=>array('formated'=>$old_Fprice,'plain'=>$old_price),'OLD_PRICE_OTAX'=>array('formated'=>$old_Fprice_otax,'plain'=>$old_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$add_return_array = array('old_plain'=>$old_price,'old_plain_otax'=>$old_price_otax,'old_plain_formated'=>$old_Fprice, 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
                    $tpl = 'price_special.html';
				break;

				case 'graduated':

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];
					$cheapest_price = $data['cheapest_price'];
					$cheapest_price_otax = $data['cheapest_price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$cheapest_price = (string)$cheapest_price;
					$cheapest_price = (float)$cheapest_price;
					$cheapest_Fprice= $this->_StyleFormat($cheapest_price);

					$cheapest_price_otax = (string)$cheapest_price_otax;
					$cheapest_price_otax = (float)$cheapest_price_otax;
					$cheapest_Fprice_otax= $this->_StyleFormat($cheapest_price_otax);

					$tpl_data = array('PRODUCTS_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'PRODUCTS_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'CHEAPEST_PRICE'=>array('formated'=>$cheapest_Fprice,'plain'=>$cheapest_price),'CHEAPEST_PRICE_OTAX'=>array('formated'=>$cheapest_Fprice_otax,'plain'=>$cheapest_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$tpl = 'price_graduated.html';
					break;

				case 'graduated-table':

					$tpl_data = array('GRADUATED_PRICES'=>$data['prices']);
					$tpl = 'graduated_table.html';
					break;

				case 'graduated_ap': // master slave

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];
					$cheapest_price = $data['cheapest_price'];
					$cheapest_price_otax = $data['cheapest_price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$cheapest_price = (string)$cheapest_price;
					$cheapest_price = (float)$cheapest_price;
					$cheapest_Fprice= $this->_StyleFormat($cheapest_price);

					$cheapest_price_otax = (string)$cheapest_price_otax;
					$cheapest_price_otax = (float)$cheapest_price_otax;
					$cheapest_Fprice_otax= $this->_StyleFormat($cheapest_price_otax);

					$tpl_data = array('PRODUCTS_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'PRODUCTS_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'CHEAPEST_PRICE'=>array('formated'=>$cheapest_Fprice,'plain'=>$cheapest_price),'CHEAPEST_PRICE_OTAX'=>array('formated'=>$cheapest_Fprice_otax,'plain'=>$cheapest_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$tpl = 'price_graduated_ap.html';
					break;

				case 'graduated_discount_ap': // master slave

					$cheapest_price = $data['cheapest_price'];
					$cheapest_price_otax = $data['cheapest_price_otax'];

					$cheapest_price = (string)$cheapest_price;
					$cheapest_price = (float)$cheapest_price;
					$cheapest_Fprice= $this->_StyleFormat($cheapest_price);

					$cheapest_price_otax = (string)$cheapest_price_otax;
					$cheapest_price_otax = (float)$cheapest_price_otax;
					$cheapest_Fprice_otax= $this->_StyleFormat($cheapest_price_otax);

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];
					$old_price = $data['old_price'];
					$old_price_otax = $data['old_price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$old_price = (string)$old_price;
					$old_price = (float)$old_price;
					$old_Fprice= $this->_StyleFormat($old_price);

					$old_price_otax = (string)$old_price_otax;
					$old_price_otax = (float)$old_price_otax;
					$old_Fprice_otax= $this->_StyleFormat($old_price_otax);

					$saving_price = $old_price-$price_plain;
					$saving_price = (string)$saving_price;
					$saving_price = (float)$saving_price;
					$saving_Fprice= $this->_StyleFormat($saving_price);


					$tpl_data1 = array('SPECIAL_PRICE_SAVE'=>array('formated'=>$saving_Fprice,'plain'=>$saving_price),'SPECIAL_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'SPECIAL_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'OLD_PRICE'=>array('formated'=>$old_Fprice,'plain'=>$old_price),'OLD_PRICE_OTAX'=>array('formated'=>$old_Fprice_otax,'plain'=>$old_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$tpl_data2 = array('PRODUCTS_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'PRODUCTS_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'CHEAPEST_PRICE'=>array('formated'=>$cheapest_Fprice,'plain'=>$cheapest_price),'CHEAPEST_PRICE_OTAX'=>array('formated'=>$cheapest_Fprice_otax,'plain'=>$cheapest_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);

					$tpl_data = array_merge($tpl_data1, $tpl_data2);

					$tpl = 'price_graduated_discount_ap.html';
					break;

				case 'default_ap': // master slave

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$tpl_data = array('PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax, 'date_available' => '', 'date_expired' => ''));
					$tpl = 'price.html';
					break;

				case 'special_ap': // master slave

                    $price_plain = $data['price'];
					$price_otax = $data['price_otax'];
					$old_price = $data['old_price'];
					$old_price_otax = $data['old_price_otax'];

                    $price_plain = (string)$price_plain;
                    $price_plain = (float)$price_plain;
					$Fprice= $this->_StyleFormat($price_plain);

					$price_otax = (string)$price_otax;
					$price_otax = (float)$price_otax;
					$Fprice_otax= $this->_StyleFormat($price_otax);

					$old_price = (string)$old_price;
					$old_price = (float)$old_price;
					$old_Fprice= $this->_StyleFormat($old_price);

					$old_price_otax = (string)$old_price_otax;
					$old_price_otax = (float)$old_price_otax;
					$old_Fprice_otax= $this->_StyleFormat($old_price_otax);

					$saving_price = $old_price-$price_plain;
					$saving_price = (string)$saving_price;
					$saving_price = (float)$saving_price;
					$saving_Fprice= $this->_StyleFormat($saving_price);


					$tpl_data = array('SPECIAL_PRICE_SAVE'=>array('formated'=>$saving_Fprice,'plain'=>$saving_price),'SPECIAL_PRICE' => array('formated'=>$Fprice,'plain'=>$price_plain), 'SPECIAL_PRICE_OTAX'=>array('formated'=>$Fprice_otax,'plain'=>$price_otax),'OLD_PRICE'=>array('formated'=>$old_Fprice,'plain'=>$old_price),'OLD_PRICE_OTAX'=>array('formated'=>$old_Fprice_otax,'plain'=>$old_price_otax), 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$add_return_array = array('old_plain'=>$old_price,'old_plain_otax'=>$old_price_otax,'old_plain_formated'=>$old_Fprice, 'date_expired' => $data['date_expired'], 'date_available' => $data['date_available']);
					$tpl = 'price_special.html';
					break;

				default:
				($plugin_code = $xtPlugin->PluginCode('class.price.php:_FormatType_data')) ? eval($plugin_code) : false;
				if(isset($plugin_return_value))
				return $plugin_return_value;
			}


			$template = new Template();
			($plugin_code = $xtPlugin->PluginCode('class.price.php:_Format_data')) ? eval($plugin_code) : false;
			if(isset($plugin_return_value))
			return $plugin_return_value;
			
			$tpl_price = $template->getTemplate('price_smarty','/'._SRV_WEB_CORE.'pages/price/'.$tpl,$tpl_data);
            $return_array = array ('formated' => $tpl_price, 'plain' => $price_plain, 'plain_otax' => $price_otax, 'data' => $tpl_data);
            if (isset($add_return_array)) $return_array=array_merge($return_array,$add_return_array);
			return $return_array;

		} else {
            $price_plain = $data['price'];
			$price_otax = $data['price_otax'];
			//return array ('plain' => round($price, $currency->decimals), 'plain_otax' => round($price_otax, $currency->decimals));
			return array ('plain' => $price_plain, 'plain_otax' => $price_otax);
		}
	}

	function _StyleFormat($price) {
		global $currency;
		
		$Fprice = number_Format($price, $currency->decimals, $currency->dec_point, $currency->thousands_sep);
		$prefix = trim($currency->prefix);
		$prefix = !empty($prefix) ? $currency->prefix.' ' : '';
		$Fprice = $prefix.' '.$Fprice.' '.$currency->suffix;
		return trim($Fprice);
	}

	function buildPriceData($price, $tax_class_id = 0, $curr = true) {
	    global $tax;

	    $tax_rate = $tax->data[$tax_class_id];
		// Check Currency
		if($curr == true)
		$price = $this->_calcCurrency($price);
		// Set Price without Tax
		$price_otax = $price;
		// Add Tax
		$price = $this->_AddTax($price, $tax_rate);
        return array(
			'plain_otax' => $price_otax,
			'formated_otax' => $this->_StyleFormat($price_otax),
			'tax_rate' => $tax_rate,
			'tax' => $price - $price_otax,
			'formated_tax' => $this->_StyleFormat($price - $price_otax),
			'plain' => $price,
			'formated' => $this->_StyleFormat($price)
		);
	}
}