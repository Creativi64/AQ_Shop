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

class xt_license {
	
	public static function verify($licenseParams, $pluginCode, $errorText = "- license not matching, contact support -") {
		global $logHandler;
		
		if (isset($licenseParams['LICENSEKEY']['value'])) {
		
			$_lic = _SRV_WEBROOT . 'lic/license.txt';
			
			if (!file_exists($_lic)) {
				$log_data= array();
				$log_data['error_msg'] = "- main lic missing -";
				$log_data['time'] = time();
				$logHandler->_addLog('error', $pluginCode, 0, $log_data);
				return $this->disablePlugin($pluginCode);
			}
			
			$val_line = '';
			$bline = '';
			$_file_content = file($_lic);
			
			foreach ($_file_content as $bline_num => $bline) {
				if (strpos($bline, 'key:', 0) !== FALSE) {
					$val_line = $bline;
					break;
				}
			}
		
			$val_line = explode(':', $val_line);
			$_shop_lic = '';
			$_shop_lic = trim($val_line[1]);
			unset($val_line);
			unset($_lic);
		
			if ($_shop_lic != md5($lic_parms['LICENSEKEY']['value'])) {
				$log_data= array();
				$log_data['error_msg'] = $errorText;
				$log_data['time'] = time();
				$logHandler->_addLog('error', $pluginCode, 0, $log_data);
				return $this->disablePlugin($pluginCode);
			}
		}
		
		return true;
	}
	
	public function disablePlugin($pluginCode) {
		require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.plugin_installed.php';
		$pluginManager = new plugin_installed();
		
		if (($id = $pluginManager->checkInstall($pluginCode))) {
			$pluginManager->_setStatus($id, 0);
		}
		
		return false;
	}
	
}