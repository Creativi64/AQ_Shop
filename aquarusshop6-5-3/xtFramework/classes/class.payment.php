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

class payment extends xt_backend_cls {

	public $lang;
	public $billing_address = array();
	public $products =  array();
	public $weight = 0;
	public $count = 0;
	public $total = array();
	public $group_permission;
	public $payment_data =  array();

	public $master_id = 'payment_id';

	protected $_table = TABLE_PAYMENT;
	protected $_table_lang = TABLE_PAYMENT_DESCRIPTION;

	protected $_table_seo = NULL;
	protected $_master_key = 'payment_id';

	function __construct() {
		global $xtPlugin;

		$this->getPermission();

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:payment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	}

	function getPermission(){
		global $store_handler, $customers_status, $xtPlugin;

		$this->perm_array = array(
			'shop_perm' => array(
				'type'=>'shop',
				'key'=>$this->_master_key,
				'value_type'=>'payment',
				'pref'=>'p'
			),
			'group_perm' => array(
				'type'=>'group_permission',
				'key'=>$this->_master_key,
				'value_type'=>'payment',
				'pref'=>'p'
			),
			'shipping_perm' => array(
				'type'=>'shipping_permission',
				'key'=>$this->_master_key,
				'value_type'=>'payment',
				'pref'=>'p'
			)
		);

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;

		$this->permission = new item_permission($this->perm_array);

		return $this->perm_array;
	}

	function _payment($data=''){
		global $xtPlugin, $price, $db, $language, $customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_payment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
		
		if(isset($data['language']) && !empty($data['language'])){
			$this->lang = $data['language'];
		}else{
			$this->lang = $language->code;
		}

		if(isset($data['customer_default_address']) && is_data($data['customer_default_address'])){
			$this->billing_address = $data['customer_default_address'];
		}else{
			$this->billing_address = $_SESSION['customer']->customer_payment_address;
		}

		if(isset($data['products']) && is_data($data['products'])){
			$this->products = $data['products'];
		}else{
			$this->products = $_SESSION['cart']->show_content;
		}

		if(isset($data['count']) && is_data($data['count'])){
			$this->count = $data['count'];
		}else{
			$this->count = $_SESSION['cart']->content_count;
		}

		if(isset($data['count']) && is_data($data['total'])){
			$this->total = $data['total'];
		}else{
			$this->total = $_SESSION['cart']->total;
		}

		if(isset($data['customers_status_id']) && !empty($data['customers_status_id'])){
			$this->group_permission = $data['customers_status_id'];
		}else{
			$this->group_permission = $customers_status->customers_status_id;
		}

		$this->payment_data = $this->_buildData();

	}

