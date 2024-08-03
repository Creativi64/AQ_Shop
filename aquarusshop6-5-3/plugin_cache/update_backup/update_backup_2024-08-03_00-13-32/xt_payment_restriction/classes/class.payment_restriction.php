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

class payment_restriction extends default_table {
	
	protected $_table = TABLE_PAYMENT_RESTRICTION;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'id';
	
	function _getParams()
	{
		// fix refresh tab function
		if($this->url_data['pg']=='overview' && empty($_REQUEST['parentNode']))
		{
			$_REQUEST['parentNode'] = 'paymentRestrictions_'.$this->url_data['customers_status_id'];
		}

		$params = array();
		
		$header['customers_status_id'] = array('type' => 'hidden');

		$header['payment_id'] = array('type' => 'dropdown','url'  => 'DropdownData.php?get=payment_methods');
		$header['order_status'] = array('type' => 'dropdown','url'  => 'DropdownData.php?systemstatus=order_status');
		
		$header['restriction_class'] = array('type' => 'dropdown','url'  => 'DropdownData.php?get=payment_restriction_class&plugin_code=xt_payment_restriction');
		

		$params['header']         = $header;
		$params['display_searchPanel']  = true;
		$params['master_key']     = $this->_master_key;

		return $params;
	}
	
	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}			
		
		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'customers_status_id='.(int)$this->url_data['customers_status_id'], $this->sql_limit);

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);
		}else{
			$data = $table_data->getHeader();
		}

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

        $obj = new stdClass();
		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type='edit'){
		global $db,$language,$filter;

		if($set_type=='new'){
			$data['customers_status_id'] = (int)$this->url_data['customers_status_id'];
		}


		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();

		$obj->success = true;

		return $obj;
	}
	
}
