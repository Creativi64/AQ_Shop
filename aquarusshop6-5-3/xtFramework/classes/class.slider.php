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

class slider extends xt_backend_cls {
	
	
	public $_table = TABLE_SLIDER;

	public $_master_key = 'slider_id';
	
	function _getParams() {
		$params = array();


		$params['header']         = [];
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
 		$params['languageTab']    = false;

 		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;
		$params['display_checkItemsCheckbox']  = true;
		
		$rowActions[] = array('iconCls' => 'slides', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_SLIDES'));
		if ($this->url_data['edit_id'])
			$js = "var edit_id = ".$this->url_data['edit_id']."; var edit_name = '".htmlentities($this->url_data['edit_id'])."';\n";
		else
			$js = "var edit_id = record.id; var edit_name=record.get('slider_id');\n";
		
		$js .= "addTab('adminHandler.php?load_section=slides&pg=overview&slider_id='+edit_id,'".__text('TEXT_SLIDES')." ('+edit_name+')', 'slides'+edit_id)";
		
		$rowActionsFunctions['slides'] = $js;
		
		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;

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

		if ($this->url_data['get_data'])
        $data = $table_data->getData();
        elseif($ID)
        $data = $table_data->getData($ID);
        else
		$data = $table_data->getHeader();

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}
	
	function _set($data, $set_type='edit'){
		global $db,$language,$filter;
	
		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();
	
		return $obj;
	}
	
	/**
	 * get slider content for frontend
	 * @param unknown $slider_id
	 * @return multitype:
	 */
	function _getSlider($slider_id) {
		global $db,$language;
		
		$slider_id = (int)$slider_id;
		
		// check if slider exists etc
		$query = "SELECT * FROM ".$this->_table." WHERE slider_id=?";
		$rs = $db->Execute($query, array($slider_id));
		if ($rs->RecordCount()==1) {
			$_slides = new slides();
			$slides = $_slides->_getSlides($slider_id);
			if ($slides!=false) {
				
				$slider_data = $rs->fields;
				$slider_data['slides']=$slides;
				return $slider_data;
				
			}
			return false;
		} 
		return false;
	}
	
	function _unset($id = 0) {
		global $db;
		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
	
		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));		
		$db->Execute("DELETE FROM ". TABLE_SLIDES ." WHERE slider_id = ?", array($id));
	}
	
	
}