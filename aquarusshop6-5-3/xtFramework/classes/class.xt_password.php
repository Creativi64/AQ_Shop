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

class xt_password {
	
	var $default_cost = 10;
	var $password_min_length = '10';
	var $password_contain_number = true;
	var $password_contain_special_char = true;
	
	
	/**
	 * verify user password, regenerate if password is insecure (md5, etc)
	 * @param unknown $pw_plain
	 * @param unknown $pw_hash
	 * @param unknown $userid
	 * @param unknown $pw_type
	 * @return boolean
	 */
	public function verify_password($pw_plain, $pw_hash, $userid, $pw_type, $login_as_admin = false) {
		global $xtPlugin, $db;
		
		($plugin_code = $xtPlugin->PluginCode('check_pw.php:_checkPW_top')) ? eval($plugin_code) : false;
		if (isset($plugin_return_value))
		{
		    return $plugin_return_value;
		}
		
		if ($login_as_admin) {
			$sql = "SELECT `user_password` FROM `" . TABLE_ADMIN_ACL_AREA_USER . "` WHERE `user_id`=?";
			$pw_hash = $db->GetOne( $sql, array((int)$_SESSION ['orderEditAdminUser'] ['user_id']));
		}
		
		if ($pw_type == 1 && !$login_as_admin) { // old md5 password type, regenerate secure password
			if ($pw_hash === md5 ( $pw_plain )) {
				$newHash = $this->hash_password ( $pw_plain );
				$db->Execute ( "UPDATE " . TABLE_CUSTOMERS . " SET password_request_key='',password_type=0,customers_password=? WHERE customers_id=?", array (
						$newHash,
						$userid 
				) );
				return true;
			} else {
				return false;
			}
		} else {
			if (password_verify ( $pw_plain, $pw_hash )) {
				if (password_needs_rehash ( $pw_hash, PASSWORD_DEFAULT,["cost" => $this->default_cost] )) {
					$newHash = $this->hash_password ( $pw_plain );
					$db->Execute ( "UPDATE " . TABLE_CUSTOMERS . " SET password_request_key='',password_type=0,customers_password=? WHERE customers_id=?", array (
							$newHash,
							$userid 
					) );
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * hash password
	 * @param string $password
	 * @return string
	 */
	public function hash_password($password) {
		return password_hash($password,PASSWORD_DEFAULT,["cost" => $this->default_cost]);
	}
	
	/**
	 * verify admin password
	 * @param unknown $pw_plain
	 * @param unknown $pw_hash
	 * @param unknown $userid
	 * @return boolean
	 */
	public function verify_admin_password($pw_plain, $pw_hash, $userid) {
		global $db;

		$isOldMd5 = !strpos($pw_hash,'$') && $pw_hash == md5($pw_plain);
		if (password_verify ( $pw_plain, $pw_hash ) || $isOldMd5) {
			if (password_needs_rehash ( $pw_hash, PASSWORD_DEFAULT,["cost" => $this->default_cost] )) {
				$newHash = $this->hash_password ( $pw_plain );
				$db->Execute ( "UPDATE " . TABLE_ADMIN_ACL_AREA_USER . " SET password_request_key='',user_password=? WHERE user_id=?", array (
					$newHash,
					$userid
				) );
			}
			return true;
		} else {
			return false;
		}
	}


    /**
     * verify password security
     * @param $pw_plain
     * @return array|bool
     */
	public function verify_password_security($pw_plain) {

        $errors = [];

	    if (strlen($pw_plain) < $this->password_min_length ) {
            $errors[] = __text('ERROR_PASSWORD_TOO_SHORT');
        }

        if ($this->password_contain_number) {
            if (!preg_match("#[0-9]+#", $pw_plain)) {
                $errors[] = __text('ERROR_PASSWORD_NO_NUMBER');
            }
        }

        if (!preg_match("#[a-z]+#", $pw_plain)) {
            $errors[] = __text('ERROR_PASSWORD_NO_LOWERCASE_LETTER');
        }

        if (!preg_match("#[A-Z]+#", $pw_plain)) {
            $errors[] = __text('ERROR_PASSWORD_NO_UPPERERCASE_LETTER');
        }

        if ($this->password_contain_special_char) {
            if (!preg_match("#\W+#", $pw_plain)) {
                $errors[] = __text('ERROR_PASSWORD_NO_SYMBOL');
            }
        }

        if (count($errors)==0) return true;

        return $errors;

    }
	
}