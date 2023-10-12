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

class shipping extends xt_backend_cls {

	public $lang;
	public $shipping_address = array();
	public $products =  array();
	public $weight = 0;
	public $count = 0;
	public $total = array();
	public $group_permission;
	public $shipping_data =  array();
	public $master_id = 'shipping_id';
    public $shipping_to = ['zones' => [], 'countries' => []];

	protected $_table = TABLE_SHIPPING;
	protected $_table_lang = TABLE_SHIPPING_DESCRIPTION;
	protected $_table_seo = NULL;
	protected $_master_key = 'shipping_id';
    public $shipping_errors = [];

    function __construct() {
		global $xtPlugin;

		$this->getPermission();

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:shipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	}

	function getPermission(){
		global $store_handler, $customers_status, $xtPlugin;

		$this->perm_array = array(
			'shop_perm' => array(
				'type'=>'shop',
				'key'=>$this->_master_key,
				'value_type'=>'shipping',
				'pref'=>'s'
			),
			'group_perm' => array('type'=>'group_permission',
				'key'=>$this->_master_key,
				'value_type'=>'shipping',
				'pref'=>'s'
			)
		);

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;

		$this->permission = new item_permission($this->perm_array);

		return $this->perm_array;
	}


	function _shipping($data=array() ){
		global $xtPlugin, $price, $db, $language, $customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_shipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!empty($data['language'])){
			$this->lang = $data['language'];
		}else{
			$this->lang = $language->code;
		}

		if(is_data($data['customer_shipping_address'])){
			$this->shipping_address = $data['customer_shipping_address'];
		}else{
			$this->shipping_address = $_SESSION['customer']->customer_shipping_address;
		}

		if(is_data($data['products'])){
			$this->products = $data['products'];
		}else{
			$this->products = $_SESSION['cart']->show_content;
		}

		if(is_data($data['weight'])){
			$this->weight = $data['weight'];
		}else{
			$this->weight = $_SESSION['cart']->content_weight_physical;
		}

		if(is_data($data['count'])){
			$this->count = $data['count'];
		}else{
			$this->count = $_SESSION['cart']->content_count;
		}

		if(is_data($data['total'])){
			$this->total = $data['total'];
		}else{
			$this->total = $_SESSION['cart']->content_total_physical;
		}

		if(!empty($data['customers_status_id'])){
			$this->group_permission = $data['customers_status_id'];
		}else{
			$this->group_permission = $customers_status->customers_status_id;
		}

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_shipping_bottom')) ? eval($plugin_code) : false;

