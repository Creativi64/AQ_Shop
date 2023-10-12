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

class redirect_404 extends xt_backend_cls {

    public    $_store_field = 'store_id';
    public    $store_field_exists = false;
    protected $_table = TABLE_SEO_URL_REDIRECT;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'master_key';

    function _getParams() {
    	global $language,$store_handler;

        if(empty($this->url_data['get_data']) && empty($this->url_data['edit_id']))
        {
            //unset($_SESSION['filters_redirect_404']);
        }

        $params = array();
		
		if (StoreIdExists($this->_table,$this->_store_field)) 
		{
			$this->store_field_exists=true;
		}
		
		if ($this->store_field_exists)
			$params['languageStoreTab'] = true;
		

		$stores = $store_handler->getStores();
		
		if(_SYSTEM_HIDE_SUMAURL=='true'){
			$header['url_text'] = array('type'=>'hidden');
		}else{
			$header['url_text'] = array('width'=>400,'readonly' => 1);
			$header['url_text_redirect'] = array('width'=>400);
		}
        $header['master_key'] = array('type'=>'text','readonly' => 1);
		$header['url_md5'] = array('type'=>'hidden');
		$header['link_id'] = array('type'=>'hidden');
		$header['link_type'] = array('type'=>'hidden');
		$header['url_md5_redirect'] = array('type'=>'hidden');
		$header['is_deleted'] = array('type'=>'hidden');
		
		$header['language_code'] = array('type'=>'text','readonly' => 1);
		$header['store_id'] = array('type'=>'text','readonly' => 1, 'width' => 80);
		$header['total_count'] = array('type'=>'text','readonly' => 1, 'width' => 80);
        $header['count_day_last_access'] = array('type'=>'text','readonly' => 1);
        $header['last_access'] = array('type'=>'text','readonly' => 1);
		
        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = 'link_id';

        $pageSize = (int)_SYSTEM_ADMIN_PAGE_SIZE_SEO_404;
        if($pageSize && is_int($pageSize)) $params['PageSize'] = $pageSize;

        if (isset($this->sql_limit))
        {
            $exp= explode(",",$this->sql_limit);
            $params['PageSize'] = trim($exp[1]);
        }
		$params['display_newBtn']  = false;
		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'])
        {
            $params['include']        = array ('store_id','lng_code', 'master_key','url_text','url_text_redirect','total_count', 'count_day_last_access','last_access');
        }
		else  $params['exclude']        = array ('');
        $params['display_searchPanel']  = false;

        $params['display_checkItemsCheckbox']  = true;
        $params['display_checkCol']  = true;

        define('TEXT_LNG_CODE', __text('TEXT_LANGUAGE'));

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
		
        $store_field= '';
		
		if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }
		
		$sql_where = ' is_deleted =0 ';
		$sort_order = ' ORDER BY last_access DESC ';
		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit, $permissions,'',$sort_order, $store_field);
		
        if ($this->url_data['get_data']){
			$data = $table_data->getData();
            foreach($data as &$v)
            {
                $v['total_count'] = (int)$v['total_count'];
                $v['count_day_last_access'] = $v['count_day_last_access'] ? (int)$v['count_day_last_access'] : '';
                $v['master_key'] =  (int)$v['master_key'];
                $v['lng_code'] = $v['language_code'];
            }
			
			$data_count = $table_data->_total_count;
			
        }elseif($ID){
			$data = $table_data->getData($ID);
			
        }else{
            $data = $table_data->getHeader();
        }

        if($data_count!=0 || !$data_count)
            $count_data = $data_count;
        else
            $count_data = count($data);
		$obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }
	
	
    function _set($data, $set_type = 'edit') {
        global $db,$language, $seo;
		
		$insert_data = $data;
		$insert_data['url_text_redirect'] = $data['url_text_redirect'];
		$validate_data = $insert_data;
		$validate_data['url_text'] = $insert_data['url_text_redirect'];		
		$url_text = $seo->validateDBKeyLink ($validate_data,'');
	    $url_md5 = $seo->_UrlHash($url_text);
		$insert_data['url_md5_redirect'] = $url_md5;
		
		$db->AutoExecute($this->_table,$insert_data,'UPDATE',"master_key=".$db->Quote($data['master_key'])." ");
				
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
		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ? ", array($id));
    }
}