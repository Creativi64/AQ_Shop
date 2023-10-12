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

class plugin_hookpoints extends plugin{

	protected $_table = TABLE_PLUGIN_CODE;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'id';

	var $master_id = 'id';

	function _getParams() {
		global $language;

		$params = array();

		$header['code'] = array('type' => 'textarea','height'=>'350','width'=>'550');
		$header['id'] = array('type' => 'hidden');
		$header['plugin_id'] = array('type' => 'hidden');
		$header['plugin_code'] = array('type' => 'hidden');
		$header['plugin_status'] = array('type' => 'hidden');

		$header['active'] = array(
			'type' => 'dropdown', 								// you can modyfy the auto type
			'url'  => 'DropdownData.php?get=status_truefalse'
		);

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;

		$params['PageSize'] = 50;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['exclude'] = array ('code','plugin_status');
		}

		return $params;
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

        $obj = new stdClass;

		if ($this->position != 'admin') return false;
        
        if (_SYSTEM_DEMO_MODE=='true') return false;

		if ($ID === 'new') {
               $obj = $this->_set(array(), 'new');
               $ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, 'plugin_id='.(int)$this->url_data['pHID']);
		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);
			$data[0]['code'] = $data[0]['code'];
		}else{
			$data = $table_data->getHeader();
		}

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db, $language, $filter, $xtPlugin;
		 if($set_type=='new')
		 $data['plugin_id'] = (int)$this->url_data['pHID'];

         $plugin_code =$xtPlugin->_getPluginCode($data['plugin_id']);
         $data['plugin_code'] = $plugin_code;
         $data['hook'] = trim($data['hook']);

         if($data['sortorder'])
         $sort = $data['sortorder'];
         else
         $sort = '0';

         $data['sortorder'] = $sort;

		 $data['plugin_status'] = $db->GetOne("SELECT plugin_status FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_id=?", array($data['plugin_id'])) ? 1 : 0;

		 $obj = new stdClass;
		 $oH = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		 $obj = $oH->saveDataSet();

		$xtPlugin->_checkHookDir(trim($data['hook']));
		$xtPlugin->_deleteHookFiles(trim($data['id']));
		$filename = $sort.'_'.$plugin_code;

		if($data['code_status']=='1'){
			$xtPlugin->_createHookFile(trim($data['hook']), $data['code'], $filename);
		}else{
			$xtPlugin->_deleteHookFile(trim($data['hook']), $filename);
		}

		return $obj;
	}

	function _unset($id = 0) {
	    global $db, $xtPlugin;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if(!is_int($id)) return false;

		$xtPlugin->_deleteHookFiles($id);

	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));

	}

	function _setStatus($id, $status) {
		global $db,$xtPlugin;

		$id = (int)$id;
		if (!is_int($id)) return false;
		
		if(!_SYSTEM_USE_DB_HOOKS){
			if($status != 1){
				$xtPlugin->_deleteHookFiles(trim($id));
			}else{
				$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, 'plugin_id='.(int)$this->url_data['pHID']);
				$data = $table_data->getData($id);
				$data = $data[0];
				
	         	if($data['sortorder'])
	         	$sort = $data['sortorder'];
	         	else
	         	$sort = '0';			
				
	        	$plugin_code =$xtPlugin->_getPluginCode($data['plugin_id']);
	         	$data['plugin_code'] = $plugin_code;
	         	$data['hook'] = trim($data['hook']);			
	         
	         	$xtPlugin->_checkHookDir(trim($data['hook']));
			 	$xtPlugin->_deleteHookFiles(trim($data['id']));
	
			 	$filename = $sort.'_'.$plugin_code;         
	         
			 	$xtPlugin->_createHookFile(trim($data['hook']), $data['code'], $filename);
	
			}
		}

		$db->Execute(
			"update " . $this->_table . " set code_status = ? where ".$this->_master_key." = ?",
			array($status, $id)
		);
	}
}