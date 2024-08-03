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

class tax extends xt_backend_cls{

	protected $_table = TABLE_TAX_RATES;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'tax_rates_id';
    public $country_code, $zone_id, $zone_id_digital;
    public array $data;

    /**
     * tax constructor.
     * @param bool $countryPreset  Vorgabe für nicht Kunden ohne Login
     */
	function __construct($countryPreset = false)
	{
		global $xtPlugin, $order_edit_controller, $customers_status, $db, $countries;

		($plugin_code = $xtPlugin->PluginCode('class.tax.php:tax_top')) ? eval($plugin_code) : false;
		if (isset($plugin_return_value)) return $plugin_return_value;

        if (!empty($_SESSION['registered_customer']))
        {
            $customer = $_SESSION['customer'];
            if(isset($customer))
            {
                switch ($customers_status->customers_status_tax_rates_calculation_base)
                {
                    case 'shipping_address':
                        /**
                         *   egal was wohin, die shipping address ist grundlage der berechnung
                         */
                        if (isset($customer->customer_shipping_address) && isset($customer->customer_shipping_address['customers_country_code']))
                        {
                            $this->country_code = $customer->customer_shipping_address['customers_country_code'];

                            $this->zone_id =
                            $this->zone_id_digital = $customer->customer_shipping_address['customers_zone'];
                        }
                        break;
                    case 'payment_address':
                        /**
                         *   egal was wohin, die payment address ist grundlage der berechnung
                         */
                        if (isset($customer->customer_payment_address) && isset($customer->customer_payment_address['customers_country_code']))
                        {
                            $this->country_code = $customer->customer_payment_address['customers_country_code'];

                            $this->zone_id =
                            $this->zone_id_digital =  $customer->customer_payment_address['customers_zone'];
                        }
                        break;
                    default:
                        /**
                         *   b2c_eu  digital wird gesondert, anhand der rechungsadresse berechnet
                         */
                        $this->country_code = $customer->customer_shipping_address['customers_country_code'];
                        $this->zone_id = $customer->customer_shipping_address['customers_zone'];
                        $this->zone_id_digital = $customer->customer_payment_address['customers_zone'];
                }
            }

        } else {
            $this->country_code = _STORE_COUNTRY;
            $this->zone_id = $this->zone_id_digital = _STORE_ZONE;

            if(!empty($countryPreset) && is_array($countries->countries_list) && array_key_exists($countryPreset, $countries->countries_list))
            {
                $zone_id = $db->GetOne(
                    "SELECT zone_id FROM " . TABLE_COUNTRIES."  where countries_iso_code_2 = ? ",array($countryPreset)
                );
                if($zone_id)
                {
                    $this->country_code = $countryPreset;
                    $this->zone_id = $this->zone_id_digital = $zone_id;
                }
            }
        }

        $order_edit_controller->hook_tax_build($this);

		($plugin_code = $xtPlugin->PluginCode('class.tax.php:tax_build')) ? eval($plugin_code) : false;
		$this->_buildData();
	}

	function setValues($data){
		$this->country_code = $data['country_code'];
		$this->zone_id = $data['zone'];
	}

	function _buildData(){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.tax.php:_buildData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute("SELECT tax_class_id, is_digital_tax FROM ".TABLE_TAX_CLASS);
			if($record->RecordCount() > 0){
				while(!$record->EOF){
					
					$data[$record->fields['tax_class_id']] = $this->_getTaxRates($record->fields['tax_class_id'], $record->fields['is_digital_tax']);
					
					$record->MoveNext();
				}$record->Close();
				($plugin_code = $xtPlugin->PluginCode('class.tax.php:_buildData_bottom')) ? eval($plugin_code) : false;
				$this->data = $data;
			}else{
				return false;
			}
	}

