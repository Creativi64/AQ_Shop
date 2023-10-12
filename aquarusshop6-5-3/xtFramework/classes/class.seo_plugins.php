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

class seo_plugins extends xt_backend_cls {

	public    $_store_field = 'store_id';
	public    $store_field_exists = false;
    protected $_table = TABLE_SEO_URL;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'link_id';

    function _getParams() {
    	global $language,$store_handler;
        $params = array();
		
		if (StoreIdExists($this->_table,$this->_store_field)) 
		{
			$this->store_field_exists=true;
		}
		
		if ($this->store_field_exists)
			$params['languageStoreTab'] = true;

		$stores = $store_handler->getStores();
		
		foreach ($stores as $store) {
	        foreach ($language->_getLanguageList() as $key => $val) {
				$add_to_f='';
				if ($this->store_field_exists) $add_to_f = 'store'.$store['id'].'_';
				if(_SYSTEM_HIDE_SUMAURL=='true'){
					$header['url_text_'.$add_to_f.$val['code']] = array('type'=>'hidden');
				}else{
					$header['url_text_'.$add_to_f.$val['code']] = array('width'=>400);
				}
	
				$header['meta_keywords_'.$add_to_f.$val['code']] = array('width'=>400);
				$header['meta_title_'.$add_to_f.$val['code']] = array('width'=>400);
				$header['meta_description_'.$add_to_f.$val['code']] = array('type' => 'textarea','width'=>400,'height'=>60);
			}
		}
		
		$header['link_type'] = array('type'=>'hidden');
		$header['link_id'] = array('type'=>'text','readonly' => 1);
		$header['plugin_code'] = array('type'=>'text','readonly' => 1);
		
        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = 'link_id';
        $params['PageSize']       = 50;
		$params['display_newBtn']  = false;
		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'])
        	$params['include']        = array ('link_id', 'link_type','plugin_code','url_text');
		else  $params['exclude']        = array ('');
        $params['display_searchPanel']  = false;

        return $params;
    }