	function _getPossiblePayment($data = array()){
		global $xtPlugin, $price, $store_handler, $db, $language,$customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getPayment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$defaults_array = array(
			'pos'=>'payment',
			'group_check'=>'true',
			'group'=>$this->group_permission,
			'store_check'=>'true',
			'store'=> $store_handler->shop_id,
			'lang'=>$language->code,
			'shipping_check'=>'true',
			'status_check'=>'true'
		);

		$d = _merge_arrays($data, $defaults_array);

		if (isset($_SESSION['selected_shipping'])) $d = array_merge($d,array('shipping'=>$_SESSION['selected_shipping'],'shipping_check'=>'true'));

		$sql_tablecols = 'p.*, pd.*';

		$this->sql_payment = new payment_query();
		$this->sql_payment->setPosition($d['pos']);
		$this->sql_payment->setFilter('Language');
		$this->sql_payment->setSQL_COLS($sql_tablecols);

		if($d['group_check']=='true')
		$this->sql_payment->setFilter('GroupCheck');

		if($d['store_check']=='true')
		$this->sql_payment->setFilter('StoreCheck');

		if($d['shipping_check']=='true')
		$this->sql_payment->setFilter('ShippingCheck');

		if($d['status_check']=='true')
		$this->sql_payment->setFilter('StatusCheck');


		if(!empty($d['start']) && !empty($d['limit']))
		$this->sql_payment->setSQL_LIMIT((int)$d['start'].", ".(int)$d['limit']);
		
		$this->sql_payment->setSQL_SORT("p.sort_order ASC");		

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getPayment_query')) ? eval($plugin_code) : false;

		$sql = $this->sql_payment->getSQL_query();

		$record = $db->Execute($sql);

		if($record->RecordCount() > 0){
			while(!$record->EOF){

				$record->fields['costs'] = $this->_getCosts($record->fields['payment_id']);
				$data[] = $record->fields;

				($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getPayment_data')) ? eval($plugin_code) : false;
				$record->MoveNext();
			}$record->Close();

			return $data;
		}else {
			return false;
		}
	}

	
	function _getCosts($payment_id){
		global $xtPlugin, $price, $db, $language;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getCosts_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute(
			"select * from " . TABLE_PAYMENT_COST . " where payment_id = ?",
			array($payment_id)
		);
		if($record->RecordCount() > 0){
			while(!$record->EOF){
				$data[] = $record->fields;
				($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getCosts_data')) ? eval($plugin_code) : false;
				$record->MoveNext();
			}$record->Close();

			return $data;
		}else {
			return false;
		}
	}


	function _buildData(){
		global $xtPlugin, $price, $tax, $customers_status,$db;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_buildData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data_array = $this->_getPossiblePayment();

		$data = array();
		if(is_array($data_array)){
            foreach($data_array as $key => $value) {
				$value = $this->_filterCustomer($value);
				$value = $this->_filterGeoZone($value);
				$value = $this->_filterCountry($value);
				$value = $this->_filterPrice($value);

				($plugin_code = $xtPlugin->PluginCode('class.payment.php:_buildData_filter')) ? eval($plugin_code) : false;

				$payment_price = $this->_calcPrice($value);
				
				if(!empty($value['payment_code']) && count($value['costs'])>=1){

					$data[$value['payment_code']] = array(
						'payment_id' => $value['payment_id'],
						'payment_name' => $value['payment_name'],
						'payment_desc' => $value['payment_desc'],
						'payment_dir'  => $value['payment_dir'],
						'payment_code' => $value['payment_code'],
						'payment_icon' => $value['payment_icon'],
						'payment_tax_class' => $value['payment_tax_class'],
						'payment_price' => $payment_price,
						'payment_cost_discount'=>$payment_price['payment_cost_discount'],
						'payment_type' => 'payment',
						'payment_tpl' => $value['payment_tpl'],
						'payment_selected' => $_SESSION['selected_payment'],
						'payment_cost_info'=>$value['payment_cost_info'],
						'sort_order'=>$value['sort_order']
					);

					$class_path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$value['payment_dir'].'/classes/';
					$class_file = 'class.'.$value['payment_code'].'.php';

					if (file_exists($class_path . $class_file)) {
						require_once($class_path.$class_file);
						$plugin_payment_data = new $value['payment_code']();
						$data[$value['payment_code']] = array_merge($data[$value['payment_code']], $plugin_payment_data->data);
					}
					($plugin_code = $xtPlugin->PluginCode('class.payment.php:_buildData_data')) ? eval($plugin_code) : false;
				}
			}
		}

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_buildData_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

    /**
    * filter array for customer permissions
	*
	* @param array $data
	* @return array
	*/
	
    public function _filterCustomer($data) {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterCustomer_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;
        
        if(!is_data($data)) return false;

        $check_array = explode(',',$_SESSION['customer']->customer_info['payment_unallowed']);        
        if(in_array($data['payment_code'], $check_array)){
        	unset($data);    	
        }

        ($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterCustomer_bottom')) ? eval($plugin_code) : false;
        return $data; 
        
    }		
	
	/**
	 * Filter array based on customers zone
	 *
	 * @param array $data
	 * @return array
	 */
        function _filterGeoZone($data)
        {
            global $xtPlugin;

            ($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterGeoZone_top')) ? eval($plugin_code) : false;
            if (isset($plugin_return_value)) {
                return $plugin_return_value;
            }

            if (!is_data($data) || !is_data($data['costs'])) {
                return false;
            }

            $countriesCosts = $zonesCosts = array();
            foreach($data['costs'] as $key => $value) {
                if ($value['payment_geo_zone'] != '0') {
                    if ($value['payment_geo_zone'] == $this->billing_address['customers_zone']) {
                        if ($value['payment_allowed'] == '1') {
                            $zonesCosts[] = $value;
                        }
                    }
                } else {
                    $countriesCosts[] = $value;
                }
            }

            $data['costs'] = array_merge($countriesCosts, $zonesCosts);
            if (!count($data['costs'])) {
                unset($data);
            }

            ($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterGeoZone_bottom')) ? eval($plugin_code) : false;
            return $data;
        }

	/**
	 * Filter array based on customers country code
	 *
	 * @param array $data
	 * @return array
	 */
	function _filterCountry($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterCountry_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

		$check_content = $data['costs'];
        $new_cost = array();
        foreach($check_content as $key => $value) {

			$value['payment_country_code'] = strtoupper($value['payment_country_code']);
			$this->billing_address['customers_country_code'] = strtoupper($this->billing_address['customers_country_code']);

			if($value['payment_country_code']!='NULL' && $value['payment_country_code']!='' && $value['payment_country_code']!='0'){
				if($value['payment_country_code'] === $this->billing_address['customers_country_code']){
					if($value['payment_allowed']=='1'){
						$new_cost[] = $value;
					}
				}
			}else{
				$new_cost[] = $value;
			}
		}

		$data['costs'] = $new_cost;

		$data_count = is_countable($data['costs']) ? count($data['costs']) : 0;

		if($data_count==0 || !$data_count){
			unset($data);
		}

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterGeoZone_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

	/**
	 * Filter array based on price range
	 *
	 * @param array $data
	 * @return array
	 */
	function _filterPrice($data){
		global $xtPlugin, $price;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterPrice_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

		$check_content = $data['costs'];
        $content_price = $_SESSION['cart']->total['plain'] /* + $_SESSION['cart']->total_discount VIW-494-59660 */;
        foreach($check_content as $key => $value)
		{
			$value['payment_type_value_from'] = $price->_calcCurrency($value['payment_type_value_from']);
			$value['payment_type_value_to'] = $price->_calcCurrency($value['payment_type_value_to']);
            
			if(($content_price >= $value['payment_type_value_from'] && $content_price <= $value['payment_type_value_to']) || ($value['payment_type_value_to']==0 && $value['payment_type_value_from']==0)){
				if($value['payment_allowed']=='1'){
					$new_cost[] = $value;
				}else{
					unset($new_cost);
					unset($data);
					break;	
				}
			}
		}

		$data['costs'] = $new_cost;

		$data_count = is_array($data['costs']) ? count($data['costs']) : 0;

		if($data_count==0 || !$data_count){
			unset($data);
		}

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_filterPrice_bottom')) ? eval($plugin_code) : false;

		return $data;
	}

	function _calcPrice($data){
		global $xtPlugin, $tax, $price, $customers_status,$currency,$tax;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_calcPrice_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(is_array($data['costs']))
        {// country wiegt mehr als zone / sort by country desc
            usort($data['costs'], function ($a, $b) {
                if ($a['payment_country_code'] == $b['payment_country_code'])
                {
                    return 0;
                }
                if (empty($a['payment_country_code']) && !empty($b['payment_country_code']))
                {
                    return 1;
                }
                return -1;
            });
        }
        else {
		    $payment_price = $price->_getPrice(array('price'=>0, 'tax_class'=>0, 'curr'=>true, 'format'=>true, 'format_type'=>'default'));
            $payment_price['discount'] = 0;
            $payment_price['fee_percent'] = 0;
            $payment_price['percent'] = 0;
            return $payment_price;
        }

        if (isset($_SESSION['selected_payment_discount'][$data['payment_code']])) unset($_SESSION['selected_payment_discount'][$data['payment_code']]);
		if($data['costs']['0']['payment_price']!=0){


			$payment_price = $data['costs']['0']['payment_price'];

            if ($data['costs']['0']['payment_cost_discount']==1) { // rabatt %

                //rabatt %, vat will be ignored.
                if($data['payment_tax_class']!=0 && _SYSTEM_USE_PRICE=='true'){
                   $payment_price = $price->_AddTax($payment_price,$tax->data[$data['payment_tax_class']]);
                }

                $_SESSION['selected_payment_discount'][$data['payment_code']] = $payment_price;
                $percent = $payment_price;
                $payment_price = array('formated'=>'-'.round($payment_price, $currency->decimals).' %','plain'=>$payment_price,'plain_otax'=>$payment_price);
                $payment_price['discount'] = 1;
                $payment_price['fee'] = 0;
                $payment_price['percent'] = $percent;
            } elseif ($data['costs']['0']['payment_cost_discount']==2) { // aufschlag total
               
                $payment_price = $price->_getPrice(array('price'=>$payment_price, 'tax_class'=>$data['payment_tax_class'], 'curr'=>true, 'format'=>true, 'format_type'=>'default'));
                $payment_price['discount'] = 0;
                $payment_price['fee_percent'] = 0;
                $payment_price['percent'] = 0;
                
            } elseif ($data['costs']['0']['payment_cost_discount']==3) { // aufschlag %
            
				$percent = $payment_price;
				//rabatt %, vat will be ignored.
				if($data['payment_tax_class']!=0 && _SYSTEM_USE_PRICE=='true'){
					$payment_price = $price->_AddTax($payment_price,$tax->data[$data['payment_tax_class']]);
				}
            
                $cart_total = $_SESSION['cart']->content_total['plain_otax'];
                $payment_price = round(($payment_price*$cart_total)/100, $currency->decimals);
                
                $payment_price = $price->_getPrice(array('price'=>$payment_price, 'tax_class'=>$data['payment_tax_class'], 'curr'=>true, 'format'=>true, 'format_type'=>'default'));
                
                if(isset($percent) && $percent > 0){
                    $percent = $price->_AddTax($percent, $tax->_getTaxRates($data['payment_tax_class']));
                    $payment_price['formated'] = $payment_price['formated'].' ('.round($percent, $currency->decimals).'%)';
                }
                $payment_price['discount'] = 0;
                $payment_price['fee_percent'] = 1;
                $payment_price['percent'] = $percent;
                
            }elseif ($data['costs']['0']['payment_cost_discount']==0) {
                $payment_price = $price->_getPrice(array('price'=>$payment_price, 'tax_class'=>$data['payment_tax_class'], 'curr'=>true, 'format'=>true, 'format_type'=>'default'));
                $payment_price['discount'] = 0;
                $payment_price['fee_percent'] = 0;
                $payment_price['percent'] = 0;
            }

			return $payment_price;

		}

	}

	function _GroupCheck(){
		if (_SYSTEM_GROUP_CHECK == 'true') {
			return "and (group_permission_" . $this->group_permission . "=1 or group_permission_all=1) ";
		}
	}

	function getConfigHeaderData() {
		global $db,$store_handler, $xtPlugin;

		$stores = $store_handler->getStores();
		$header = array();
		$grouping = array();
		// query config_payment
		foreach ($stores as $sdata) {

			$store_names[] = $sdata['text'];
			$store_ids[] = 'SHOP_'.$sdata['id'];
			$query = "SELECT * FROM " . TABLE_CONFIGURATION_PAYMENT . " where payment_id = ? and shop_id=? ORDER BY sort_order ASC";

			$record = $db->Execute($query, array($this->payment_id, $sdata['id']));

            ($plugin_code = $xtPlugin->PluginCode('class.payment.php:getConfigHeaderData_foreach_store')) ? eval($plugin_code) : false;

			while (!$record->EOF) {

                ($plugin_code = $xtPlugin->PluginCode('class.payment.php:getConfigHeaderData_foreach_store_while_top')) ? eval($plugin_code) : false;

				$required = true;
				if ($record->fields['config_value'] == 'true' || $record->fields['config_value'] == 'false') {
					$type = 'truefalse';
				}

				if($record->fields['type'])
					$type = $record->fields['type'];

				if ($record->fields['type'] == 'dropdown') {
					if (strstr($record->fields['url'],'status:')) {
						$record->fields['url'] = str_replace('status:','',$record->fields['url']);
						$url = 'DropdownData.php?systemstatus='.$record->fields['url'];
					} else {
						$url = 'DropdownData.php?get='.$record->fields['url'];
					}

				} else {
					$url = $record->fields['url'];
				}

				if ($record->fields['config_value'] == '') {
					$required = false;
				}

				$groupingPosition = 'SHOP_'.$sdata['id'];
				$grouping['conf_'.$record->fields['config_key'].'_shop_'.$sdata['id']] = array('position' => $groupingPosition);
				// set header data
				$header['conf_'.$record->fields['config_key'].'_shop_'.$sdata['id']] = $tmp_data = array(
					'name' => 'conf_'.$record->fields['config_key'].'_shop_'.$sdata['id'],
					'text' => __define($record->fields['config_key'].'_TITLE'),
					'masterkey' => false,
					'lang' => false,
					'value' => $record->fields['config_value'],
					'hidden' => false,
					'min' => null,
					'max' => null,
					'readonly' => false,
					'required' => $required,
					'type' => $type,
					'url' => $url,
					'renderer' => null,
					'sort_order' => $record->fields['sort_order'],
				);

				$record->MoveNext();
			}
		}

		$panelSettings[] = array(
			'position' => 'store_settings',
			'text' => __text('TEXT_EXPORT_SETTINGS'),
			'groupingPosition' => $store_ids
		);
		return array('header'=>$header,'panelSettings'=>$panelSettings,'grouping'=>$grouping);
	}


	function _getParams() {
		global $xtPlugin, $store_handler,$language,$store_handler;

		define('TEXT_PAYMENT_NAME_STORE'.$store_handler->shop_id, __text('TEXT_PAYMENT_NAME'));

		$this->_setPaymentId();

		$params = array();
		$header['payment_tax_class'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=tax_classes'
		);
		$header['payment_status'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=status_truefalse'
		);
		$header['plugin_required'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=status_truefalse'
		);
		$header['plugin_installed'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=status_truefalse'
		);

		$rowActions[] = array('iconCls' => 'payment_price', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_PAYMENT_PRICE'));
		if ($this->url_data['edit_id'])
		{
			$js = "var edit_id = ".$this->url_data['edit_id'].";";
			$js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
		}
		else
		{
			$js = "var edit_id = record.id;";
			$js .= "var edit_name = record.data.payment_code;";
		}

		$js .= "addTab('adminHandler.php?load_section=payment_price&pg=overview&payment_id='+edit_id,'".__text('TEXT_PAYMENT_PRICE')." '+edit_name,'payment_price_'+edit_id)";

		$rowActionsFunctions['payment_price'] = $js;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getParams_top')) ? eval($plugin_code) : false;

		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;

		$header['payment_id'] = array('type' => 'hidden');
		$header['payment_code'] = array('type' => 'hidden');
		$header['payment_cost_info'] = array('type' => 'status');
		$params['exclude'] = array ('plugin_required', 'plugin_installed');
		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('status','payment_id', 'payment_name_store'.$store_handler->shop_id.'_'.$language->code, 'payment_code');
		}else{
			$edit_data = $this->getConfigHeaderData();
			if (count($edit_data['header'])>0) {
				$header = array_merge($header,$edit_data['header']);
				$params['grouping'] = $edit_data['grouping'];
				$params['panelSettings']  = $edit_data['panelSettings'];
			}
		}

		$params['display_newBtn'] = false;

        $stores = $store_handler->getStores();

        foreach ($stores as $store) {
            foreach ($language->_getLanguageList() as $key => $val) {
                $add_to_f = 'store'.$store['id'].'_';

                $header['payment_desc_'.$add_to_f.$val['code']] = array('type' => 'htmleditor');
                //$header['products_short_description_'.$add_to_f.$val['code']] = array('type' => 'htmleditor');

                $header['payment_description_store_id_'.$add_to_f.$val['code']] = array('type'=>'hidden');

                if (count($stores)>1){
                    if (_SYSTEM_SHOW_OVERLOAD_MESSAGE=="true") $msg = __text('TEXT_OVERLOAD_PRODUCT_DATA');
                    else $msg ='';
                    $listners = array("select"=>"OverLoadata('reload_st_".$add_to_f.$val['code']."','".$val['code']."','".$store['id']."',
                                   '".$this->url_data['edit_id']."','".$msg."');");

                    $header['reload_st_'.$add_to_f.$val['code']] = array('type' => 'dropdown','url'  => 'DropdownData.php?get=stores&current_store_id='.$store['id'],'listner'=>$listners);
                }else{
                    $header['reload_st_'.$add_to_f.$val['code']] =array('type'=>'hidden');
                }
            }
        }

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_getParams_bottom')) ? eval($plugin_code) : false;

		$params['header']         = $header;
		$params['master_key']     = $this->master_id;
		$params['default_sort']   = $this->master_id;
		
		$params['display_checkItemsCheckbox']  = true;
		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;

        $params['ddisplay_adminActionStatus'] = true;

        $params['languageStoreTab'] = true;

		return $params;
	}

	function _getSearchIDs($search_data) {
		$sql_tablecols = array('payment_code');
		$lang_sql_tablecols = array('payment_name');
		$sql_where = '';
		$lang_sql_where = '';

		foreach ($sql_tablecols as $tablecol) {
			$sql_where .= "(".$tablecol." LIKE '%".$search_data."%')";
		}

		foreach ($lang_sql_tablecols as $lang_tablecol) {
			$lang_sql_where .= "(".$lang_tablecol." LIKE '%".$search_data."%')";
		}

		$search_array = array('sql_where'=>$sql_where, 'lang_sql_where'=>$lang_sql_where);

		return $search_array;
	}

	function _setPaymentId() {
		if($this->url_data['edit_id'])
		$this->payment_id = (int)$this->url_data['edit_id'];
	}

	function _get($pID = 0) {
		global $xtPlugin, $db, $language,$store_handler;

		$stores = $store_handler->getStores();

		$pID = (int)$pID;

		if ($this->position != 'admin') return false;

		if ($pID === 'new') {
			$obj = $this->_set(array(), 'new');
			$pID =  $obj->new_id;
		} else {
			$obj = new stdClass;
			// query for config values
			foreach ($stores as $sdata) {
				$query = "SELECT * FROM " . TABLE_CONFIGURATION_PAYMENT . " where payment_id = ? and shop_id=? ORDER BY sort_order ASC";
				$rs = $db->Execute($query, array($pID, $sdata['id']));

				while (!$rs->EOF) {
					$conf_data['conf_'.$rs->fields['config_key'].'_shop_'.$sdata['id']]=$rs->fields['config_value'];
					$rs->MoveNext();
				}$rs->Close();
			}
		}

		if (!$pID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', $this->sql_limit, $this->perm_array, '','','payment_description_store_id');

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($pID){
			$data = $table_data->getData($pID);
            $data[0]['group_permission_info']=_getPermissionInfo();
            $data[0]['shop_permission_info']=_getPermissionInfo();
            $data[0]['shipping_permission_info']=_getPermissionInfo();
		}else{
			$data = $table_data->getHeader();
		}

		if (is_array($conf_data)) $data[0] = array_merge($data[0],$conf_data);

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_get_bottom')) ? eval($plugin_code) : false;

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit'){
		global $db,$language,$filter,$xtPlugin;

		$obj = new stdClass;

		foreach ($data as $key => $val) {

			if($val == 'on') {
				$val = 1;
			}
			$data[$key] = $val;
		}

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_set_top')) ? eval($plugin_code) : false;

		$oP = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$objP = $oP->saveDataSet();

		if ($set_type=='new') {	// edit existing
			$obj->new_id = $objP->new_id;
			$data = array_merge($data, array($this->_master_key=>$objP->new_id));
		}

		$oPD = new adminDB_DataSave($this->_table_lang, $data, true, __CLASS__, true);
		$objPD = $oPD->saveDataSet();

		$set_perm = new item_permission($this->perm_array);
		$set_perm->_saveData($data, $data[$this->_master_key]);

		$this->setPaymentConfig($data);

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_set_bottom')) ? eval($plugin_code) : false;


		if($data['status'] == 1 && $set_type!='new')
		{
			$plg_id = $db->GetOne("SELECT plg.plugin_id FROM ".TABLE_PLUGIN_PRODUCTS." plg
				LEFT JOIN ".TABLE_PAYMENT." pay on plg.plugin_id = pay.plugin_installed
				WHERE pay.payment_id = ? ", array($data['payment_id']));
			$plg_installed = new plugin_installed();
			$plg_installed->_setStatus($plg_id, 1);
		}

		if ($objP->success && $objPD->success) {

			$obj->success = true;
		} else {
			$obj->failed = true;
		}

		return $obj;
	}

	function setPaymentConfig($data) {
		global $db,$store_handler,$filter;

		if ($this->position != 'admin') return false;

		$payment_id=(int)$data['payment_id'];
		if (!is_int($payment_id)) return false;

		$stores = $store_handler->getStores();

		foreach ($stores as $sdata) {
			$store_names[] = $sdata['text'];
			$query = "SELECT * FROM " . TABLE_CONFIGURATION_PAYMENT . " where payment_id = ? and shop_id=?";
			$record = $db->Execute($query, array($payment_id, $sdata['id']));

			while (!$record->EOF) {
				$conf_value = $filter->_filter($data['conf_'.$record->fields['config_key'].'_shop_'.$sdata['id']]);
                if($record->fields['config_key'] == 'status' && empty($conf_value))
                    $conf_value = 0;
				$db->Execute(
					"UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value = ? WHERE payment_id=? and shop_id=? and config_key=?",
					array($conf_value, $payment_id, $sdata['id'], $record->fields['config_key'])
				);
				$record->MoveNext();
			}

		}
		return true;
	}

	function checkInstall($paymentCode) {
		global $db;
		
		$rs = $db->Execute(
			"SELECT payment_id FROM ". TABLE_PAYMENT ." WHERE payment_code = ? LIMIT 1",
			array($paymentCode)
		);
		if ($rs->RecordCount()>0)
			return $rs->fields['payment_id'];
		return false;			
	}
	
	function install($data,$plugin_id) {
		global $db,$language,$filter, $store_handler;

        $existing_data = $db->GetArray('SELECT * FROM '. TABLE_PAYMENT. " WHERE payment_code = ?", [$data['payment_code']]);
        if(empty(!$existing_data))
        {
            $data['payment_dir']  = $existing_data[0]['payment_dir'];
            $data['payment_icon'] = $existing_data[0]['payment_icon'];
            $data['payment_tpl']  = $existing_data[0]['payment_tpl'];
            $data['payment_tax_class']  = $existing_data[0]['payment_tax_class'];
            $data['sort_order']  = $existing_data[0]['sort_order'];
            $data['payment_cost_info']  = $existing_data[0]['payment_cost_info'];
        }

		$input_data = array();
		$input_data['payment_code'] = array_key_exists('payment_code', $data) ? $filter->_filter($data['payment_code']) : 'ERROR_MISSING_PAYMENT_CODE_IN_INSTALLER';
		$input_data['payment_dir']  = array_key_exists('payment_dir',  $data) ? $filter->_filter($data['payment_dir']) : '';
		$input_data['payment_icon'] = array_key_exists('payment_icon', $data) ? $filter->_filter($data['payment_icon']) : '';

		$input_data['status'] = array_key_exists('payment_status', $data) ? $data['payment_status'] : '0';
        if ($input_data['status']!='1') $input_data['status']='0';
		
		$input_data['payment_tpl']=$filter->_filter($data['payment_tpl']);
		$input_data['plugin_required']='1';
		$input_data['plugin_installed']=$plugin_id;

        if (!array_key_exists('payment_sort', $data) || !$data['payment_sort']) {
            $data['payment_sort'] = $db->GetOne("SELECT sort_order FROM ".$this->_table." WHERE payment_code=?", [$input_data['payment_code']]);
        }
		if (!$data['payment_sort']) $data['payment_sort'] = 0;
		$input_data['sort_order'] = $filter->_filter($data['payment_sort']);

		$check = $this->checkInstall($input_data['payment_code']);
		if (!$check) {
			$db->AutoExecute(TABLE_PAYMENT, $input_data);
			$payment_id = $db->Insert_ID();
		} else {
			$payment_id = $check;
			$db->AutoExecute(TABLE_PAYMENT, $input_data,'UPDATE',"payment_id=".$db->Quote($payment_id));
		}

		foreach ($store_handler->getStores() as $store)
        {
            $store_id = $store['id'];
            foreach ($language->_getLanguageList() as $key => $val)
            {
                // check payment language
                $rs = $db->Execute(
                    "SELECT payment_id FROM " . TABLE_PAYMENT_DESCRIPTION . " WHERE payment_id = ? AND language_code = ? and payment_description_store_id = ? LIMIT 1",
                    array($payment_id, $val['code'], $store_id)
                );
                if ($rs->RecordCount() == 0)
                {
                    $input_data = array('payment_description_store_id' => $store_id);
                    if (is_array($data[$val['code']]))
                    {
                        $input_data['payment_name'] = $data[$val['code']]['title'];
                        $input_data['payment_desc'] = $data[$val['code']]['description'];
                        $input_data['payment_id'] = $payment_id;
                        $input_data['language_code'] = $val['code'];
                    }
                    else
                    {
                        $input_data['payment_name'] = $data['en']['title'];
                        $input_data['payment_desc'] = $data['en']['description'];
                        $input_data['payment_id'] = $payment_id;
                        $input_data['language_code'] = $val['code'];
                    }
                    $db->AutoExecute(TABLE_PAYMENT_DESCRIPTION, $input_data);
                }
            }
        }

		return $payment_id;

	}

	function _unset($id = 0) {
		global $db, $xtPlugin;

		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

		($plugin_code = $xtPlugin->PluginCode('class.payment.php:_unset')) ? eval($plugin_code) : false;


		$rs = $db->Execute("SELECT plugin_required, plugin_installed FROM ". TABLE_PAYMENT ." WHERE ".$this->_master_key." = ?", array($id));
		if ($rs->fields['plugin_required']=='1') {
			// uninstall plugin
			$plugin = new plugin();
			$plugin->DeletePlugin($rs->fields['plugin_installed']);
		} else {
			$this->cleanupData($id);
		}

		$obj = new stdClass;
		$obj->success = true;
		return $obj;
	}

	function cleanupData($id = 0)
    {
        global $db;

        if ($this->position != 'admin') return false;
        $id=(int)$id;
        if (!is_int($id) || empty($id)) return false;

        $db->Execute("DELETE FROM ". TABLE_PAYMENT ." WHERE ".$this->_master_key." = ?", array($id));
        $db->Execute("DELETE FROM ". TABLE_PAYMENT_DESCRIPTION ." WHERE ".$this->_master_key." = ?", array($id));
        $db->Execute("DELETE FROM ". TABLE_PAYMENT_COST ." WHERE ".$this->_master_key." = ?", array($id));
        $db->Execute("DELETE FROM ". TABLE_CONFIGURATION_PAYMENT ." WHERE ".$this->_master_key." = ?", array($id));

        $set_perm = new item_permission($this->perm_array);
        $set_perm->_deleteData($id);
    }

	function _setStatus($id, $status) {
		global $db,$xtPlugin;

		$id = (int)$id;
		if (!is_int($id)) return false;

		if($status)
		{
			$plg_id = $db->GetOne("SELECT plg.plugin_id FROM ".TABLE_PLUGIN_PRODUCTS." plg
				LEFT JOIN ".TABLE_PAYMENT." pay on plg.plugin_id = pay.plugin_installed
				WHERE pay.payment_id = ? ", array($id));
			$plg_installed = new plugin_installed();
			$plg_installed->_setStatus($plg_id, 1);
		}

		$db->Execute(
			"update " . TABLE_PAYMENT . " set status = ? where ".$this->_master_key." = ?",
			array($status, $id)
		);

        ($plugin_code = $xtPlugin->PluginCode('class.payment.php:_setStatus_bottom')) ? eval($plugin_code) : false;
	}
}