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

class logo {

	public $_master_key = 'config_key';
	public $_image_key = 'config_key';
	public $_table = TABLE_CONFIGURATION_MULTI;
	public $_store_id = '1';

	public function __construct()
	{
		global $xtPlugin;

		$this->_table=TABLE_CONFIGURATION_MULTI.'1';
	}
	
	public function setPosition($position)
	{
		$this->position = $position;
	}
	
	public function setStoreId($_store_id)
	{
		$this->_store_id = $_store_id;
	}
	
	 function _setImage($file)
	{
		global $xtPlugin,$db,$language;
		
		$old_file = _SRV_WEBROOT._SRV_WEB_IMAGES._DIR_ORG.$file;
		$newfile = _SRV_WEBROOT.'media/logo/'.$file;
		copy($old_file, $newfile);
		
		$sql = "update ".TABLE_CONFIGURATION_MULTI.$_SESSION['logo_store_id']." set config_value = ".$db->Quote($file)." where config_key = '_STORE_LOGO'";
		
		 $res = $db->execute($sql);
	      if ($db->ErrorNo == 0) {
	        $erg = true;   
	      } else {
	        $erg = false;   
	      }

		return $erg;
	}
}