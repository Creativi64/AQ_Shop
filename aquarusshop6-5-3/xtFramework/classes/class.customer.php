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

#[AllowDynamicProperties]
class customer extends check_fields{

	public $customers_id;
	public $customers_status;
	public $customer_info = array('account_type'=>0);
	public $customer_default_address = array();
	public $customer_shipping_address = array();
	public $customer_payment_address = array();
	public $error = false;

	public $_master_key = 'customers_id';
	public $_master_key_add = 'address_book_id';
	public $_table = TABLE_CUSTOMERS;
	public $_table_add = TABLE_CUSTOMERS_ADDRESSES;
	public $password_special_signs = 3;

	public $master_id = 'customers_id';

	function __construct($customer_id=''){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:customer_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!empty($customer_id)){
			$this->customers_id = $customer_id;
			$this->_customer($customer_id);
		}elseif(!empty($_SESSION['registered_customer'])){
			$this->customers_id = $_SESSION['registered_customer'];
			$this->_customer($_SESSION['registered_customer']);
		}else{
			$this->customers_id = 0;
			$this->customers_status = _STORE_CUSTOMERS_STATUS_ID_GUEST;
		}
	}

	function _customer($customer_id){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_customer_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!empty($customer_id)){
			$this->customers_id = $customer_id;
			$this->customer_info = $this->_buildData($customer_id);
			$this->customers_status = $this->customer_info['customers_status'] ?? 0;
			$this->customer_default_address = $this->_buildAddressData($customer_id, 'default');
			$this->customer_payment_address = $this->_buildAddressData($customer_id, 'payment');
			$this->customer_shipping_address = $this->_buildAddressData($customer_id, 'shipping');
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_customer_bottom')) ? eval($plugin_code) : false;

	}

	function _buildData($cID){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS . " where customers_id=?", array($cID));
		if($record->RecordCount() > 0){
			while(!$record->EOF){
				($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildData_data')) ? eval($plugin_code) : false;
				unset($record->fields['customers_password']);

				if($record->fields['account_type']=='1'){
					//$record->fields['customers_status'] = _STORE_CUSTOMERS_STATUS_ID_GUEST; // fix f�r falsche anzeige der kindengruppe im backend, wenn be im 'falschen' mandanten ge�ffnet
				}

				$data = $record->fields;
				$record->MoveNext();
			}$record->Close();
			($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildData_bottom')) ? eval($plugin_code) : false;
			return $data;
		}else{
			return false;
		}
	}


	function   _buildAddressData($cID, $type='', $id=''){
		global $db, $xtPlugin, $countries, $system_status, $language;

		if (!is_object($countries)) {
			$countries = new countries('true','store');
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildAdressData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        $sql_qry = '';
		if(empty($type) && !empty($id)){
			$sql_qry = "and address_book_id='".(int)$id."'";
		}elseif(!empty($type) && empty($id)){
			$sql_qry = "and address_class=".$db->Quote($type)."";
		}

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=? {$sql_qry} ORDER BY address_book_id DESC LIMIT 1", array($cID));
		if($record->RecordCount() > 0){
			while(!$record->EOF){
				($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildAdressData_data')) ? eval($plugin_code) : false;

				$country  = $countries->_getCountryData($record->fields['customers_country_code']);

				$record->fields['customers_dob'] = date_short($record->fields['customers_dob'], _STORE_ACCOUNT_DOB_FORMAT);

				$record->fields['customers_country'] = $country['countries_name'];

				if(_STORE_ACCOUNT_FEDERAL_STATES == 'true'){
					if ($record->fields['customers_federal_state_code']>0){

                        $clear_data = $db->GetOne('SELECT states_id FROM xt_federal_states WHERE country_iso_code_2 = ? AND states_id = ?',
                                [$record->fields['customers_country_code'], $record->fields['customers_federal_state_code']]) == false;

                        if($clear_data)
                        {
                            $db->Execute('UPDATE '.TABLE_CUSTOMERS_ADDRESSES.' SET customers_federal_state_code = NULL WHERE address_book_id = ?',
                                [$record->fields['address_book_id']]);
                        }
                        else {
                            $fst_record = $db->Execute(
                                "SELECT fsd.*, fs.states_code FROM ".TABLE_FEDERAL_STATES_DESCRIPTION." fsd INNER JOIN ".TABLE_FEDERAL_STATES." fs ON fs.states_id=fsd.states_id WHERE fsd.states_id = ? AND fsd.language_code = ? LIMIT 1",
                                array($record->fields['customers_federal_state_code'], $language->code)
                            );
                            $record->fields['customers_country'] = $fst_record->fields['state_name'].', '.$record->fields['customers_country'];
                            $record->fields['customers_federal_state_code_iso'] = $fst_record->fields['states_code'];
                        }
					}
				}

				$record->fields['customers_zone'] = $country['zone_id'];

				$data = $record->fields;
				$data['customers_age'] = current_age($record->fields['customers_dob']);
				$data['allow_change'] = true;
				$record->MoveNext();
			}$record->Close();
			($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildAdressData_bottom')) ? eval($plugin_code) : false;
			return $data;
		}else{
			$record = '';
			$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=? and address_class='default'", array($cID));
			if($record->RecordCount() > 0){
				while(!$record->EOF){
					($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildAdressData_data')) ? eval($plugin_code) : false;

					$record->fields['customers_dob'] = date_short($record->fields['customers_dob'], _STORE_ACCOUNT_DOB_FORMAT);

					$country  = $countries->_getCountryData($record->fields['customers_country_code']);

					$record->fields['customers_country'] = $country['countries_name'];

					if(_STORE_ACCOUNT_FEDERAL_STATES == 'true'){
						if ($record->fields['customers_federal_state_code']>0){

                            $clear_data = $db->GetOne('SELECT states_id FROM xt_federal_states WHERE country_iso_code_2 = ? AND states_id = ?',
                                    [$record->fields['customers_country_code'], $record->fields['customers_federal_state_code']]) == false;

                            if($clear_data)
                            {
                                $db->Execute('UPDATE '.TABLE_CUSTOMERS_ADDRESSES.' SET customers_federal_state_code NULL WHERE address_book_id = ?',
                                    [$record->fields['address_book_id']]);
                            }
                            else
                            {
                                $fst_record = $db->Execute(
                                    "SELECT fsd.*, fs.states_code FROM " . TABLE_FEDERAL_STATES_DESCRIPTION . " fsd INNER JOIN " . TABLE_FEDERAL_STATES . " fs ON fs.states_id=fsd.states_id WHERE fsd.states_id = ? AND fsd.language_code = ? LIMIT 1",
                                    array($record->fields['customers_federal_state_code'], $language->code)
                                );
                                $record->fields['customers_country'] = $fst_record->fields['state_name'] . ', ' . $record->fields['customers_country'];
                                $record->fields['customers_federal_state_code_iso'] = $fst_record->fields['states_code'];
                            }
						}
					}

					$record->fields['customers_zone'] = $country['zone_id'];

					$data = $record->fields;

					$data['customers_age'] = current_age($record->fields['customers_dob']);
					$data['allow_change'] = true;
					$record->MoveNext();
				}$record->Close();
				($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildAdressData_bottom')) ? eval($plugin_code) : false;
				return $data;
			}else{
				return false;
			}
		}
	}

	function _getAdressList($cID, $filter = false){
		global $db, $xtPlugin, $countries, $language;

		if (!is_object($countries)) {
			$countries = new countries('true','store');
		}

		$filter_sql = '';
        $sort_sql = '';
        $param_array = array($cID);
		if(is_array($filter) && count($filter))
        {
            for($i=0, $p='?'; $i<(count($filter)-1); $i++) $p .= ',?';
            $filter_sql = "AND address_class IN (".$p.")";
            $param_array = array_merge($param_array, $filter);
            $sort_sql = ' ORDER BY address_class DESC ';
        }

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getAdressList_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=? ".$filter_sql.$sort_sql, $param_array);
		if($record->RecordCount() > 0){
			while(!$record->EOF){
				($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getAdressList_data')) ? eval($plugin_code) : false;

				$country  = $countries->_getCountryData($record->fields['customers_country_code']);

				$record->fields['customers_country'] = $country['countries_name'];
				$record->fields['id'] = $record->fields['address_book_id'];

				$record->fields['text'] = $record->fields['customers_company'].' '. $record->fields['customers_firstname'].' '.$record->fields['customers_lastname'].' ('.$record->fields['customers_street_address'].' '.$record->fields['customers_postcode'] .'  '.$record->fields['customers_city'] .')';
				$record->fields['allow_change'] = true;
				($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getAdressList_data_bottom')) ? eval($plugin_code) : false;
				$data[] = $record->fields;
				$record->MoveNext();
			}$record->Close();

			if(_STORE_ACCOUNT_FEDERAL_STATES == 'true'){
				foreach ($data as $key => $value){
					if ($value['customers_federal_state_code']>0){
						$parent_country = substr($value['customers_country_code'], 0, 2);
						unset ($record);
						$record = $db->Execute(
							"SELECT * FROM ".TABLE_FEDERAL_STATES_DESCRIPTION." fsd WHERE fsd.states_id = ? AND fsd.language_code = ? LIMIT 1",
							array($value['customers_federal_state_code'], $language->code)
						);
						$data[$key]['customers_country'] = $record->fields['state_name'].', '.$value['customers_country'];
					}
				}
			}

			($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getAdressList_bottom')) ? eval($plugin_code) : false;
			return $data;
		}else{
			return false;
		}
	}

	function _registerCustomer(array $data, $register_type='both', $add_type = 'insert', $check_data=true, $login_customer=true){
		global $db, $xtPlugin, $store_handler, $countries, $xtLink, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_registerCustomer_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;


		$this->error = false;
		$data["cust_info"]['customers_email_address'] = trim($data["cust_info"]['customers_email_address']);
		$data["cust_info"]['customers_email_address_confirm'] = trim($data["cust_info"]['customers_email_address_confirm']);
        // transform dob (date pickers etc)
        //if (isset($data['default_address']['customers_dob'])) {
		if ($data['default_address']['customers_dob']!='') {
            $dob=trim($data['default_address']['customers_dob']);
            $data['default_address']['customers_dob'] = date('d.m.Y', strtotime($dob));
        }

        if(empty($data["default_address"]["customers_federal_state_code"]))
            $data["default_address"]["customers_federal_state_code"] = $data["default_address"]['customers_federal_state_code'.$data["default_address"]["customers_country_code"]];

		if(is_array($data['cust_info'])){

            /**
             *  2019 nov
             *  wegen abwärts-kompat, wurde der name des inputs "guest-account" nicht geändert
             *  eigentlich bezeichnet "guest-account" von jetzt an register, also soll ein account mit pwd angelegt werden
             *  $data["guest-account"] == guest ist aus dem alten tpl mit dropdown, dort wurde immer ein value gesendet (sollte damit abwärt kompat sein)
             */
			if(_STORE_ALLOW_GUEST_ORDERS == 'true' && (empty($data["guest-account"]) || $data["guest-account"]=='guest') ) // checkbox ist nicht angehakt, also Gast
			{
				$data['cust_info']['guest'] = 1;
                $data['cust_info']['customers_password'] = $data['cust_info']['customers_password_confirm'] = '';
                $data['cust_info']['password_required'] = 0;
			}else{
                $data['cust_info']['password_required'] = 1;
                if(empty($data["guest-account"]) && _STORE_ALLOW_GUEST_ORDERS == 'true')   // checkbox ist nicht angehakt, dh Bestätigung zum Account-Anlegen fehlt
                {
                    $this->error = true;
                    $info->_addInfo(constant('ERROR_ACCEPT_ACOUNT_CREATION'));
                }
			}

			$this->_checkCustomerData($data);

            if($data["guest-account"] == 'guest' && !empty($data["cust_info"]["customers_vat_id"]))
            {
                $this->error = true;
                $info->_addInfo(__text('TEXT_NO_GUEST_ALLOWED_WITH_VAT'));
            }

		}

		if(is_array($data['default_address'])){
			$this->_checkCustomerAddressData($data['default_address'], $add_type, $check_data);
		}

        if(array_key_exists('shipping_address', $data) && is_array($data['shipping_address'])){
			$this->_checkCustomerAddressData($data['shipping_address'], $add_type, $check_data);
		}

		if(array_key_exists('payment_address', $data) && is_array($data['payment_address'])){
			$this->_checkCustomerAddressData($data['payment_address'], $add_type, $check_data);
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_registerCustomer_address')) ? eval($plugin_code) : false;

		if($this->error == true){
			$data['error'] = true;
			return $data;
		}

		if(is_array($data['cust_info'])){
			$this->_buildCustomerData($data);
			$data['cust_info'] = $this->customerData;
		}

		if(is_array($data['default_address'])){
			$this->_buildCustomerAddressData($data['default_address']);
			$data['default_address'] = $this->customerAdressData['default'];
		}

		if(is_array($data['shipping_address'])){
			$this->_buildCustomerAddressData($data['shipping_address']);
			$data['shipping_address'] = $this->customerAdressData['shipping'];
		}

		if(is_array($data['payment_address'])){
			$this->_buildCustomerAddressData($data['payment_address']);
			$data['payment_address'] = $this->customerAdressData['payment'];
		}


		if($data['cust_info']['guest'] !=1)
		$this->_sendAccountMail();

		if($login_customer == true){
			$_SESSION['registered_customer'] = $this->data_customer_id;
			$this->_customer($_SESSION['registered_customer']);
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_registerCustomer_bottom')) ? eval($plugin_code) : false;

		$data['success'] = true;

		return $data;
	}

	function _updateAddressClass($id, $customer, $class){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_updateAddressClass_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$update_record = array('address_class'=>$class);
		$db->AutoExecute(TABLE_CUSTOMERS_ADDRESSES, $update_record, 'UPDATE', "customers_id=".(int)$customer." and address_book_id =".(int)$id);
	}

	function _checkCustomerData($data, $add_type = 'insert', $check_data=true){
		global $db, $xtPlugin, $store_handler, $xtLink;

		$form_data = $data;

		if(is_array($data['cust_info']))
		$data = $data['cust_info'];

		$this->error = false;
		$data['error'] = false;

		if($check_data == true){
			$this->_checkLenght($data['customers_email_address'], _STORE_EMAIL_ADDRESS_MIN_LENGTH, __text('ERROR_EMAIL_ADDRESS'));
            $this->_checkEmailAddress($data['customers_email_address'], __text('ERROR_EMAIL_ADDRESS_SYNTAX'));
			$this->_checkMatch($data['customers_email_address'], $data['customers_email_address_confirm'], __text('ERROR_EMAIL_ADDRESS_NOT_MATCHING'), true);
			if(constant('_STORE_ACCOUNT_DOB') == 'true')
            {
                $this->_checkMinAge($form_data["default_address"]["customers_dob"]);
            }

			if ($add_type=='insert') {
				$this->_checkVatId($form_data,__text('ERROR_VAT_ID'));
			}

			if ($add_type === 'update')
			{
				$form_data['cust_info']['customers_vat_id'] = $form_data["customers_vat_id"];
				$form_data['default_address']['customers_country_code'] = $_SESSION['customer']->customer_default_address["customers_country_code"];
				$this->_checkVatId($form_data,__text('ERROR_VAT_ID'));
			}
			else
			{
			    $reset_link = $xtLink->_link(array('page'=>'customer', 'conn'=>'SSL', 'paction'=>'password_reset'));
			    $reset_link = '<a href='.$reset_link.'>'.__text('TEXT_LINK_LOSTPASSWORD').'</a>';
				$this->_checkExist($data['customers_email_address'], 'customers_email_address', TABLE_CUSTOMERS, "account_type = 0 and shop_id = ".$store_handler->shop_id, __text('ERROR_EMAIL_ADDRESS_EXISTS'). ' '.$reset_link);
			}

			if($data['guest']!=1 && $data['password_required']==1){
				$this->_checkLenght($data['customers_password'], _STORE_PASSWORD_MIN_LENGTH, __text('TEXT_PASSWORD_ERROR'));
				$this->_checkMatch($data['customers_password'], $data['customers_password_confirm'], __text('ERROR_PASSWORD_NOT_MATCHING'));
			}elseif($add_type == 'update' && $data['customers_password']!=''){
				$this->_checkLenght($data['customers_password'], _STORE_PASSWORD_MIN_LENGTH, __text('TEXT_PASSWORD_ERROR'));
				$this->_checkMatch($data['customers_password'], $data['customers_password_confirm'], __text('ERROR_PASSWORD_NOT_MATCHING'));
				$this->_checkCurrentPassword($data['customers_password_current'],$data['customers_id'], __text('ERROR_CURRENT_PASSWORD_NOT_MATCHING'));
			}else{
				if(!empty($data['customers_password']))
				$this->_checkMatch($data['customers_password'], $data['customers_password_confirm'], __text('ERROR_PASSWORD_NOT_MATCHING'));

			}

			($plugin_code = $xtPlugin->PluginCode('class.customer.php:_CustomerData_check')) ? eval($plugin_code) : false;

		}

		if($this->error == true){
			$data['error'] = true;
			return false;
		}

	}


	function _buildCustomerData($data, $add_type = 'insert', $check_data=true){
		global $db, $xtPlugin, $store_handler, $language, $currency, $countries;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildCustomerData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$form_data = $data;

		if(is_array($data['cust_info']))
		$data = $data['cust_info'];

		if($data['guest']==1)
		$data['customers_status'] = _STORE_CUSTOMERS_STATUS_ID_GUEST;

		if (!is_object($countries)) {
			$countries = new countries('true','store');
		}
		// xt 5.0 load customers status settings from zone
		$country  = $countries->_getCountryData($form_data['default_address']['customers_country_code']);
		$zone_id = $country['zone_id'];
		$zone_data_str = $db->GetOne('SELECT status_values FROM '.TABLE_SYSTEM_STATUS.' WHERE status_id=?', array($zone_id));
		$zone_data = unserialize(stripslashes($zone_data_str));

		if ($add_type=='insert')
		{
			if ($data['customers_status'] == 0 || !$data['customers_status'])
			{
			$data['customers_status'] = _STORE_CUSTOMERS_STATUS_ID;
				// xt 5.0 override shop setting with vat-zone settings
				$key = '_ZONE_CUSTOMERS_STATUS_shop_'.$store_handler->shop_id;
				if(is_array($zone_data) && is_array($zone_data['data'])
					&& array_key_exists($key,$zone_data['data']) && !empty($zone_data['data'][$key]))
				{
					$data['customers_status'] = (int) $zone_data['data'][$key];
				}
			}

		// vat ID check

			if (isset($data['customers_vat_id']) && $data['customers_vat_id']!='' && $data['customers_status'] != constant('_STORE_CUSTOMERS_STATUS_ID_GUEST')) {
				$vat_check = $this->_checkVatId($form_data,'',$return_val = true);
				if ($vat_check==true) {
					if (_STORE_VAT_CHECK_MOVE=='true') {
						if ($form_data['default_address']['customers_country_code']!=_STORE_COUNTRY) {
							$data['customers_status'] = _STORE_VAT_CHECK_STATUS_OUT;
						} else {
							$data['customers_status'] = _STORE_VAT_CHECK_STATUS_IN;
						}
						// xt 5.0 override shop setting with vat-zone settings
						$key = '_ZONE_VAT_CUSTOMERS_STATUS_shop_'.$store_handler->shop_id;
						if(is_array($zone_data) && is_array($zone_data['data'])
							&& array_key_exists($key,$zone_data['data']) && !empty($zone_data['data'][$key]))
						{
							$data['customers_status'] = (int) $zone_data['data'][$key];
						}
					}
					$data['customers_vat_id_status'] = '1';
				} else {
					$data['customers_vat_id_status'] = '0';
				}
			}
		}
		elseif ($add_type === 'update')
		{
			if (isset($data['customers_vat_id']) && $data['customers_vat_id'] != '')
			{
				$data['cust_info']['customers_vat_id'] = $data['customers_vat_id'];
				$data['default_address']['customers_country_code'] = $_SESSION['customer']->customer_default_address["customers_country_code"];
				$vat_check = $this->_checkVatId($data,'',$return_val = true);

				if ($vat_check == true)
				{
					if (_STORE_VAT_CHANGE_CLIENT_GROUP_ON_VAT_CHANGE=='true')
					{
						$data['customers_status'] = ($_SESSION['customer']->customer_default_address["customers_country_code"] != _STORE_COUNTRY)
							? _STORE_VAT_CHECK_STATUS_OUT
							: _STORE_VAT_CHECK_STATUS_IN;
					}
					// xt 5.0 override shop setting with vat-zone settings
					$key = '_ZONE_VAT_CUSTOMERS_STATUS_shop_'.$store_handler->shop_id;
					if(is_array($zone_data) && is_array($zone_data['data'])
						&& array_key_exists($key,$zone_data['data']) && !empty($zone_data['data'][$key]))
					{
						$data['customers_status'] = (int) $zone_data['data'][$key];
					}

					$data['customers_vat_id_status'] = '1';
				} else {
					$data['customers_vat_id_status'] = '0';
				}
			}
			else {
				if (_STORE_VAT_CHANGE_CLIENT_GROUP_ON_VAT_CHANGE=='true')
				{
					$data['customers_status'] = _STORE_CUSTOMERS_STATUS_ID;
				}
				$data['customers_vat_id_status'] = '0';
			}
		}

        $currencies = $currency->_getCurrencyList();
        foreach($currencies as $k => $v)
        {
            $currencies[$k] = $v['code'];
        }
        if(empty($form_data['customers_default_currency']) || !in_array($form_data['customers_default_currency'],$currencies))
        {
            $form_data['customers_default_currency'] = $currency->default_currency;
        }
        $languages = $language->_getLanguageList();
        foreach($languages as $k => $v)
        {
            $languages[$k] = $v['code'];
        }
        if(empty($form_data['customers_default_language']) || !in_array($form_data['customers_default_language'],$languages))
        {
            $form_data['customers_default_language'] = $language->code;
        }


		$customer_data_array = array (
			'customers_gender'  => $data['customers_gender'],
			'customers_vat_id' => $data['customers_vat_id'],
			'customers_vat_id_status' => $data['customers_vat_id_status'],
			'customers_email_address' => $data['customers_email_address'],
			'customers_default_currency' => $form_data['customers_default_currency'],
			'customers_default_language' => $form_data['customers_default_language'],
			'shop_id' => $data['shop_id']
		);

		if (empty($customer_data_array['shop_id']))
		$customer_data_array['shop_id'] = $store_handler->shop_id;

		$customer_data_array['customers_id'] = $data['customers_id'];

		if(!empty($data['customers_status']))
			$customer_data_array['customers_status'] = $data['customers_status'];

		if($data['guest']==1){
			$customer_data_array['account_type'] = 1;
		}else{
            $customer_data_array['account_type'] = 0;
			if(!empty($data['customers_password']))
			$customer_data_array['customers_password'] = $data['customers_password'];
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildCustomerData_bottom')) ? eval($plugin_code) : false;
		$this->customerData = $data;

		$this->_writeCustomerData($customer_data_array, $add_type);
		$this->customerData['success'] = true;
	}

    /**
     * @param $data
     * @param string $add_type
     * @param bool $check_data
     * @return false|mixed|void
     */
	function _checkCustomerAddressData($data, $add_type = 'insert', $check_data = true){
		global $db, $xtPlugin;

		$data['error'] = false;

        ($plugin_code = $xtPlugin->PluginCode('class.customer.php:_checkCustomerAddressData_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

		if($check_data == true){
			if (_STORE_ACCOUNT_GENDER == 'true')
			    $this->_checkGender($data['customers_gender']);

            // check date of birth and phone number if activated
            if (_STORE_ACCOUNT_DOB == 'true') {
            	if (!defined('_STORE_ACCOUNT_DOB_FORMAT')) define('_STORE_ACCOUNT_DOB_FORMAT', 'dd.mm.yyyy');
            	$this->_checkDate($data['customers_dob'], _STORE_ACCOUNT_DOB_FORMAT, __text('ERROR_DATE_SYNTAX'));
            }
            if (_STORE_TELEPHONE_MIN_LENGTH > 0) {
            	$this->_checkLenght($data['customers_phone'], _STORE_TELEPHONE_MIN_LENGTH, __text('ERROR_TELEPHONE_NUMBER'));
            }

			if (_STORE_MOBILE_PHONE_MIN_LENGTH > 0) {
				$this->_checkLenght($data['customers_mobile_phone'], _STORE_MOBILE_PHONE_MIN_LENGTH, __text('ERROR_MOBILE_PHONE_NUMBER'));
			}

            if (_STORE_ACCOUNT_USE_TITLE == 1)
            {
                $this->_checkTitle($data['customers_title']);
            }


           // var_dump($data); exit();
            if(array_key_exists('old_address_class', $data) && $data['old_address_class']== 'default' && $data['address_class'] != 'default'){
				$this->_checkDefaultAddress($data['customers_id'], __text('ERROR_DEFAULT_ADDRESS'));
            }
            // end check date and phone

			$this->_checkLenght($data['customers_firstname'], _STORE_FIRST_NAME_MIN_LENGTH, __text('ERROR_FIRST_NAME'));
			$this->_checkLenght($data['customers_lastname'], _STORE_LAST_NAME_MIN_LENGTH, __text('ERROR_LAST_NAME'));
			$this->_checkLenght($data['customers_street_address'], _STORE_STREET_ADDRESS_MIN_LENGTH, __text('ERROR_STREET_ADDRESS'));
			$this->_checkLenght($data['customers_postcode'], _STORE_POSTCODE_MIN_LENGTH, __text('ERROR_POST_CODE'));
			$this->_checkLenght($data['customers_city'], _STORE_CITY_MIN_LENGTH, __text('ERROR_CITY'));

			if (defined('_STORE_COMPANY_MIN_LENGTH') && _STORE_COMPANY_MIN_LENGTH > 0) {
				$this->_checkLenght($data['customers_company'], _STORE_COMPANY_MIN_LENGTH, __text('ERROR_COMPANY'));
			}

			if (defined('_STORE_FAX_MIN_LENGTH') && _STORE_FAX_MIN_LENGTH > 0) {
				$this->_checkLenght($data['customers_fax'], _STORE_FAX_MIN_LENGTH, __text('ERROR_FAX'));
			}

			($plugin_code = $xtPlugin->PluginCode('class.customer.php:_CustomerAddressData_check')) ? eval($plugin_code) : false;
		}

		if($this->error == true){
			$data['error'] = true;
			return false;
		}

	}


	function _buildCustomerAddressData($data, $add_type = 'insert', $check_data=true){
		global $db, $xtPlugin;

		$update_address_class = true;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_buildCustomerAddressData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(empty($data['customers_id']))
		$data['customers_id'] = $this->data_customer_id;

		if($data['customers_dob']){
			$data['customers_dob'] = strtotime($data['customers_dob']);
			$data['customers_dob'] = date('Y-m-d', $data['customers_dob']);
		}
		if (_STORE_SHOW_PHONE_PREFIX=='true'){
            $countries = new countries(true,'store');
            if ($data['customers_phone_prefix']!='')
                $data['customers_phone'] = $data['customers_phone_prefix'].$countries->phone_delimiter.$data['customers_phone'];
            if ($data['customers_mobile_phone_prefix']!='')
                $data['customers_mobile_phone'] = $data['customers_mobile_phone_prefix'].$countries->phone_delimiter.$data['customers_mobile_phone'];
            if ($data['customers_fax_prefix']!='')
                $data['customers_fax'] = $data['customers_fax_prefix'].$countries->phone_delimiter.$data['customers_fax'];
        }
		$address_data_array = array (
			'customers_id'  => $data['customers_id'],
			'customers_gender' => $data['customers_gender'],
            'customers_title' => $data['customers_title'],
			'customers_dob' => $data['customers_dob'],
			'customers_phone' => $data['customers_phone'],
			'customers_mobile_phone' => $data['customers_mobile_phone'],
			'customers_fax' => $data['customers_fax'],
			'customers_company' => $data['customers_company'],
			'customers_company_2' => $data['customers_company_2'],
			'customers_company_3' => $data['customers_company_3'],
			'customers_firstname' => $data['customers_firstname'],
			'customers_lastname' => $data['customers_lastname'],
			'customers_street_address' => $data['customers_street_address'],
            'customers_address_addition' => substr($data['customers_address_addition'], 0 , 64),
			'customers_suburb' => $data['customers_suburb'],
			'customers_postcode' => $data['customers_postcode'],
			'customers_city' => $data['customers_city'],
			'customers_state' => $data['customers_state'],
			'customers_country_code' => $data['customers_country_code'],
			'customers_federal_state_code' => $data['customers_federal_state_code'],
			'customers_federal_state_code_iso' => $data['customers_federal_state_code_iso'],
			'address_class' => $data['address_class'],
			'external_id' => $data['external_id']
		);


		if(!empty($data['address_book_id']))
		$address_data_array['address_book_id'] = $data['address_book_id'];

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_registerCustomer_AddressData_bottom')) ? eval($plugin_code) : false;

		$this->customerAdressData[$data['address_class']] = $data;

		$this->_writeAddressData($address_data_array, $add_type);
		if($update_address_class == true){
			$this->_updateAddressClass($this->address_book_id, $data['customers_id'] , $data['address_class']);
		}
		$this->customerAdressData[$data['address_class']]['address_book_id'] = $this->address_book_id;
		$this->customerAdressData[$data['address_class']]['success'] = true;
	}

	function _writeCustomerData($data, $type='insert'){
		global $db, $xtPlugin, $store_handler;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_writeCustomerData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(!empty($data['customers_password'])){
			$pw = new xt_password();
			$data['customers_password'] = $pw->hash_password($data['customers_password']);
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_writeCustomerData_bottom')) ? eval($plugin_code) : false;

		if($type=='insert'){
			$insert_record = array('date_added'=>$db->BindTimeStamp(time()));
			$record = array_merge($insert_record, $data);
			$db->AutoExecute(TABLE_CUSTOMERS, $record, 'INSERT');
			$this->data_customer_id = $db->Insert_ID();
		}elseif($type=='update'){
			$update_record = array('last_modified'=>$db->BindTimeStamp(time()));
			$record = array_merge($update_record, $data);
			unset($record["customers_email_address"]);
			$db->AutoExecute(TABLE_CUSTOMERS, $record, 'UPDATE', "customers_id=".(int)$data['customers_id']."");
			$this->data_customer_id = $data['customers_id'];
		}

	}

    /**
     * @param $data
     * @param string $type
     * @return mixed
     */
	function _writeAddressData($data, $type='insert'){
		global $db, $xtPlugin, $store_handler;

		// fix adodb force null
        if(empty($data['customers_federal_state_code']))
            $data['customers_federal_state_code'] = null;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_writeAddressData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_writeAddressData_bottom')) ? eval($plugin_code) : false;

		if($type=='insert'){
			$insert_record = array('date_added'=>$db->BindTimeStamp(time()));
			$record = array_merge($insert_record, $data);
			$db->AutoExecute(TABLE_CUSTOMERS_ADDRESSES, $record, 'INSERT');
			$this->address_book_id = $db->Insert_ID();
		}elseif($type=='update'){
			$update_record = array('last_modified'=>$db->BindTimeStamp(time()));
			$record = array_merge($update_record, $data);
			$db->AutoExecute(TABLE_CUSTOMERS_ADDRESSES, $record, 'UPDATE', "address_book_id=".$db->Quote($data['address_book_id'])." and customers_id=".(int)$data['customers_id']);
			$this->address_book_id = $data['address_book_id'];
		}
        return $this->address_book_id;
	}

	function _deleteAddressData($id, $cid){
		global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_deleteAddressData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data['error'] = false;

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=?", array($cid));
		if($record->RecordCount() == 1){
			$data['error']='true';
			$data['message']=__text('ERROR_DELETE_LAST_ADDRESS');
			$data['message_type']='error';
			return $data;
		}else{
            $result=$record->GetAll();
            $filtered=array_filter($result,function($item){$ret=false;if($item['address_class']=="default"){$ret=true;}return $ret;});
            $rs = $db->Execute("SELECT address_class FROM " . TABLE_CUSTOMERS_ADDRESSES . " where address_book_id =?", array($id));
            if ($rs->RecordCount() > 0) {
                if ($rs->fields['address_class'] == 'default' && count($filtered)<=1) {
                    $data['error'] = 'true';
                    $data['message'] = __text('ERROR_DELETE_DEFAULT_ADDRESS');
                    $data['message_type'] = 'error';
                    return $data;
                }
            }

        }

		$db->Execute(
			"DELETE FROM ". TABLE_CUSTOMERS_ADDRESSES ." WHERE address_book_id = ? and customers_id=?",
			array($id, $cid)
		);

		$data['success'] = 'true';
		$data['message']=SUCCESS_DELETE_ADDRESS;
		$data['message_type']='success';
		return $data;
	}

	function _sendAccountMail(){
		global $db, $xtPlugin, $store_handler;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_sendAccountMail_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$mail = new xtMailer('create_account');
		$mail->_addReceiver($this->customerData['customers_email_address'],$this->customerAdressData['default']['customers_lastname'].' '.$this->customerAdressData['default']['customers_firstname']);
		$mail->_assign('address_data',$this->customerAdressData);
		$mail->_assign('customers_data',$this->customerData);
		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_sendAccountMail_bottom')) ? eval($plugin_code) : false;
		$mail->_sendMail();
	}

	function _sendPasswordOptIn() {
		global $db,$xtPlugin, $store_handler,$xtLink;

		$request_key = $this->generateRandomString(32,0);
		$db->Execute(
			"UPDATE ".TABLE_CUSTOMERS." SET password_request_key=? WHERE customers_id=?",
			array($request_key, $this->customers_id)
		);

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_sendPasswordOptIn')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;

		$mail = new xtMailer('password_optin');
		$mail->_addReceiver($this->customer_info['customers_email_address'],$this->customer_default_address['customers_lastname'].' '.$this->customer_default_address['customers_firstname']);
		$mail->_assign('address_data',$this->customer_default_address);
		$mail->_assign('customers_data',$this->customerData);

		$remember_link = $xtLink->_link(array('page'=>'customer', 'paction'=>'login','params'=>'action=check_code&remember='.$this->customers_id.':'.$request_key,'conn'=>'SSL'));

		$mail->_assign('remember_link',$remember_link);
		$mail->_sendMail();

	}


	function _sendNewPassword($password='') {
		global $db,$xtPlugin, $store_handler,$xtLink;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_sendNewPassword')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;

		$password = $this->generateRandomString(_STORE_PASSWORD_MIN_LENGTH,$this->password_special_signs);
		$pw = new xt_password();
		$password_hash = $pw->hash_password($password);

		$db->Execute(
			"UPDATE ".TABLE_CUSTOMERS." SET password_request_key='',password_type='0',customers_password=? WHERE customers_id=?",
			array($password_hash, $this->customers_id)
		);

		$mail = new xtMailer('new_password');
		$mail->_addReceiver($this->customer_info['customers_email_address'],$this->customer_default_address['customers_lastname'].' '.$this->customer_default_address['customers_firstname']);
		$mail->_assign('address_data',$this->customer_default_address);
		$mail->_assign('customers_data',$this->customerData);
		$mail->_assign('NEW_PASSWORD',$password);
		$mail->_sendMail();

	}

	/**
	 * generate more secure token/passwords
	 * @param number $length
	 * @param number $specialSigns
	 * @return string
	 */
	function generateRandomString($length=32,$specialSigns = 0) {

		$newpass = "";
		$laenge=$length;
		$laengeS = $specialSigns;
		$string="ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789";
		$stringS = "!#$%&()*+,-./";

		mt_srand();

		for ($i=1; $i <= $laenge; $i++) {
			$newpass .= substr($string, mt_rand(0,strlen($string)-1), 1);
		}
		for ($i = 1; $i <= $laengeS; $i++) {
			$newpass .= substr($stringS, mt_rand(0, strlen($stringS) - 1), 1);
		}
		$newpass_split = str_split($newpass);
		shuffle($newpass_split);
		$newpass = implode($newpass_split);
		return $newpass;
	}

	function _setAdress($id,$type){
		global $db, $xtPlugin, $store_handler;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_setAdress_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data = $this->_buildAddressData($this->customers_id, '', $id);

		if($type=='payment')
		$this->customer_payment_address = $data;

		if($type=='shipping')
		$this->customer_shipping_address = $data;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_setAdress_bottom')) ? eval($plugin_code) : false;

	}

	/**
	 * query total amount for given order status of customer, or amount of all orders
	 *
	 * @param int $status
	 * @return decimal
	 */
	function _getTotalOrderAmount($status = '') {
		global $db,$store_handler;

		if ($status == '') {
			$query = "SELECT os.orders_stats_price,o.currency_value FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_STATS." os WHERE o.orders_id=os.orders_id and o.customers_id=?";
			$rs = $db->Execute($query, array($this->customers_id));
		} else {
			$status = (int)$status;
			$query = "SELECT os.orders_stats_price,o.currency_value FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_STATS." os WHERE o.orders_id=os.orders_id and o.customers_id=? and o.orders_status=?";
			$rs = $db->Execute($query, array($this->customers_id, $status));
		}

		if ($rs->RecordCount()>0) {
			$total = 0;
			while (!$rs->EOF) {
				$total+=$rs->fields['orders_stats_price']/$rs->fields['currency_value'];
				$rs->MoveNext();
			}$rs->Close();
			return $total;
		} else {
			return 0;
		}
	}

	/**
	 * query total count for given order status of customer, or count of all orders
	 *
	 * @param int $status
	 * @return int
	 */
	function _getTotalOrderCount($status = '') {
		global $db,$store_handler;

		if ($status == '') {
			$query = "SELECT count(*) as count FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_STATS." os WHERE o.orders_id=os.orders_id and o.customers_id=?";
			$rs = $db->Execute($query, array($this->customers_id));
		} else {
			$status = (int)$status;
			$query = "SELECT count(*) as count FROM ".TABLE_ORDERS." o, ".TABLE_ORDERS_STATS." os WHERE o.orders_id=os.orders_id and o.customers_id=? and o.orders_status=?";
			$rs = $db->Execute($query, array($this->customers_id, $status));
		}

		return $rs->fields['count'];
	}

	public function sendDsgvo($data)
    {
        $r = new stdClass();
        $r->success = true;
        try
        {
            $send_admin = false;
            if($data['send_admin'] === 'true') $send_admin = true;
            dsgvo::sendEmailCustomerData($data["customers_id"], $send_admin);
        }
        catch(Exception $e)
        {
            $r->msg = $e->getMessage();
        }
        return $r;
    }

	function setPosition ($position) {
		$this->position = $position;
	}

	function _getParams() {
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getParams_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(empty($this->url_data['get_data']) && empty($this->url_data['edit_id']))
		{
			unset($_SESSION['filters_customer']);
		}

		$params = array();

		$header['customers_id'] = array('type' => 'hidden');
		$header['password_type'] = array('type' => 'hidden');
        $header['customers_email_address'] = array('required' => true);
		$header['customers_password_old'] = array(
			'type' => 'hidden'
		);
		$header['date_added'] = array(
			'readonly' => true
		);
		$header['last_modified'] = array(
			'type' => 'hidden', 'readonly' => false
		);

		$header['customers_status'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=customers_status'
		);

		$header['customers_vat_id_status'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=status_truefalse'
		);

		$header['customers_admin_status'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=status_truefalse'
		);

		$header['shop_id'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=stores'
		);

		$header['campaign_id'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?systemstatus=campaign'
		);

		$header['customers_default_currency'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=currencies'
		);

		$header['customers_default_language'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=language_codes'
		);

		$header['customers_gender'] = array('renderer' => 'genderRenderer');

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getParams_header')) ? eval($plugin_code) : false;

        $rowActionsJavascript = '';

		$rowActions[] = array('iconCls' => 'address', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ADDRESS'));
        if ($this->url_data['edit_id'])
		{
			$js = "var edit_id = ".$this->url_data['edit_id'].";";
			$js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
		}
		else
		{
			$js = "var edit_id = record.id;";
			$js .= "var edit_name = /*record.data.customers_firstname + ' ' +*/ record.data.customers_lastname + ' ' + edit_id;";
		}
		$js .= "addTab('adminHandler.php?load_section=address&pg=overview&adID='+edit_id,'".__text('TEXT_ADDRESS')." '+edit_name,'customerAddresses_'+edit_id);";
		$rowActionsFunctions['address'] = $js;

		$rowActions[] = array('iconCls' => 'orders', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ORDERS'));
        if ($this->url_data['edit_id'])
		{
			$js = "var edit_id = ".$this->url_data['edit_id'].";";
			$js .= "var edit_name = " . $this->url_data['edit_id'] . ";";
		}
		else
		{
			$js = "var edit_id = record.id;";
			$js .= "var edit_name = /*record.data.customers_firstname + ' ' +*/ record.data.customers_lastname + ' ' + edit_id;";
		}
		$js .= "addTab('adminHandler.php?load_section=order&pg=overview&c_oID='+edit_id,'".__text('TEXT_ORDERS')." '+edit_name,'customerOrders_'+edit_id);";
		$rowActionsFunctions['orders'] = $js;

        $rowActions[] = array('iconCls' => 'dsgvo_send_admin', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_SEND_GDPA_REPORT'));
        if ($this->url_data['edit_id'])
        {
            $js = "var customers_id = ".$this->url_data['edit_id'].";";
        }
        else
        {
            $js = "var customers_id = record.id;";
        }
        $js .= "
        var mbbb = Ext.Msg.show({
        title: '".__text('TEXT_SEND_GDPA_REPORT')."',
        msg:      '<input type=\"checkbox\" id=\"send_admin\" /> ".__text('TEXT_DSGVO_SEND_ADMIN_ASK')."',
        buttons: Ext.Msg.YESNO,
        fn: function(btn, text){
            if (btn == 'yes'){
                var send_admin = false;
                if($('#send_admin').is(':checked')) send_admin = true;
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php?load_section=customer&pg=sendDsgvo&sec=' + csrf_key,
                    method: 'POST',
                    waitMsg: 'Wait',
                    params: {customers_id: customers_id, send_admin: send_admin},
                    error: function (responseObject) {
                        var jObj = JSON.parse(responseObject.responseText);
                        Ext.Msg.alert('Error', jObj.msg);
                    },
                    success: function (responseObject) {
                        var jObj = JSON.parse(responseObject.responseText);
                        if (jObj.success != true)
                        {
                             Ext.Msg.alert('Error', jObj.msg);
                        }
                        else {
                             Ext.Msg.alert('Info', 'Versand wurde gestartet');
                        }

                    }
                });
            }
        },
        icon: Ext.MessageBox.QUESTION
    });
        ";
        $rowActionsFunctions['dsgvo_send_admin'] = $js;


        $rowActions[] = array('iconCls' => 'delete_customer', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_DELETE'));

        $js = "var edit_id = record.id;";
        $js .= "Ext.Msg.show({title:'".__text('TEXT_DELETE_CUSTOMER')."',
				msg: '".__text('TEXT_ORDER_DELETE_TOO')."',
				buttons: Ext.Msg.YESNOCANCEL,
				animEl: 'elId',
				fn: function(btn){deleteCustomer_askDeleteOrder(edit_id,btn);},
				icon: Ext.MessageBox.QUESTION
				});";

        $rowActionsFunctions['delete_customer'] = $js;


        $rowActionsJavascript .= " 
        
        function deleteCustomer_askDeleteOrder(edit_id,btn)
        {
            if(btn == 'cancel') return;
            
            if(btn == 'yes')
            {
                Ext.Msg.show({title:'".__text('TEXT_DELETE_CUSTOMER')." - ".__text('TEXT_CUSTOMER_ORDERS')."',
                    msg: '".__text('TEXT_DELETE_CUSTOMER_ORDERS_ASK')."',
                    buttons: Ext.Msg.YESNOCANCEL,
                    animEl: 'elId',
                    fn: function(btn){deleteCustomer(edit_id, 1, btn);},
                    icon: Ext.MessageBox.QUESTION
                    });
			}
			else 
			{
			    deleteCustomer(edit_id, 0, 0)
			}
        }
        
        function deleteCustomer(edit_id, delete_order, refill_stock_btn)
		{
		    console.log('deleteCustomer',edit_id, delete_order, refill_stock_btn);
		    
		    if(refill_stock_btn == 'cancel') return;
		    
	  		var edit_id = edit_id;
	  		var fillup_stock = (refill_stock_btn == 'yes') ? 1 : 0;
	  		
	  		var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_DELETE')." ...'});
            lm.show();
	  	
	  		var conn = new Ext.data.Connection();
             conn.request({
             url: 'row_actions.php',
             method:'GET',
             params: {'customers_id': edit_id,'type': 'delete_customer','delete_order': delete_order, 'fillup_stock':fillup_stock},
             success: function(responseObject) {
                    var result = Ext.util.JSON.decode(responseObject.responseText);
                    var msg = '".__text('TEXT_SUCCESS')."';
                    if(typeof(result.msg) != 'undefined' && result.msg.length > 0)
                    {
                        msg = result.msg
                    }
                    lm.hide();
                    customerds.reload();
                    Ext.MessageBox.alert('Message', msg);
                 },
                 failure: function(a,b)
                 {
                    console.log(a,b);
                    Ext.MessageBox.alert('Error', a.statusText + '<br><br>Check Web Console / php logs');
                    lm.hide();
                 }
             });
		};";

        $js_multiDeleteButton =  "
        
        const l = customerds.getModifiedRecords().length;
        if(l == 0) return;
        
        Ext.Msg.show({title: '".__text('TEXT_DELETE_CUSTOMER')." (' + l + ')',
				msg: '".__text('TEXT_ORDER_DELETE_TOO')."',
				buttons: Ext.Msg.YESNOCANCEL,
				animEl: 'elId',
				fn: function(btn){multiDeleteCustomer_askDeleteOrder(btn);},
				icon: Ext.MessageBox.QUESTION
				});
				
		function multiDeleteCustomer_askDeleteOrder(btn)
        {
            if(btn == 'cancel') return;
            
            if(btn == 'yes')
            {
                Ext.Msg.show({title:'".__text('TEXT_DELETE_CUSTOMER')." - ".__text('TEXT_CUSTOMER_ORDERS')."',
                    msg: '".__text('TEXT_DELETE_CUSTOMER_ORDERS_ASK')."',
                    buttons: Ext.Msg.YESNOCANCEL,
                    animEl: 'elId',
                    fn: function(btn){multiDeleteCustomer(1, btn);},
                    icon: Ext.MessageBox.QUESTION
                    });
			}
			else 
			{
			    multiDeleteCustomer(0, 0);
			}
        }

        function multiDeleteCustomer(delete_order, refill_stock_btn)
        {
            console.log('multiDeleteCustomer', delete_order, refill_stock_btn);
            
            if(refill_stock_btn == 'cancel') return;
            
	  		var records = new Array();
            records = customerds.getModifiedRecords();
            var record_ids = [];
            for (var i = 0; i < records.length; i++) {
                if (records[i].get('selectedItem'))
                    record_ids.push( records[i].get('customers_id'));
            }
            if (record_ids.length == 0) return;
	  		
	  		var fillup_stock = (refill_stock_btn == 'yes') ? 1 : 0;
	  		
	  		var lm = new Ext.LoadMask(Ext.getBody(),{msg:'".__define('TEXT_DELETE')." ...'});
            lm.show();
	  		
	  		var conn = new Ext.data.Connection();
            conn.request({
                 url: 'adminHandler.php?load_section=customer&pg=overview&parentNode=node_customer&sec=".$_SESSION['admin_user']['admin_key']."',
                 method:'POST',
                 params: {
                    'multiFlag_unset': true,
                    'delete_order': delete_order,
                    'fillup_stock': fillup_stock,
                    'm_ids': record_ids.toString()
                 },
                 success: function(responseObject) {
                        var result = Ext.util.JSON.decode(responseObject.responseText);
                        var msg = '".__text('TEXT_SUCCESS')."';
                        if(typeof(result.msg) != 'undefined' && result.msg.length > 0)
                        {
                            msg = result.msg
                        }
                        lm.hide();
                 		customerds.reload();
                        Ext.MessageBox.alert('Message', msg);
                 },
                 failure: function(a,b)
                 {
                    console.log(a,b);
                    Ext.MessageBox.alert('Error', a.statusText + '<br><br>Check Web Console / php logs');
                    lm.hide();
                 }
            });
		};";


        $code = 'customer_multiDeleteButton';
        $multiDeleteButton = array('text' => 'BUTTON_DELETE', 'style'=>'delete', 'icon'=>'delete.png','font-icon'=>'fa fa-trash-alt', 'acl'=>'delete', 'stm' => $js_multiDeleteButton);
        $params['display_' . $code . 'Btn'] = true;
        $params['UserButtons'][$code] = $multiDeleteButton;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_row_actions')) ? eval($plugin_code) : false;

		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;
        $params['rowActionsJavascript']   = $rowActionsJavascript;

		$params['header']         = $header;
		$params['master_key']     = $this->master_id;
		$params['default_sort']   = $this->master_id;
        $params['RemoteSort']   = true;
 		$params['languageTab']    = false;
		$params['edit_masterkey'] = false;
		$params['display_checkItemsCheckbox']  = true;
		$params['display_checkCol']  = true;
        $params['display_deleteBtn'] = false;

		$pageSize = (int)_SYSTEM_ADMIN_PAGE_SIZE_CUSTOMER;
		if($pageSize && is_int($pageSize)) $params['PageSize'] = $pageSize;

		$params['display_searchPanel']  = true;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('customers_id','customers_status','customers_gender', 'customers_company', 'customers_email_address','customers_firstname', 'customers_lastname','shop_id');
		}else{
			$params['exclude'] = array('customers_parent_id', 'password_request_key', 'refferers_id',/* 'date_added', 'last_modified', */'account_type', 'external_id');
		}

		// open shop
		if(!$this->url_data['edit_id'] && $this->url_data['new'] != true){
            $adminUser = $_SESSION['admin_user'];
            $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
            if ($adminUser && $adminUser['user_id'])
            {
                global $language;
                $lang = $_SESSION['selected_language'] ? $_SESSION['selected_language'] : $language->default_language;

                $url_backend = _SRV_WEB.'adminHandler.php?openRemoteWindow=addProducts&plugin=order_edit&load_section=order_edit_new_order&pg=openNewOrderTabBackend&customers_id=';
                $js_backend  = "var customers_id = record.data.customers_id;\n";
                $js_backend .= "addTab('".$url_backend."' + customers_id,'".__text('TEXT_NEW_ORDER')." ' + record.data.customers_email_address);\n";
                $js_backend .= "var a = 0;\n";

                $js_frontend  = "var customers_id = record.data.customers_id;\n";
                $url_frontend = _SRV_WEB. "adminHandler.php?plugin=order_edit&load_section=order_edit_new_order".$add_to_url."&pg=openNewOrderWindowFrontend&customers_email=";
                $js_frontend .= "
                    //console.log(encodeURIComponent(record.data.customers_email_address));\n
                    window.open('".$url_frontend."'+encodeURIComponent(record.data.customers_email_address)+'&customers_id='+customers_id,'_blanko');\n";

                $rowActionsFunctions['NEW_ORDER'] = (_SYSTEM_ORDER_EDIT_NEW_ORDER_IN_FRONTEND === 'true')
                    ? $js_frontend
                    : $js_backend;

                $rowActions[] = array('iconCls' => 'NEW_ORDER', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_NEW_ORDER'));

                $params['rowActions']             = $rowActions;
                $params['rowActionsFunctions']    = $rowActionsFunctions;
            }
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getParams_bottom')) ? eval($plugin_code) : false;
		return $params;
	}

	function _getSearchIDs($search_data) {
		global $xtPlugin,$filter, $db;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getSearchIDs_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	    $customer_sql_tablecols = array('customers_email_address','customers_id','customers_cid');

	   	$address_sql_tablecols = array(
			'customers_company',
			'customers_firstname',
			'customers_lastname',
			'customers_street_address',
			'customers_postcode',
			'customers_city'
		);

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getSearchIDs_arrays')) ? eval($plugin_code) : false;

		// SEARCH IN CUSTOMERS
        foreach ($customer_sql_tablecols as $customer_tablecol) {
           $customer_sql_where[]= "(".$customer_tablecol." LIKE '%".$search_data."%')";
        }

		$customer_sql_data_array = "(".implode(' or ', $customer_sql_where).")";

		$rs = $db->Execute("SELECT customers_id FROM " . $this->_table . " where ".$customer_sql_data_array);
		if($rs->RecordCount() > 0){
			while (!$rs->EOF) {
				$customer_search_data[] = $rs->fields['customers_id'];
				$rs->MoveNext();
			}$rs->Close();
		}

	    // SEARCH IN ADDRESS BOOK

        foreach ($address_sql_tablecols as $address_tablecol) {
           $address_sql_where[]= "(".$address_tablecol." LIKE '%".$search_data."%')";
        }

		$address_sql_data_array = "(".implode(' or ', $address_sql_where).")";

		$record = $db->Execute("SELECT customers_id FROM " . $this->_table_add . " where ".$address_sql_data_array."");
		if($record->RecordCount() > 0){
			while (!$record->EOF) {
				$address_search_data[] = $record->fields['customers_id'];
				$record->MoveNext();
			}$record->Close();
		}

		$search_array = array();

		if(is_array($customer_search_data))
		$search_array = array_merge($search_array, $customer_search_data);

		if(is_array($address_search_data))
		$search_array = array_merge($search_array, $address_search_data);

		if(is_array($search_array))
            $search_array = array_unique($search_array);

		if(!is_array($search_array) || count($search_array)==0){
			$search_array[0] = '0';
		}

		$sql_where = TABLE_CUSTOMERS.".customers_id IN (".implode(',', $search_array).")";

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_getSearchIDs_bottom')) ? eval($plugin_code) : false;
		return $sql_where;
	}

	function _get($ID = 0,$searched='') {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

        ($plugin_code = $xtPlugin->PluginCode('class.customer.php:_get_top')) ? eval($plugin_code) : false;

		if ($ID === 'new') {
               $ID = $this->url_data['edit_id'];
		}
		if ($searched!='') $sql_where = TABLE_CUSTOMERS.'.customers_id IN ('.$searched.')';
		if($this->url_data['query']){
				$sql_where = $this->_getSearchIDs($this->url_data['query']);
		}

        $sort_col = $this->_master_key;
        if (!empty($this->url_data['sort']))
        {
            $data_read = new adminDB_DataRead(TABLE_CUSTOMERS, '', '', $this->_master_key);
            $fields = $data_read->getTableFields(TABLE_CUSTOMERS);

            if (isset($fields[$this->url_data['sort']]))
            {
                switch ($this->url_data['sort'])
                {
                    default:
                        $sort_col = $this->url_data['sort'];
                        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':_get_sort_qry')) ? eval($plugin_code) : false;
                }
            }
        }
        $sort_dir = (!empty($this->url_data['dir']) &&  $this->url_data['dir'] == 'ASC') ? 'ASC' : 'DESC';
        $sort_order =' ORDER BY '.TABLE_CUSTOMERS.'.'.$sort_col.' '.$sort_dir;

		$table_data = new adminDB_DataRead($this->_table, NULL, NULL, $this->_master_key, $sql_where, $this->sql_limit,'','', $sort_order);

		if ($this->url_data['get_data']){
			$data = $table_data->getData();

			if(is_array($data)){
				foreach ($data as $d_key=>$d_val){
					$_address_data = array();
					$_address_data = $this->_buildAddressData($d_val['customers_id'], 'default');

					if(is_array($_address_data))
					$data[$d_key] = array_merge($data[$d_key], $_address_data);
				}
			}

			$data_count = $table_data->_total_count;
		}elseif($ID){
			$data = $table_data->getData($ID);
			$data[0]['customers_password_old'] = $data[0]['customers_password'];
			$data[0]['customers_password'] = '';
		}else{
			$data = $table_data->getHeader();
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_get_bottom')) ? eval($plugin_code) : false;

		if($data_count!=0 || !$data_count)
		$count_data = $data_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db,$language,$filter, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_set_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if($data['customers_password']){
			$pw = new xt_password();
			$data['customers_password'] = $pw->hash_password($data['customers_password']);
			$data['password_type']='0';
		}else{
			$data['customers_password'] = $data['customers_password_old'];
		}

		 $obj = new stdClass;
		 $oC = new adminDB_DataSave(TABLE_CUSTOMERS, $data, false, __CLASS__);
		 $obj = $oC->saveDataSet();

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		return $obj;
	}

	function _unset($id = 0) {
	    global $db, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.customer.php:_unset_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        $id = (int) $id;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;

        $params = [
            'delete_order' => !empty($this->url_data["delete_order"]),
            'refill_stock' => !empty($this->url_data["fillup_stock"]),
        ];
        $this->deleteCustomer($id, $params);

	    ($plugin_code = $xtPlugin->PluginCode('class.customer.php:_unset_bottom')) ? eval($plugin_code) : false;

        return true;
	}

    function deleteCustomer($id, $params = [])
    {
        global $db, $xtPlugin;

        $id = (int) $id;
        if ($id == 0) return false;

        $db->Execute("DELETE FROM ". TABLE_CUSTOMERS_BASKET .   " WHERE ".$this->master_id." = ?", array($id));
        $db->Execute("DELETE FROM ". TABLE_CUSTOMERS_ADDRESSES ." WHERE ".$this->master_id." = ?", array($id));
        $db->Execute("DELETE FROM ". TABLE_CUSTOMERS .          " WHERE ".$this->master_id." = ?", array($id));

        if(array_key_exists('delete_order', $params) && $params["delete_order"])
        {
            $refill_stock = array_key_exists('refill_stock', $params) ? $params['refill_stock'] : false;
            $orders = $db->GetArray("SELECT orders_id FROM ".TABLE_ORDERS. " WHERE customers_id = ?", [$id]);
            foreach ($orders as $order)
            {
                static $order_obj;
                if(empty($order_obj))
                    $order_obj = new order();
                $order_obj->_deleteOrder($order['orders_id'], $refill_stock);
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('class.customer.php:deleteCustomer_bottom')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;
    }

	static function pageCustomerGetDobTplData($pos, $def_dob = 'now', $cust_id = 0)
	{
		global $db;

		$preset_min_age = 8;
		$preset_age = 21;

		$dob_preset = new DateTime();
		$dob_preset = $dob_preset->sub(new DateInterval('P'.$preset_age.'Y'));

		// _STORE_ACCOUNT_DOB_PRESET  represents the age
		$store_dob_preset = strtolower(trim(_STORE_ACCOUNT_DOB_PRESET));

		try
		{
			if ($pos == 'login')
			{
				$tpl_data['is_new'] = true;
				if ($store_dob_preset == 'auto')
				{
					global $db;
					$avg_year = $db->GetOne("select ROUND(AVG(YEAR(customers_dob))) from " . TABLE_CUSTOMERS_ADDRESSES . "
									where customers_dob is not null and customers_dob !='1970-01-01 00:00:00' and customers_dob !='0000-00-00 00:00:00'
								    and address_book_id > (SELECT max(address_book_id) from xt_customers_addresses)-100");
					$dob_preset = new DateTime($avg_year);
				}
				else
				{
					if (!empty($store_dob_preset))
					{
						$dob_preset = new DateTime();
						$dob_preset = $dob_preset->sub(new DateInterval('P'.(int)$store_dob_preset.'Y'));
					}
				}
			}
			else if ($pos == 'edit_address_edit')
			{
				if (!empty($def_dob))
				{
					$dob_preset = new DateTime($def_dob);
				}
				else
				{
					if ($store_dob_preset == 'auto')
					{
						global $db;
						$avg_year = $db->GetOne("select ROUND(AVG(YEAR(customers_dob))) from " . TABLE_CUSTOMERS_ADDRESSES . "
									where customers_dob is not null and customers_dob !='1970-01-01 00:00:00' and customers_dob !='0000-00-00 00:00:00'
								    and address_book_id > (SELECT max(address_book_id) from xt_customers_addresses)-100");
						$dob_preset = new DateTime($avg_year);
					}
					else
					{
						if (!empty($store_dob_preset))
						{
							$dob_preset = new DateTime();
							$dob_preset = $dob_preset->sub(new DateInterval('P' . (int)$store_dob_preset . 'Y'));
						}
					}
				}
			}
			else if ($pos == 'edit_address_add')
			{
				$tpl_data['is_new'] = true;
				$dob = $db->GetOne("select customers_dob from " . TABLE_CUSTOMERS_ADDRESSES . "
									where customers_dob is not null and customers_dob !='1970-01-01 00:00:00' and customers_dob !='0000-00-00 00:00:00'
									and customers_id = ? order by address_book_id desc LIMIT 1", array($cust_id));
				if ($dob)
				{
					$dob_preset = new DateTime($dob);
				}
			}

		}
		catch (Exception $e) {}

		$min_year = new DateTime();
		$min_year = $min_year->sub(new DateInterval('P'.$preset_min_age.'Y'));
		$tpl_data['max_date'] = $min_year->format('Y,n,j');

		$now = new DateTime();
		$now120before = $now->sub(new DateInterval('P120Y'));
		$tpl_data['min_date'] = $now120before->format('Y,n,j');

		if($dob_preset->format('Y') > $min_year->format('Y'))
		{
			$dob_preset = $min_year;
		}
		$tpl_data['dobPreselect'] = $dob_preset->format("Y,n-1,j");

		return $tpl_data;
	}

	static function pageCustomerGetSalutationTplData($cust_id)
	{
		global $db;

		$store_salutation_preset = strtolower(trim(_STORE_ACCOUNT_SALUTATION_PRESET));

		if ($store_salutation_preset == 'auto')
		{
			if($cust_id)
			{
				$salutations_db = $db->GetArray("SELECT  customers_gender as g FROM " . TABLE_CUSTOMERS_ADDRESSES . "
					where customers_id = ?
					group by customers_gender
					order by count(customers_gender) desc", array($_SESSION['customer']->customers_id ? $_SESSION['customer']->customers_id : 0));
				// use of indexed customers_id in where clause keeps the query execution time constantly small
				// its possible we get empty values too
				if(is_array($salutations_db))
				{
					foreach($salutations_db as $sal)
					{
						$salutations[] = $sal['g'];
					}
				}
			}
			else
			{
				$salutations = $db->GetArray("SELECT  customers_gender as g FROM ".TABLE_CUSTOMERS_ADDRESSES."
					where address_book_id > (SELECT max(address_book_id) from xt_customers_addresses)-100
					group by customers_gender
					order by count(customers_gender) desc");
				// use of indexed address_book_id in where clause keeps the query execution time constantly small
				// its possible we get empty values too
			}
		}
		else {
			$salutations = array_map('trim', explode(',', $store_salutation_preset));
		}

		$supported_salutations = array('f', 'm', 'c', 'n', 'z');
		$salutations_order = array();
		foreach($salutations as $gd)
		{
			if (in_array($gd, $supported_salutations))
			{
				$salutations_order[] = $gd;
			}
		}
		$salutations_order = array_unique(array_merge($salutations_order, $supported_salutations));
		if (count($salutations_order))
		{
			$gender_data = array();
			foreach ($salutations_order as $s)
			{
				if ($s == 'c' && _STORE_ACCOUNT_COMPANY != 'true')
				{
					continue;
				}
				$skip = false;
				switch ($s)
				{
					case 'f':
						$text = __text('TEXT_FEMALE');
						break;
					case 'm':
						$text = __text('TEXT_MALE');
						break;
					case 'c':
						$text = __text('TEXT_COMPANY_GENDER');
						break;
                    case 'n':
                        $text = __text('TEXT_NEUTRAL_SALUTATION') ;
                        break;
                    case 'z':
                        $text = __text('TEXT_NOT_SPECIFIED') ;
                        break;
					default:
						$skip = true;
				}
				if($skip) continue;
				$gender_data[] = array('id' => $s, 'text' => $text);
			}
		}
		else {
			$gender_data = array(array('id'=>'m', 'text'=>__text('TEXT_MALE')), array('id'=>'f', 'text'=>__text('TEXT_FEMALE')), array('id'=>'n', 'text'=>__text('TEXT_NEUTRAL_SALUTATION')),array('id'=>'z', 'text'=>__text('TEXT_NOT_SPECIFIED')));
			if (_STORE_ACCOUNT_COMPANY=='true') $gender_data = array_merge($gender_data,array(array('id'=>'c','text'=>__text('TEXT_COMPANY_GENDER'))));
		}

		return $gender_data;
	}

    static function pageCustomerGetTitleTplData()
    {
        $title_preset = array_map('trim', explode(PHP_EOL,_STORE_ACCOUNT_TITLE_PRESET));

        $preselect = !empty($title_preset[0]);

        $title_preset = array_filter($title_preset);

        foreach($title_preset as &$v)
        {
            $v = array('id' => $v, 'text' => $v);
        }
        if (!$preselect && count($title_preset))
            array_unshift($title_preset, array('id' => '', 'text' => ' '));
        return $title_preset;

    }

}