		$this->shipping_data = $this->_buildData();

	}

	function _getPossibleShipping($data=array()){
		global $xtPlugin, $store_handler, $price, $db, $language;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getShipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$defaults_array = array(
			'pos'=>'shipping',
			'group_check'=>'true',
			'group'=>$this->group_permission,
			'store_check'=>'true',
			'store'=> $store_handler->shop_id,
			'lang'=>$language->code,
			'status_check'=>'true'
		);
		$d = _merge_arrays($data, $defaults_array);

		$sql_tablecols = 's.*, sd.*';

		$this->sql_shipping = new shipping_query();
		$this->sql_shipping->setPosition($d['pos']);
		$this->sql_shipping->setFilter('Language');
		$this->sql_shipping->setSQL_COLS($sql_tablecols);

		if($d['group_check']=='true')
		$this->sql_shipping->setFilter('GroupCheck');

		if($d['store_check']=='true')
		$this->sql_shipping->setFilter('StoreCheck');

		if($d['status_check']=='true')
		$this->sql_shipping->setFilter('StatusCheck');		

		if(!empty($d['start']) && !empty($d['limit']))
		$this->sql_shipping->setSQL_LIMIT((int)$d['start'].", ".(int)$d['limit']);
		
		$this->sql_shipping->setSQL_SORT("s.sort_order ASC");		

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getShipping_query')) ? eval($plugin_code) : false;

		$sql = $this->sql_shipping->getSQL_query();

		$record = $db->Execute($sql);

		if($record->RecordCount() > 0){
			while(!$record->EOF){

				$record->fields['costs'] = $this->_getCosts($record->fields['shipping_id']);
				$data[] = $record->fields;

				($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getShipping_data')) ? eval($plugin_code) : false;
				$record->MoveNext();
			}$record->Close();

			return $data;
		}else {
			return false;
		}

	}

	function _getCosts($shipping_id){
		global $xtPlugin, $price, $db, $language;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getCosts_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute(
			"select * from " . TABLE_SHIPPING_COST . " where shipping_id = ? ORDER BY shipping_type_value_from ASC",
			array($shipping_id)
		);
		if($record->RecordCount() > 0){
			while(!$record->EOF){
				$data[] = $record->fields;
				($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getCosts_data')) ? eval($plugin_code) : false;
				$record->MoveNext();
			}$record->Close();
			return $data;
		}else {
			return false;
		}
	}


	function _buildData(){
        global $xtPlugin, $price, $tax, $customers_status, $system_status, $language, $db;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_buildData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data_array = $this->_getPossibleShipping();

        ($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_buildData_possibleShippings')) ? eval($plugin_code) : false;

        $this->shipping_to = ['zones' => [], 'countries' => []];
		$data = array();
		if(is_array($data_array)){
            foreach($data_array as $key => $value) {

                foreach($value['costs'] as $costs)
                {
                    if (empty($costs['shipping_allowed'])) continue;

                    if(!empty($costs["shipping_country_code"]))
                    {
                        $this->shipping_to['countries'][] = $costs["shipping_country_code"];
                    }
                    else if(!empty($costs["shipping_geo_zone"]))
                    {
                        $sgz = (int) $costs["shipping_geo_zone"];
                        if($sgz > 9999)
                        {
                            $sgz = $sgz - 99990;
                            $zsc = $db->GetOne("SELECT zone_countries FROM ".TABLE_SHIPPING_ZONES. " WHERE zone_id = ?;", array($sgz));
                            if($zsc)
                            {
                                $zsc = array_filter(explode(',', $zsc));
                                foreach ($zsc as $sc)
                                {
                                    $this->shipping_to['countries'][] = $sc;
                                }
                            }
                        }
                        else
                        {
                            $sys_stat = $system_status->getSingleValue($costs["shipping_geo_zone"], $language->content_language);
                            if (is_array($sys_stat) && !empty($sys_stat['status_name']))
                            {
                                $this->shipping_to['zones'][] = $sys_stat['status_name'];
                            }
                        }
                    }
                }

				$value = $this->_filterCustomer($value);

                if ($value['use_shipping_zone']==0) {
                    $value = $this->_filterZone($value);    
                }   else {
                    $value = $this->_filterShippingZone($value);
                }
				
				$value = $this->_filterCountry($value);

				if($value['shipping_type']=='weight'){
					$value = $this->_filterWeight($value);
				}

				if($value['shipping_type']=='price'){
					$value = $this->_filterPrice($value);
				}

				if($value['shipping_type']=='item'){
					$value = $this->_filterItem($value);				
				}

				($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_buildData_filter')) ? eval($plugin_code) : false;

				if(!empty($value['shipping_code'])){

                    $shipping_price = $this->_calcPrice($value);

					$data[$value['shipping_code']] = array(
						'shipping_id' => $value['shipping_id'],
						'shipping_name' => $value['shipping_name'],
						'shipping_desc' => $value['shipping_desc'],
						'shipping_dir'  => $value['shipping_dir'],
						'shipping_code' => $value['shipping_code'],
						'shipping_icon' => $value['shipping_icon'],
						'shipping_tax_class' => $value['shipping_tax_class'],
						'shipping_price' => $shipping_price,
						'shipping_type' => 'shipping',
						'shipping_tpl' => $value['shipping_tpl'],
						'shipping_selected' => $_SESSION['selected_shipping']
					);

					$class_path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$value['shipping_dir'].'/classes/';
					$class_file = 'class.'.$value['shipping_code'].'.php';

					if (file_exists($class_path . $class_file)) {
						require_once($class_path.$class_file);
						$plugin_shipping_data = new $value['shipping_code']();

						$data[$value['shipping_code']] = array_merge($data[$value['shipping_code']], $plugin_shipping_data->data);
					}
				}
			}
		}
		$this->shipping_to['zones'] = array_unique($this->shipping_to['zones']);
        $this->shipping_to['countries'] = array_unique($this->shipping_to['countries']);

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_buildData_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

    /**
    * filter shipping array for customer permissions
    * 
    * @param mixed $data
    */	
	
    public function _filterCustomer($data) {
        global $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterCustomer_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;
        
        if(!is_data($data)) return false;

        $check_array = explode(',',$_SESSION['customer']->customer_info['shipping_unallowed']);        
        if(in_array($data['shipping_code'], $check_array)){
        	unset($data);    	
        }

        ($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterCustomer_bottom')) ? eval($plugin_code) : false;
        return $data;
    }
	
    /**
     * filter shipping array for shipping zones
     * 
     * @param mixed $data
     */
    public function _filterShippingZone($data)
    {
        global $xtPlugin, $db;

        ($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterShippingZone_top')) ? eval($plugin_code) : false;
        if (isset($plugin_return_value)) {
            return $plugin_return_value;
        }

        if (!is_data($data) || !is_data($data['costs'])) {
            return false;
        }

        $zones = $this->_getShippingZones();
        if (!$zones) {
            return false;
        }
        
        $new_cost = array();
        $check_content = $data['costs'];
        $this->shipping_address['customers_country_code'] = strtoupper($this->shipping_address['customers_country_code']);

        $hasCostForCountry = false;
        //shipping zone
        foreach($check_content as $key => $value) {
            if ($value['shipping_allowed'] == '1')
            {
                if (is_array($zones[$value['shipping_geo_zone']]))
                {
                    $countries = $zones[$value['shipping_geo_zone']]['countries'];
                    $countries = explode(',', $countries);
                    if (
                        (in_array($this->shipping_address['customers_country_code'], $countries) && $this->shipping_address['customers_country_code'] == $value['shipping_country_code'])
                        ||
                        ($value['shipping_country_code'] == "" && in_array($this->shipping_address['customers_country_code'], $countries))
                    )
                    {
                        $hasCostForCountry = true;
                        $value['shipping_country_code'] = $this->shipping_address['customers_country_code'];
                        $new_cost[] = $value;

                    }
                }
                elseif ($this->shipping_address['customers_zone'] == $value['shipping_geo_zone'] || empty($value['shipping_geo_zone']))
                {
                    $value['shipping_country_code'] = strtoupper($value['shipping_country_code']);
                    if (empty($value['shipping_country_code']) || $value['shipping_country_code'] == $this->shipping_address['customers_country_code'])
                    {
                        $new_cost[] = $value;
                    }
                }
            }
        }
        
        $data['costs'] = $new_cost;

        $data_count = is_countable($data['costs']) ? count($data['costs']) : 0;
        if ($data_count == 0 || !$data_count) {
            unset($data);
        }
        $data['hasCostForCountry'] = $hasCostForCountry;
        ($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterShippingZone_bottom')) ? eval($plugin_code) : false;

        return $data;
    }
    
    /**
    * Filter shipping array for customers zone
    * 
    * @param mixed $data
    */
	public function _filterZone($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterGeoZone_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;
        
        $new_cost = array();
		$check_content = $data['costs'];

        $zones = $this->_getShippingZones();

        $customers_country_code = $this->shipping_address['customers_country_code'];
        $customers_zone = $this->shipping_address['customers_zone'];

        if (is_array($zones)) {
            foreach($zones as $key => $value) {
                $countries = $value['countries'];
                $countries = explode(',', $countries);
                if (in_array($customers_country_code, $countries)) {
                    $customers_zone = $value['zone_id'];
                }
            }
        }

        foreach($check_content as $key => $value) {

			if($value['shipping_geo_zone']!='0'){
				if($value['shipping_geo_zone'] == $this->shipping_address['customers_zone'] || $value['shipping_geo_zone'] == $customers_zone){

					if($value['shipping_allowed']=='1'){
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
		
		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterGeoZone_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

    /**
    * filter shipping array for customers shipping country
    * 
    * @param mixed $data
    */
	public function _filterCountry($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterCountry_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

        $new_cost=array();
		$check_content = array();
		$check_content = $data['costs'];

        foreach($check_content as $key => $value) {

			$value['shipping_country_code'] = strtoupper($value['shipping_country_code']);

			$this->shipping_address['customers_country_code'] = strtoupper($this->shipping_address['customers_country_code']);

			if($value['shipping_country_code']!='NULL' && $value['shipping_country_code']!='' && $value['shipping_country_code']!='0'){
				if($value['shipping_country_code'] === $this->shipping_address['customers_country_code']){

					if($value['shipping_allowed']=='1'){
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
		
		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterCountryZone_bottom')) ? eval($plugin_code) : false;
		return $data;
	}
	
	
	/**
	 * filter shipping array for customers shipping country on shipping form
	 *
	 * @param mixed $data
	 * @param string $country_iso2
	 */
    public function _filterShippingCountry($data, $sel_country, $zone_id)
    {
        if(!is_data($data)) return false;

        if(!is_data($data['costs'])) return false;

        $new_cost=array();

        $check_content = $data['costs'];
        $sel_country = strtoupper($sel_country);
        $hasCostForCountry = false;

        // try to find costs for country first
        foreach ($check_content as $key => $value)
        {
            $value['shipping_country_code'] = strtoupper($value['shipping_country_code']);

            if($value['shipping_country_code']!='NULL' && $value['shipping_country_code']!='' && $value['shipping_country_code']!='0'
                && $value['shipping_geo_zone'] == 0){
                if($value['shipping_country_code'] === $sel_country){

                    if($value['shipping_allowed']=='1'){
                        $new_cost[] = $value;
                        $hasCostForCountry = true;
                    }else{
                        unset($new_cost);
                        break;
                    }
                }
            }
        }
        // if there are no country costs try for shipping zone
        if(count($new_cost) == 0)
        {
            foreach ($check_content as $key => $value)
            {
                if($value['shipping_geo_zone'] == $zone_id)
                {
                    if($value['shipping_allowed']=='1'){
                        $new_cost[] = $value;
                    }else{
                        unset($new_cost);
                        break;
                    }
                }
            }
        }

        $data['costs'] = $new_cost;
        $data['hasCostForCountry'] = $hasCostForCountry;
        return $data;
    }

    /**
    * Filter shipping array for cart weight
    * 
    * @param mixed $data
    */
	public function _filterWeight($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterWeight_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

        $new_cost=array();
		$check_content = array();
		$check_content = $data['costs'];

        $errors = [];

        foreach($check_content as $key => $value) {
			if(($this->weight >= $value['shipping_type_value_from'] && $this->weight <= $value['shipping_type_value_to']) || ($value['shipping_type_value_to']==0 && $value['shipping_type_value_from']==0)){
				if($value['shipping_allowed']=='1'){
					$new_cost[] = $value;
				}else{
					unset($new_cost);
					unset($data);
					break;	
				}
			}
            else {
                $error = [
                    'shipping_code' => $data['shipping_code'],
                    'shipping_name' => $data['shipping_name'],
                    'error' => 'weight',
                    'cart' => $this->weight,
                    'type' => 'maximum',
                    'limit' => $value['shipping_type_value_to']
                ];
                if($this->weight <$value['shipping_type_value_from'])
                {
                    $error['type'] = 'minimum';
                    $error['limit'] = $value['shipping_type_value_from'];
                }
                $errors[$data['shipping_code']] = $error;
            }
		}

		$data['costs'] = $new_cost;

		$data_count = is_countable($data['costs']) ? count($data['costs']) : 0;
	
		if($data_count==0 || !$data_count){
			unset($data);
            $this->shipping_errors = array_merge($this->shipping_errors, $errors);
		}
		
		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterWeight_bottom')) ? eval($plugin_code) : false;

		return $data;
	}

    /**
    * filter shipping array for cart total amount
    * 
    * @param mixed $data
    */
	public function _filterPrice($data){
		global $xtPlugin,$price;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterPrice_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

        $new_cost=array();
		$check_content = array();
		$check_content = $data['costs'];

        $errors = [];

        foreach($check_content as $key => $value) {
			$value['shipping_type_value_from'] = $price->_calcCurrency($value['shipping_type_value_from']);
			$value['shipping_type_value_to'] = $price->_calcCurrency($value['shipping_type_value_to']);
			
			if((round($this->total['plain'],2) >= $value['shipping_type_value_from'] && round($this->total['plain'],2) <= $value['shipping_type_value_to']) || ($value['shipping_type_value_to']==0 && $value['shipping_type_value_from']==0)){
				if($value['shipping_allowed']=='1'){
					$new_cost[] = $value;
				}
			}

            else {
                $error = [
                    'shipping_code' => $data['shipping_code'],
                    'shipping_name' => $data['shipping_name'],
                    'error' => 'price',
                    'cart' => $this->total['plain'],
                    'type' => 'maximum',
                    'limit' => $value['shipping_type_value_to']
                ];
                if($this->total['plain'] < $value['shipping_type_value_from'])
                {
                    $error['type'] = 'minimum';
                    $error['limit'] = $value['shipping_type_value_from'];
                }
                $errors[$data['shipping_code']] = $error;
            }

		}

		$data['costs'] = $new_cost;
		
		$data_count = is_countable($data['costs']) ? count($data['costs']) : 0;
	
		if($data_count==0 || !$data_count){
			unset($data);
            $this->shipping_errors = array_merge($this->shipping_errors, $errors);
		}
		
		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterPrice_bottom')) ? eval($plugin_code) : false;

		return $data;
	}

    /**
    * filter shipping array if shipping per item has been selected
    * 
    * @param mixed $data
    */
	public function _filterItem($data){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterItem_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!is_data($data)) return false;

		if(!is_data($data['costs'])) return false;

		if(!is_data($this->count)) return false;

        $new_cost=array();
		$check_content = array();
		$check_content = $data['costs'];

        foreach($check_content as $key => $value) {

			if(($this->count >= $value['shipping_type_value_from'] && $this->count <= $value['shipping_type_value_to']) || ($value['shipping_type_value_to']==0 && $value['shipping_type_value_from']==0)){

				if($value['shipping_allowed']=='1'){
					$data['item_price'] = true;
					$new_cost[] = $value;
				}else{
					unset($new_cost);
					unset($data);
					break;
				}
			}

		}


		$data['costs'] = $new_cost;

		$data_count = is_countable($data['costs']) ? count($data['costs']) : 0;
	
		if($data_count==0 || !$data_count){
			unset($data);		
		}
		
		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_filterItem_bottom')) ? eval($plugin_code) : false;

		return $data;
	}


	/**
	 * Format price for shipping (add tax, currency etc), used in shipping content table
	 *
	 * @param array $data
	 */
	function _formatArray(&$data) {
		global $xtPlugin, $tax, $price, $customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_format_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;


		foreach ($data as $key => $methods) {

			if (is_array($methods['costs'])) {

				foreach ($methods['costs'] as $val => $arr) {

					$shipping_tax = $tax->data[$methods['shipping_tax_class']];
					$shipping_price_otax = $arr['shipping_price'];
					$shipping_price=$shipping_price_otax;
					$shipping_tax_value = $price->_calcTax($shipping_price, $shipping_tax);

					if ($methods['shipping_type'] == 'price') {
						$shipping_from_otax = $arr['shipping_type_value_from'];
						$shipping_to_otax = $arr['shipping_type_value_to'];

						$shipping_from_otax = $price->_Format(array('price'=>$shipping_from_otax, 'price_otax'=>$shipping_from_otax, 'format'=>true, 'format_type'=>'default'));
						$shipping_to_otax = $price->_Format(array('price'=>$shipping_to_otax, 'price_otax'=>$shipping_to_otax, 'format'=>true, 'format_type'=>'default'));
						$data[$key]['costs'][$val]['shipping_type_value_from'] = $shipping_from_otax['formated'];
						$data[$key]['costs'][$val]['shipping_type_value_to'] = $shipping_to_otax['formated'];

					}

					if ($methods['shipping_type'] == 'item') {
						$data[$key]['costs'][$val]['shipping_type_value_from'] = round($arr['shipping_type_value_from'],0);
						$data[$key]['costs'][$val]['shipping_type_value_to'] = round($arr['shipping_type_value_to'],0);
					}
					
					if ($methods['shipping_type'] == 'weight') {
						$weight_from=$arr['shipping_type_value_from'];
						$weight_from = number_format($weight_from, 2, ',', '.');
						
						$weight_to=$arr['shipping_type_value_to'];
						$weight_to = number_format($weight_to, 2, ',', '.');						
						
						$data[$key]['costs'][$val]['shipping_type_value_from'] = $weight_from;
						$data[$key]['costs'][$val]['shipping_type_value_to'] = $weight_to;
					}


					if ($customers_status->customers_status_show_price_tax == '1') {
						$shipping_price = $shipping_price_otax + $shipping_tax_value;
					}

					$shipping_price = $price->_Format(array('price'=>$shipping_price, 'price_otax'=>$shipping_price_otax, 'format'=>true, 'format_type'=>'default'));

					$data[$key]['costs'][$val]['shipping_price'] = $shipping_price['formated'];

				}
			}
		}
	}

	/**
	 * calculate shipping price (format and add tax)
	 *
	 * @param array $data
	 * @return array
	 */
	function _calcPrice($data){
		global $xtPlugin, $tax, $price, $customers_status;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_calcPrice_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		// country wiegt mehr als zone / sort by country desc
        usort($data['costs'], function($a, $b){
            if($a['shipping_country_code'] == $b['shipping_country_code']) return 0;
            if(empty($a['shipping_country_code']) && !empty($b['shipping_country_code'])) return 1;
            return -1;
        });

		$shipping_price = $data['costs']['0']['shipping_price'];

		if($data['shipping_type'] == 'item' && $data['item_price'] == true){
			$qty = $this->count;
		}

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_calcPrice')) ? eval($plugin_code) : false;

		$shipping_price = $price->_getPrice(array('price'=>$shipping_price, 'qty'=>$qty, 'tax_class'=>$data['shipping_tax_class'], 'format'=>true, 'curr'=>true, 'format_type'=>'default'));

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_calcPrice_bottom')) ? eval($plugin_code) : false;
		return $shipping_price;
	}
    
    /**
    * get shipping zones
    * 
    */
    public function _getShippingZones() {
        global $db;
        
        $rs = $db->Execute("SELECT * FROM ".TABLE_SHIPPING_ZONES);
        if ($rs->RecordCount()==0) return false;
        $zones = array();
        while (!$rs->EOF) {
            $zones['9999'.$rs->fields['zone_id']]=array('zone_id'=>'9999'.$rs->fields['zone_id'],'countries'=>$rs->fields['zone_countries']);
            $rs->MoveNext();
        }
        return $zones;
    }

	function _getParams() {
		global $xtPlugin, $language;

		$params = array();

		if ($this->url_data['new'] == true && !$this->url_data['edit_id']) {
			$obj = new stdClass;
			$obj = $this->_set(array(), 'new');
			$this->url_data['edit_id'] = $obj->new_id;
		}

		$header['shipping_id'] = array('type' => 'hidden');
        $header['use_shipping_zone'] = array('type' => 'status');
		$header['shipping_tax_class'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=tax_classes'
		);
		$header['shipping_status'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=status_truefalse'
		);

		$header['shipping_type'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=conf_shippingtype'
		);

        $header['shipping_code'] = ['regex' => '^[0-9a-zA-Z.\-\_]+$'];
		
        foreach ($language->_getLanguageList() as $key => $val) {
            $header['shipping_desc_'.$val['code']] = array(
                'type' => 'htmleditor'
            );
        }


		
		$params['display_checkItemsCheckbox']  = true;
		$params['display_checkCol'] = false;
		$params['display_adminActionStatus'] = false;

		if ($this->url_data['edit_id'])
		{
			$js = "var edit_id = ".$this->url_data['edit_id'].";";
			$js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
		}
		else
		{
			$js = "var edit_id = record.id;";
			$js .= "var edit_name = record.data.shipping_code;";
		}

		$rowActions[] = array('iconCls' => 'shipping_price', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_SHIPPING_PRICE'));
        if ($this->url_data['edit_id'])
		{
		  	$js = "var edit_id = ".$this->url_data['edit_id'].";";
			$js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
		}
		else
		{
          	$js = "var edit_id = record.id;";
			$js .= "var edit_name = record.data.shipping_code;";
		}

		$js .= "addTab('adminHandler.php?load_section=shipping_price&pg=overview&shipping_id='+edit_id,'".__text('TEXT_SHIPPING_PRICE')." '+edit_name,'shipping_price_'+edit_id)";

		$rowActionsFunctions['shipping_price'] = $js;

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_getParams_bottom')) ? eval($plugin_code) : false;

		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;

		$params['header']         = $header;
		$params['master_key']     = $this->master_id;
		$params['default_sort']   = $this->master_id;

		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('shipping_id', 'shipping_name_'.$language->code, 'shipping_code', 'status');
		}

		return $params;
	}


	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$ID = $this->url_data['edit_id'];
		}

		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}			
		
		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', $this->sql_limit, $this->perm_array);

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);

			$data[0]['group_permission_info']=_getPermissionInfo();

            $data[0]['shop_permission_info']=_getPermissionInfo();
		} else {
			$data = $table_data->getHeader();
		}

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_get_bottom')) ? eval($plugin_code) : false;

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit'){
		global $xtPlugin,$db,$language,$filter;

		$obj = new stdClass;

		foreach ($data as $key => $val) {

			if($val == 'on')
			$val = 1;

			$data[$key] = $val;

		}

        if ($set_type!='new') {
            // checks
			if (!preg_match('/^[0-9a-z.\-\_]+$/i', $data['shipping_code'])){
                $obj->success = false;
                $obj->error_message = __text('ERROR_SHIPPINGCODE_WHITESPACE');
                return $obj;  
            }
        }
        elseif ($set_type=='new' &&  empty($data['shipping_code'])) {
            $data['shipping_code'] = __text('TEXT_SHIPPING_CODE');
        }


        $oS = new adminDB_DataSave(TABLE_SHIPPING, $data, false, __CLASS__);
		$objS = $oS->saveDataSet();

		if ($set_type=='new') {	// edit existing
			$obj->new_id = $objS->new_id;
			$data = array_merge($data, array($this->master_id=>$objS->new_id));
		}

		$oSD = new adminDB_DataSave(TABLE_SHIPPING_DESCRIPTION, $data, true, __CLASS__);
		$objSD = $oSD->saveDataSet();

		$set_perm = new item_permission($this->perm_array);
		$set_perm->_saveData($data, $data[$this->_master_key]);

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_set_bottom')) ? eval($plugin_code) : false;

		if ($objS->success && $objSD->success) {

			$obj->success = true;
		} else {
			$obj->failed = true;
		}

		return $obj;
	}

	function _unset($id = 0) {
		global$xtPlugin,$db;
		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

		$set_perm = new item_permission($this->perm_array);
		$set_perm->_deleteData($id);

		$db->Execute("DELETE FROM ". $this->_table ." WHERE shipping_id = ?", array($id));
		$db->Execute("DELETE FROM ". $this->_table_lang ." WHERE shipping_id = ?", array($id));
		$db->Execute("DELETE FROM ". TABLE_SHIPPING_COST ." WHERE shipping_id = ?", array($id));

		($plugin_code = $xtPlugin->PluginCode('class.shipping.php:_unset_bottom')) ? eval($plugin_code) : false;
	}

	function _setStatus($id, $status) {
		global $db,$xtPlugin;

		$id = (int)$id;
		if (!is_int($id)) return false;

		$db->Execute(
			"update " . $this->_table . " set status = ? where shipping_id = ?",
			array($status, $id)
		);
	}
}