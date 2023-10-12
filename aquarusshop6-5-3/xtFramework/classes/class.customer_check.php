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

class customer_check{

	function __construct(){


	}

	function _checkGender($data){
		global $db, $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkGender_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

        if (!in_array($data,['m', 'f', 'c', 'n', 'z'])) {
			$this->error = true;
			$info->_addInfo(__text('TEXT_GENDER_ERROR'));
		}

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkGender_bottom')) ? eval($plugin_code) : false;
	}

	function _checkLenght($data, $lenght, $error_message){
		global $db, $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkLenght_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if (strlen($data) < $lenght) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}

	function _checkNum($data, $error_message){
		global $db, $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkNum_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if (is_numeric($data) == false) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}

	function _checkMatch($data, $data_match, $error_message){
		global $db, $xtPlugin, $info;

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

		$record = $db->Execute("SELECT ".$field." FROM " . $table . " where ".$field."=? ".$params."", array($data));
			if($record->RecordCount() > 0){
				$this->error = true;
				$info->_addInfo($error_message);
			}
	}

}