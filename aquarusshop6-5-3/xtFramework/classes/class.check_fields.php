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

class check_fields{

    //const REGEX_EMAIL = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
    const   REGEX_EMAIL = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){90,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

        /**
     * check title for empty or not allowed values
     *
     * @param char $data
     */
    function _checkTitle($title)
    {
        global $xtPlugin, $info;

        ($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkTitle_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        if (_STORE_ACCOUNT_TITLE_REQUIRED && empty($title))
        {
            $this->error = true;
            $info->_addInfo(__text('TEXT_TITLE_MISSING'));
        }
        else if (!empty($title))
        {
            $title_preset = array_filter(array_map('trim', explode("\n",_STORE_ACCOUNT_TITLE_PRESET)));

            $allow_arbitrary = empty($title_preset[0]);

            if (!$allow_arbitrary && count($title_preset) && !in_array($title, $title_preset))
            {
                $this->error = true;
                $info->_addInfo(__text('TEXT_TITLE_NOT_ALLOWED'));
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkTitle_bottom')) ? eval($plugin_code) : false;
    }

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

        if (!in_array($data,['m', 'f', 'c', 'n', 'z'])) {
			$this->error = true;
			$info->_addInfo(__text('TEXT_GENDER_ERROR'));
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
	function _checkLenght($data, $lenght, $error_message){
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkLenght_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$data=trim($data);
		
		if ((strlen($data) < $lenght) && $lenght!=0) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}
	
    
    /**
    * validate email address by regex expression
    * 
    * @param string $data email address
    * @param string $error_message
    */
    function _checkEmailAddress($data, $error_message){
        global $xtPlugin, $info;
        
        ($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkEmailAddress_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;

        if (filter_var($data, FILTER_VALIDATE_EMAIL) != $data) {
            $pattern = self::REGEX_EMAIL;
            $result = preg_match ($pattern, $data);
            if (!$result) {
                $this->error = true;
                $info->_addInfo($error_message);
            }
        }
    }

    function _checkMinAge($dob, $dob_format = false, $minAge = false){
        global $xtPlugin, $info;


        ($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkMinAge_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        if(empty($dob_format))
        {
            $dob_format = constant('_STORE_ACCOUNT_DOB_FORMAT');
            $dob_format = str_replace('yyyy', 'Y', $dob_format);
            $dob_format = str_replace('mm', 'm', $dob_format);
            $dob_format = str_replace('dd', 'd', $dob_format);
        }

        if($minAge == false)
        {
            $minAge = preg_replace('/[^0-9]/', '', constant('_STORE_ACCOUNT_MIN_AGE'));
            $minAge = (int)$minAge;
        }
        if($minAge)
        {
            try{
                $dt_dob = DateTime::createFromFormat($dob_format, $dob);
                if($dt_dob)
                {
                    $dt_minAge = $dt_dob->add(new DateInterval('P' . $minAge . 'Y'));
                    $dt_now = new DateTime();
                    if ($dt_now >= $dt_minAge)
                    {
                        ;//ok
                    }
                    else
                    {
                        $this->error = true;
                        $info->_addInfo(sprintf(__text('ERROR_MIN_AGE'), $minAge));
                    }
                }
            }
            catch(Exception $e)
            {
                $this->error = true;
                $info->_addInfo($e->getMessage());
            }
        }
    }
            
	function _checkDefaultAddress($cID, $error_message){
		global $db,$info;
		$record = $db->Execute(
			"SELECT address_book_id FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=? and address_class='default'",
			array($cID)
		);

		if($record->RecordCount() <2 ){
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}
	
	function _checkCurrentPassword($pw_plain,$cID,$error_message) {
		global $xtPlugin, $info,$db;
	
		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkCurrentPassword_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
			return $plugin_return_value;
	
		$pw = new xt_password();
		
		$cID = (int)$cID;
		if (!is_int($cID)) return false;
	
		$rs = $db->Execute(
				"SELECT customers_password,password_type FROM ".TABLE_CUSTOMERS." WHERE customers_id=?",
				array($cID)
		);
		
		if ($rs->RecordCount()==1) {

			$pw_check = $pw->verify_password($pw_plain, $rs->fields['customers_password'],$cID,$rs->fields['password_type']);			
			if($pw_check != true) {
				$this->error = true;
				$info->_addInfo($error_message);
			}
			
			
		} else {
			$this->error = true;
			$info->_addInfo($error_message);
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
     * @param bool $ignore_case
     * @return mixed
     */
	function _checkMatch($data, $data_match, $error_message, $ignore_case = false){
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkMatch_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if($ignore_case)
        {
            if(is_string($data)) $data = strtolower($data);
            if(is_string($data_match)) $data_match = strtolower($data_match);
        }

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

		$record = $db->Execute(
			"SELECT ".$field." FROM " . $table . " where ".$field."=? and ".$params."",
			array($data)
		);

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
		if (!(_STORE_VAT_CHECK_TYPE == 'format' || _STORE_VAT_CHECK_TYPE == 'simple' || _STORE_VAT_CHECK_TYPE == 'complex' || _STORE_VAT_CHECK_TYPE == 'live')) return false;;
		
		$vat_id = new vat_id();
		
		try {
			$check = $vat_id->_check($data['cust_info']['customers_vat_id'],$data['default_address']['customers_country_code']);

			if ($return_val==true) return $check;

			if ($check!=true) {
				$this->error = true;
				if ($check==-99) {
					$info->_addInfo(__text('ERROR_VAT_ID_SERVICE'));
				} else {
					$info->_addInfo($error_message);
				}

			}

		} catch (Exception $e) {
			$this->error = true;
			$info->_addInfo(__text('ERROR_VAT_ID_SERVICE'). ' ['.$e->getMessage().']');
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


	/**
	 * 
	 * checks if user input is a valid date (birthday)
	 * 
	 * @param string $data date to be checked
	 * @param string $format_str format date should have
	 * @param string $error_message message that is shown to the user in case of an error
	 */
	function _checkDate($data, $format_str, $error_message) {
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkDate_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
		
		$isDate_bol = true;
		
		// check length
		if (strlen($data) != strlen($format_str)) $isDate_bol = false;
				
		// checking for delimiter
		if (strpos($format_str, '.') !== false) {
			$glue_str = '.';
		}
		elseif (strpos($format_str, '-') !== false) {
			$glue_str = '-';
		}
		elseif (strpos($format_str, '/') !== false) {
			$glue_str = '/';
		}
		elseif (strpos($format_str, ' ') !== false) {
			$glue_str = ' ';
		}
		else {
			$isDate_bol = false;
		}
		
		if ($isDate_bol) {
			// finding delimiter positions
			$firstPos_num = strpos($format_str, $glue_str);
			$lastPos_num = strrpos($format_str, $glue_str);
			
			$subLength1_num = $firstPos_num;
			$subLength2_num = $lastPos_num - $firstPos_num - 1;
			
			$startPos2_num = $firstPos_num + 1;
			$startPos3_num = $lastPos_num + 1;
			
			// checking date parts
			if (!is_numeric(substr($data,0,$subLength1_num))) $isDate_bol = false;
			if (!is_numeric(substr($data,$startPos2_num,$subLength2_num))) $isDate_bol = false;
			if (!is_numeric(substr($data,$startPos3_num))) $isDate_bol = false;
			if (substr($data,$firstPos_num,1) != $glue_str) $isDate_bol = false;
			if (substr($data,$lastPos_num,1) != $glue_str) $isDate_bol = false;
		}
		
		// checking validity
		if ($isDate_bol) {
			if ($format_str == 'dd.mm.yyyy') {
				$day_num = (int) substr($data,0,$subLength1_num);
				$month_num = (int) substr($data,$startPos2_num,$subLength2_num);
				$year_num = (int) substr($data,$startPos3_num);
				
				if (!checkdate($month_num, $day_num, $year_num)) $isDate_bol = false;
			}
			elseif ($format_str == 'yyyy-mm-dd') {
				$year_num = (int) substr($data,0,$subLength1_num);
				$month_num = (int) substr($data,$startPos2_num,$subLength2_num);
				$day_num = (int) substr($data,$startPos3_num);
				
				if (!checkdate($month_num, $day_num, $year_num)) $isDate_bol = false;
			}
			elseif ($format_str == 'mm/dd/yyyy') {
				$month_num = (int) substr($data,0,$subLength1_num);
				$day_num = (int) substr($data,$startPos2_num,$subLength2_num);
				$year_num = (int) substr($data,$startPos3_num);
				
				if (!checkdate($month_num, $day_num, $year_num)) $isDate_bol = false;
			}
			
			($plugin_code = $xtPlugin->PluginCode('class.customer_check.php:_checkDate_validity')) ? eval($plugin_code) : false;
		}
		
		if (!$isDate_bol) {
			$this->error = true;
			$info->_addInfo($error_message);
		}
	}
}