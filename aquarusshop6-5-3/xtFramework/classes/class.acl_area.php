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

class acl_area extends acl{

	protected $_table = TABLE_ADMIN_ACL_AREA;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'area_id';

	function setPosition ($position) {
		$this->position = $position;
	}

	function _getAreaList(){
		global $db;

		$query = "SELECT * FROM " . TABLE_ADMIN_ACL_AREA . " ";

		$record = $db->Execute($query);
		if ($record->RecordCount() > 0) {
			while(!$record->EOF){
				$data[] = $record->fields;
				$record->MoveNext();
			} $record->Close();

		}

		return $data;
	}

	function _getParams() {
		$params = array();

		$params['header']         = array();
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
		$params['languageTab']    = false;

		return $params;
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

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

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db,$language,$filter;

		 $obj = new stdClass;
		 $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		 $obj = $o->saveDataSet();

		return $obj;
	}

	function _unset($id = 0) {
	    global $db;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
 		$db->Execute("DELETE FROM ". TABLE_ADMIN_ACL_AREA_PERMISSIONS ." WHERE ".$this->_master_key." = ?", array($id));
	}
}