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

include_once _SRV_WEBROOT.'xtFramework/classes/class.vat_id.php';

/**
 * Klasse kapselt häufig genutzte Validierungsfunktionen
 * Diese Funktionen werden noch kaum genutzt
 */
class check_input{


	/**
	 * check gender value, f m c allowed
	 *
	 * @param char $data
	 */
	function _checkGender($data){
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkGender_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if (($data != 'm') && ($data != 'f') && ($data != 'c') ) {
			$this->error = true;
			$info->_addInfo(TEXT_GENDER_ERROR);
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkGender_bottom')) ? eval($plugin_code) : false;
	}

	/**
	 * validate input by length
	 *
	 * @param string $data
	 * @param int $lenght
	 * @param string $error_message
	 */
	function _checkLenght($data, $lenght){
		global $xtPlugin, $info;
		
		if ((strlen($data) < $lenght) && $lenght!=0) {
			return false;
		}
		return true;
	}

        /**
         * Customer Passwort prüfen
         * @global type $xtPlugin
         * @global type $info
         * @global type $db
         * @param type $data Klartext-Passwort vom Kunden
         * @param type $cID
         * @param type $error_message
         * @return boolean
         */
	function _checkCurrentPassword($data,$cID,$error_message) {
		global $xtPlugin, $info,$db;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkCurrentPassword_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

                
                // TODO: 2017-02-06 NEUE PW-HASH Implementieren. Bzw checkCurrentPasswort raus...
                // Passwort zu MD5 hashen
		$password = md5($data);
		
                // CUstomer ID zu int casten
                $cID = (int)$cID;
		
                // Customer ID war keine Zahl = rausgehen
                if (!is_int($cID)) return false;

                // DB Abfrage auf Tabelle TABLE_CUSTOMERS
		$rs = $db->Execute("SELECT * FROM ".TABLE_CUSTOMERS." WHERE customers_id='".$cID."' and customers_password='".$password."'");
		if ($rs->RecordCount()!=1) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
                // Garcia: hinzugefügt
                else{
                    // PAasswort ist OK
                    return true;
                }
	}

	function _checkNum($data, $error_message){
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkNum_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if (is_numeric($data) == false) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}

	/**
	 * validate by match
	 *
	 * @param string $data
	 * @param string $data_match
	 * @param string $error_message
	 */
	function _checkMatch($data, $data_match, $error_message){
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkMatch_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if ($data != $data_match) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}

	function _checkExist($data, $field, $table, $params, $error_message){
		global $db, $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkExist_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$record = $db->Execute("SELECT ".$field." FROM " . $table . " where ".$field."='".$data."' and ".$params."");
			if($record->RecordCount() > 0){
				$this->error = true;
				$info->_addInfo($error_message);
			}
	}

	function _checkState($data, $error_message){
		global $xtPlugin, $info;

		if(ACCOUNT_STATE == 'true' && empty($data)){
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}

	function _checkVatId($data,$error_message,$return_val = false) {
		global $xtPlugin, $info;

		if (!isset($data['cust_info']['customers_vat_id']) or $data['cust_info']['customers_vat_id']=='') return false;
		if (_STORE_VAT_CHECK_STATUS=='false') return false;

		$vat_id = new vat_id();
		try {
			$check = $vat_id->_check($data['cust_info']['customers_vat_id'],$data['default_address']['customers_country_code']);

			if ($return_val==true) return $check;

			if ($check!=true && _STORE_VAT_CHECK_BLOCK=='true') {
				$this->error = true;
				if ($check==-99) {
					$info->_addInfo(ERROR_VAT_ID_SERVICE);
				} else {
					$info->_addInfo($error_message);
				}

			}

		} catch (Exception $e) {
			$this->error = true;
			$info->_addInfo(ERROR_VAT_ID_SERVICE);
			if ($return_val==true) return -3;
			// TODO add service error to error log
		}
	}

	function _checkDefaultAddressClass($data, $old_data){
		global $xtPlugin, $db, $info;

		if($old_data == 'default' && $data != 'default'){
			return $old_data;
		}else{
			return $data;
		}

	}

}
?>