    function _get($ID = 0) {
        global $xtPlugin, $db, $language;
        $where='';
        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;
        if ($this->url_data['get_data']){
        	
        	$res = $db->Execute(
				"SELECT DISTINCT t.*,p.plugin_code FROM ".$this->_table." t
        		LEFT JOIN " . TABLE_PLUGIN_CODE . " p ON p.plugin_id = t.link_id
        		WHERE t.link_type = '1000' and t.language_code = ? ", array($language->code));
        	if ($res->RecordCount() > 0) 
        	{
        		$i=0;
				while(!$res->EOF)
				{
					$data[$i]= $res->fields; 
					$i++;
					$res->MoveNext();
				}$res->Close();	 
			}
        }elseif($ID){
        	
			$res = $db->Execute("SELECT DISTINCT t.*, p.plugin_code FROM ".$this->_table." t 
        							LEFT JOIN " . TABLE_PLUGIN_CODE . " p ON p.plugin_id = t.link_id 
        						WHERE t.link_type = '1000' and t.link_id = ? ", array($ID));
			if ($res->RecordCount() > 0) 
        	{					
				while(!$res->EOF)
				{
					$store_field='';
					if ($this->store_field_exists) {
						$store_field= 'store'.$res->fields['store_id'].'_';
					}
					$data[0]['url_text_'.$store_field.$res->fields['language_code']] = $res->fields['url_text'];
					$data[0]['meta_keywords_'.$store_field.$res->fields['language_code']] = $res->fields['meta_keywords'];
					$data[0]['meta_title_'.$store_field.$res->fields['language_code']] = $res->fields['meta_title'];
					$data[0]['meta_description_'.$store_field.$res->fields['language_code']] = $res->fields['meta_description'];
					$data[0]['link_type'] = $res->fields['link_type'];
					$data[0]['link_id'] = $res->fields['link_id'];
					$data[0]['plugin_code'] = $res->fields['plugin_code'];
					$res->MoveNext();
				}$res->Close();	
				
				$data[0] = $this->checkAllLangs($data[0]);
			} 			
        }else{
        	$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where,'','','','',$store_field);
            $data = $table_data->getHeader();
			$data[0]['plugin_code'] = 'plugin_code'; 
        }

        $count_data = count($data);
		$obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }
	
	function checkAllLangs($data)
	{
		global $db,$language,$store_handler;
		$stores = $store_handler->getStores();
		
		foreach ($stores as $store) {
			foreach ($language->_getLanguageList() as $key => $val) 
			{
				$add_to_f='';
				if ($this->store_field_exists) $add_to_f = 'store'.$store['id'].'_';
				$data['url_text_'.$add_to_f.$val['code']] = ($data['url_text_'.$add_to_f.$val['code']]=='')? '' : $data['url_text_'.$add_to_f.$val['code']];
				$data['meta_keywords_'.$add_to_f.$val['code']] = ($data['meta_keywords_'.$add_to_f.$val['code']]=='')? '' :$data['meta_keywords_'.$add_to_f.$val['code']];
				$data['meta_title_'.$add_to_f.$val['code']] = ($data['meta_title_'.$add_to_f.$val['code']]=='')? '' :$data['meta_title_'.$add_to_f.$val['code']];
				$data['meta_description_'.$add_to_f.$val['code']] = ($data['meta_description_'.$add_to_f.$val['code']]=='')? '' :$data['meta_description_'.$add_to_f.$val['code']];
	
			}
		}
		return $data;
	}
	
    function _set($data, $set_type = 'edit') {
        global $db,$language, $seo,$store_handler;

		
		$rec = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE  plugin_id=? LIMIT 0,1", array($data['link_id']));
	    if ($rec->RecordCount() >0) 
	    	$data['plugin_code'] = $rec->fields['code'];

		$stores = $store_handler->getStores();
		
		foreach ($stores as $store) {
			foreach ($language->_getLanguageList() as $key => $val) 
			{
				$insert_data['language_code'] = $val['code'];
	            $insert_data['link_type'] = '1000';
	            $insert_data['link_id'] =$data['link_id'];
				$insert_data['store_id'] =$store['id'];

				$remove_suma_lang = false;
				$key_exists = array_key_exists('url_text_store'.$store['id'].'_'.$val['code'], $data);
				if ($key_exists && $data['url_text_store'.$store['id'].'_'.$val['code']]!='' && _SYSTEM_SEO_URL_LANG_BASED == 'true')
				{
					$url_text = $data['url_text_store'.$store['id'].'_'.$val['code']];
				}
				else if ($key_exists && $data['url_text_store'.$store['id'].'_'.$val['code']]!='')
				{
					$url_text = $data['url_text_store'.$store['id'].'_'.$val['code']];
					$remove_suma_lang = true;
				}
				else if (_SYSTEM_SEO_URL_LANG_BASED == 'true'){
					$url_text = $val['code'].'/'.$data['plugin_code'];
				}
				else {
					$url_text = $data['plugin_code'];
					$remove_suma_lang = true;
				}
				$exp = explode($val['code'].'/',$url_text);

				$suma_lang = $remove_suma_lang ? '' : $val['code'].'/';

				$insert_data['url_text'] = $suma_lang.$seo->filterAutoUrlText($exp[count($exp)-1],$val['code'] );
		
				$url_text = $seo->validateDBKeyLink ($insert_data,'');
	    		$url_md5 = $seo->_UrlHash($url_text);
				$insert_data['url_md5'] = $url_md5;
				$insert_data['meta_keywords'] =     array_key_exists('meta_keywords_store'.$store['id'].'_'.$val['code'],   $data) ? $data['meta_keywords_store'.$store['id'].'_'.$val['code']] : '';
				$insert_data['meta_title'] =        array_key_exists('meta_title_store'.$store['id'].'_'.$val['code'],      $data) ? $data['meta_title_store'.$store['id'].'_'.$val['code']] : '';
				$insert_data['meta_description'] =  array_key_exists('meta_description_store'.$store['id'].'_'.$val['code'],$data) ? $data['meta_description_store'.$store['id'].'_'.$val['code']] : '';
				
				$record = $db->Execute(
					"SELECT * FROM " . TABLE_SEO_URL . " WHERE link_type='1000' and link_id=? and language_code=? and store_id=?",
					array($data['link_id'], $val['code'], $store['id'])
				);
		        if ($record->RecordCount() == 0) {
		            $db->AutoExecute(TABLE_SEO_URL,$insert_data,'INSERT');
		        }else if (array_key_exists('link_type', $data)){
					$db->AutoExecute(TABLE_SEO_URL,$insert_data,'UPDATE',"link_type=".$db->Quote($data['link_type'])." and link_id=".$db->Quote($data['link_id'])." and language_code=".$db->Quote($val['code'])." and store_id=".$db->Quote($store['id']));
		        }
			}
		}
		$obj = new stdClass;
       	$obj->success = true;
		return $obj;	
    }

    function _unset($id = 0) {
        global $db;

        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        $id=(int)$id;
        if (!is_int($id)) return false;
		saveDeletedUrl($id,1000);
		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ? and link_type=1000", array($id));

    }

	function getPluginData($id, $all=0, $shop_id=0)
	{
		global $xtPlugin, $db,$language,$filter, $store_handler;

		($plugin_code = $xtPlugin->PluginCode('class.seo_plugin.php:getPluginData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
		if ($all==0) $add_to_sql=" and language_code = '".$language->code."'";
		else $add_to_sql = '';
		if ($shop_id==0) $add_to_sql .= " and store_id = '".$store_handler->shop_id."'";
		else $add_to_sql .= '';
		$query = "SELECT * FROM ".$this->_table." WHERE link_type='1000' and link_id = ? ".$add_to_sql;
		
		$record = $db->Execute($query, array($id));
		if($record->RecordCount() > 0){
			if($all==0) 
				$data = $record->fields;
			else
			{
				$i=0;
				while(!$record->EOF)
				{
					$data[$i]= $record->fields; 
					$i++;
					$record->MoveNext();
				}$record->Close();	 
			}
			($plugin_code = $xtPlugin->PluginCode('class.seo_plugin.php:getPluginData_bottom')) ? eval($plugin_code) : false;
			return $data;
		}else{
			return false;
		}
	}
	
	function getPluginByPluginCode($plugin_code)
	{
		global $db,$language;

		$query = "SELECT * FROM ".TABLE_PLUGIN_PRODUCTS." WHERE code =? LIMIT 0,1 ";
		
		$record = $db->Execute($query, array($plugin_code));
		if($record->RecordCount() > 0){
			$data = $record->fields;
			return $data;
		}else{
			return false;
		}
	}
	
	function getPluginByID($id)
	{
		global $db,$language;

		$query = "SELECT * FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_id =? LIMIT 0,1 ";
		$record = $db->Execute($query, array($id));
		if($record->RecordCount() > 0){
			$data = $record->fields;
			return $data;
		}else{
			return false;
		}
	}
	
	public function setPluginSEO($code)
	{
		$plugin_data = $this->getPluginByPluginCode($code);
		$insert_data = array('plugin_code' => $code, 'link_id'=> $plugin_data['plugin_id']);
		$this->_set($insert_data);
		
	} 

	public function unsetPluginSEO($code)
	{ global $db;
		$plugin_data = $this->getPluginByPluginCode($code);
		$db->Execute("DELETE FROM ". TABLE_SEO_URL ." WHERE link_id = ? and link_type=1000", array($plugin_data['plugin_id']));
	}
}