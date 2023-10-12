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

class subpage{

	var $loaded_subpage;

	function __construct($data=''){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.subpage_handler.php:subpage_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if(empty($data)){
			return false;
		}

		$this->loaded_subpage = $this->_getsubpage($data);
		($plugin_code = $xtPlugin->PluginCode('class.subpage_handler.php:subpage_bottom')) ? eval($plugin_code) : false;
	}

	function _getsubpage($data){
		global $xtPlugin;

		if(empty($data['type'])){
			$type = 'core';
		}else{
			$type = $data['type'];
		}
        
        //check status of plugin
        if(!array_key_exists($data['name'],$xtPlugin->active_modules) && $data['type'] == 'user'){
            return false;            
        }

		($plugin_code = $xtPlugin->PluginCode('class.subpage_handler.php:_getsubpage_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$name = $data['name'].'.php';

		if(!empty($data['name']))
		$plugin_name = $data['name'].'/';


		if($type=='core'){
			$path = _SRV_WEBROOT._SRV_WEB_CORE.'pages/';
		}elseif($type=='user'){
			$path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$plugin_name.'pages/';
		}else{
			$path = $type;
		}

		if (!file_exists($path . $name)) {
			return false;
		}else{
			return $path.$name;
		}
	}
}