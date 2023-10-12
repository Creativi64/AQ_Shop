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

class slides extends xt_backend_cls {
	
	
	public $_table = TABLE_SLIDES;
	public $_table_lang = null;
	public $_table_seo = null;
	public $_image_key = 'slide_image';
	public $_master_key = 'slide_id';

	function _getParams() {
		$params = array();


		
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
 		$params['languageTab']    = false;

 		$header['slide_id'] = array('type' => 'hidden');
 		$header['slider_id'] = array('type' => 'hidden');
 		 		
 		$header['slide_language_code'] = array(
 				'type' => 'dropdown', // you can modyfy the auto type
 				'url' => 'DropdownData.php?get=language_codes', 'text' => __text('TEXT_LANGUAGE_SELECT')
 		);
 		
 		$params['header']         = $header;
 		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;
		$params['display_checkItemsCheckbox']  = false;

		return $params;
	}
	
	function _setImage($id, $file)
	{
		global $xtPlugin,$db,$language,$filter,$seo;
	
		if ($this->position !== 'admin') return false;
	
		$data[$this->_master_key] = $id;
		$data['slide_image'] = $file;
	
		$o = new adminDB_DataSave($this->_table, $data);
		$obj = $o->saveDataSet();
	
		$obj->totalCount = 1;
		if ($obj->success) {
			$obj->success = true;
		} else {
			$obj->failed = true;
		}
	
	
		return $obj;
	}
	
	
	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'slider_id='.(int)$this->url_data['slider_id']);

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

		if(!array_key_exists('slide_status', $data))
		    $data['slide_status'] = 0;

		unset($data['slide_image']);

		if($set_type=='new'){
			$data['slider_id'] = (int)$this->url_data['slider_id'];
		}
		
		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();
		

	
		return $obj;
	}
	
	function _getSlides($slider_id) {
		global $db,$language;
		
		$slider_id = (int)$slider_id;
		
		$query = "SELECT * FROM ".$this->_table." WHERE slider_id=? 
		    AND (
		            slide_language_code=? OR slide_language_code IS NULL OR slide_language_code = ''
		        )
		    AND (
                    (NOW() BETWEEN slide_date_from AND slide_date_to) OR
                    (slide_date_from IS NULL AND slide_date_to IS NULL)
		        )
		    AND slide_status=1 ORDER BY sort_order ASC";
		$rs = $db->Execute($query, array($slider_id,$language->code));
		if ($rs->RecordCount()>0) {
			$slides = array();
			while (!$rs->EOF) {
				$slides[]=$rs->fields;
				$rs->MoveNext();
			}
			return $slides;
			
		}
		
		return false;
	}
	
	function _unset($id = 0) {
		global $db;
		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;

		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
	}
	
	function _setStatus($id, $status)
	{
		global $db, $xtPlugin;
	
		$id = (int)$id;
	
		$db->Execute(
				"UPDATE ".$this->_table." SET slide_status = ? WHERE slide_id = ?",
				array($status, $id)
		);
	

	}
	
}