	function _getTaxRates($class_id, $is_digital_tax = false, $tax_country = false, $tax_zone = false){
		global $db, $xtPlugin, $currency, $customers_status;

        if(empty($tax_country)) $tax_country = $this->country_code;
        if(empty($tax_zone)) $tax_zone = $this->zone_id;

		($plugin_code = $xtPlugin->PluginCode('class.tax.php:_getTaxRates_top')) ? eval($plugin_code) : false;

		if(isset($plugin_return_value))
			return $plugin_return_value;

        /**
         *   je nachdem ob digital oder nicht > zone_id bzw zone_id_digital verwenden, siehe constructor
         */
        $tax_record = $db->GetArray(
            "SELECT * FROM " . TABLE_TAX_RATES . " where tax_class_id = ? AND tax_rate_countries = ? ",
            array($class_id, $tax_country)
        );
        if(!count($tax_record))
        {
            $tax_record = $db->GetArray(
                "SELECT * FROM " . TABLE_TAX_RATES . " where tax_class_id = ? AND tax_zone_id = ? AND (tax_rate_countries IS NULL OR tax_rate_countries = '')",
                array($class_id, $tax_zone)
            );
        }

		if(count($tax_record))
		{
			$currentCountryCode = '';

			$customer = $_SESSION['customer'];
			if(isset($customer))
			{
				switch ($customers_status->customers_status_tax_rates_calculation_base)
				{
					case 'shipping_address':
						if (isset($customer->customer_shipping_address) && isset($customer->customer_shipping_address['customers_country_code']))
						{
							$currentCountryCode = $customer->customer_shipping_address['customers_country_code'];
						}
						break;
					case 'payment_address':
						if (isset($customer->customer_payment_address) && isset($customer->customer_payment_address['customers_country_code']))
						{
							$currentCountryCode = $customer->customer_payment_address['customers_country_code'];
						}
						break;
					default:  // b2c_eu
						if ($is_digital_tax && isset($customer->customer_payment_address) && isset($customer->customer_payment_address['customers_country_code']))
						{
							$currentCountryCode = $customer->customer_payment_address['customers_country_code'];
						}
						else if (!$is_digital_tax && isset($customer->customer_shipping_address) && isset($customer->customer_shipping_address['customers_country_code']))
						{
							$currentCountryCode = $customer->customer_shipping_address['customers_country_code'];
						}
				}
				if(empty($currentCountryCode) && isset($customer->customer_default_address) && isset($customer->customer_default_address['customers_country_code']))
				{
					$currentCountryCode = $customer->customer_default_address['customers_country_code'];
				}
			}
			if(empty($currentCountryCode))
			{
			    $currentCountryCode = $this->country_code;
			}

			$tax_rate = 0;
			// filter by country
            $tax_records_country = array_values(array_filter($tax_record, function($arr) use ($currentCountryCode)
            {
                if($arr['tax_rate_countries'] == $currentCountryCode) return true;
                return false;
            }));
			if(count($tax_records_country))
            {
                $tax_rate = $tax_records_country[0]['tax_rate'];
            }
            else {
                // filter by zone
                $zone_id = $is_digital_tax ? $this->zone_id_digital : $this->zone_id;

                $tax_records_zone = array_values(array_filter($tax_record, function($arr) use($zone_id)
                {
                    if($arr['tax_zone_id'] == $zone_id) return true;
                    return false;
                }));
                if(count($tax_records_zone))
                {
                    $tax_rate = $tax_records_zone[0]['tax_rate'];
                }
                else {
                    $tax_records_no_zone = array_values(array_filter($tax_record, function($arr)
                    {
                        if($arr['tax_zone_id'] == 0 && empty($arr['tax_rate_countries'])) return true;
                        return false;
                    }));
                    if(count($tax_records_no_zone))
                    {
                        $tax_rate = $tax_records_no_zone[0]['tax_rate'];
                    }
                }
            }
            $tax_multiplier = 1.0 + ( $tax_rate / 100);
            ($plugin_code = $xtPlugin->PluginCode('class.tax.php:_getTaxRates_bottom')) ? eval($plugin_code) : false;
            $tax_res = ($tax_multiplier - 1.0) * 100;
            $tax_res = round($tax_res, $currency->decimals);
            return $tax_res;
		}else {
			return 0;
	    }
	}

	function _getParams() {
		$params = array();

		$header[$this->_master_key] = array('type' => 'hidden');

		$header['tax_class_id'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=tax_classes'
		);

		$header['tax_zone_id'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=tax_zones'
		);

		$header['tax_rate_countries'] = array(
			'type' => 'dropdown',
			'url' => 'DropdownData.php?get=countries',
		);

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
 		$params['languageTab']    = false;

		$params['include']        = array ('tax_rates_id', 'tax_class_id', 'tax_rate', 'tax_zone_id', 'tax_rate_countries');
		$params['exclude']        = array ('');
		$params['GroupField']     = "tax_class_id";
		$params['SortField']      = $this->_master_key;
		$params['SortDir']        = "ASC";

		$params['PageSize'] = 250;

		return $params;
	}
	
	public function get_tax_rate_countries() {
		global $db;
		$edit_id = $this->url_data['tax_rates_id'];
		$obj = new stdClass();
		$obj->topics = array();
		$obj->totalCount = 0;
	
		if (!empty($edit_id)) {
			$query = "SELECT tax_rate_countries FROM " . TABLE_TAX_RATES . " WHERE tax_rates_id=?";
			$record = $db->Execute($query, array((int)$edit_id));
			if($record->RecordCount() > 0) {
				$zones_array = explode(',', $record->fields['tax_rate_countries']);
	
				if (!empty($zones_array)) {
					$countries = new countries();
					foreach ($zones_array as $code) {
						if (isset($countries->countries_list[$code])) {
							$obj->topics[] = array('id' => $code, 'name' => $countries->countries_list[$code]['countries_name'], 'desc' => '');
						}
					}
					$obj->totalCount = count($obj->topics);
				}
			}
			return json_encode($obj);
		}
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

		if ($this->url_data['get_data'])
        $data = $table_data->getData();
        elseif($ID)
        $data = $table_data->getData($ID);
        else
		$data = $table_data->getHeader();

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type='edit'){
		global $db,$language,$filter;

		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();
		return $obj;
	}

	function _unset($id = 0) {
	    global $db;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;

	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
	}
}