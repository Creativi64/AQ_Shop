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

class order_status extends system_status{

	protected $_master_status = 'order_status';

	function _defineFields(){
		$this->status_fields_array = "a:1:{s:4:\"data\";a:5:{s:15:\"enable_download\";i:0;s:7:\"visible\";s:1:\"0\";s:13:\"visible_admin\";i:0;s:19:\"calculate_statistic\";i:1;s:12:\"reduce_stock\";i:0;}}";
	}

	function _getParams() {
		global $language;

		$params = array();

		foreach ($language->_getLanguageList() as $key => $val) {
			$header['status_name_'.$val['code']] = array('type' => '');
			$header['status_image_'.$val['code']] = array('type' => '');
			$header['language_code_'.$val['code']] = array('type' => 'hidden');
		}

		$header['enable_download'] = array('type' => 'status');
		$header['visible'] = array('type' => 'status');
		$header['visible_admin'] = array('type' => 'status');
        $header['calculate_statistic'] = array('type' => 'status'); 
        $header['reduce_stock'] = array('type' => 'hidden'); 

		$header['status_class'] = array('type' => 'hidden');
		$header['status_id'] = array('type' => 'hidden');

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;

		$params['exclude']        = array ('');

		if(!$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include']   = array ('status_id', 'status_class', 'status_name_'.$language->code);
		}

		return $params;
	}
}