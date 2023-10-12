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

class acl_user extends xt_backend_cls {

	protected $_table = TABLE_ADMIN_ACL_AREA_USER;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'user_id';

	function setPosition ($position) {
		$this->position = $position;
	}

	function _getParams() {
		global $xtPlugin;

		if(empty($this->url_data['get_data']) && empty($this->url_data['edit_id']))
		{
			unset($_SESSION['filters_acl_user']);
		}

		$rowActions = array();
		$rowActionsFunctions = array();

		$params = array();
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_top')) ? eval($plugin_code) : false;

		$header['user_id'] = array('type' => 'hidden');
		$header['user_password'] = array('type' => 'password');
		
		$header['admin_current_ip'] = array('type' => 'textfield','readonly'=>1);

		$header['group_id'] = array(
            'required' => true,
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=acl_group_list'
		);

        $header['firstname'] = array('required' => true);
        $header['lastname'] = array('required' => true);


		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_rowactions')) ? eval($plugin_code) : false;
		
		if (count($rowActions)>0) $params['rowActions']             = $rowActions;
		if (count($rowActionsFunctions)>0) $params['rowActionsFunctions']    = $rowActionsFunctions;
		
		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
		$params['languageTab']    = false;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['exclude'] = array('user_password','password_request_key');
		}
		else
			$params['exclude'] = array('password_request_key');

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_bottom')) ? eval($plugin_code) : false;
		return $params;
	}


	function _get($ID = 0) {
		global $xtPlugin, $db, $language;
		$obj = new stdClass;
		if ($this->position != 'admin') return false;

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        if ($this->url_data['get_data'])
        {
            $data = $table_data->getData();
        }
        elseif ($ID)
        {
            if ($ID === 'new') {
                $data = $table_data->getHeader(); // empty data set
            }
            else
            {
                $data = $table_data->getData($ID);
                $data[0]['user_password'] = '';
            }
            if ($_SERVER["HTTP_X_FORWARDED_FOR"])
            {
                $admin_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }
            else
            {
                $admin_ip = $_SERVER["REMOTE_ADDR"];
            }

            $data[0]['admin_current_ip'] = $admin_ip;
        }
        else
        {
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
		global $db,$language,$filter,$xtPlugin;

        if(empty($data['user_id'])) // new
        {
            $exists = $db->GetOne('SELECT handle FROM '.TABLE_ADMIN_ACL_AREA_USER.' WHERE handle = ?', [$data['handle']]);
            if(!empty($exists))
            {
                $obj = new stdClass();
                $obj->success = false;
                $obj->error = true;
                $obj->error_message = defined('TEXT_USER_EXISTS') ? __text('TEXT_USER_EXISTS') : 'Benutzername bereits vergeben<br>User name already exists';
                return $obj;
            }
        }

		if($data['user_password']!=''){
			$pw = new xt_password();
			
		 	$data['user_password'] = $pw->hash_password($data['user_password']);
		}else{
			unset($data['user_password']);
		}

		($plugin_code = $xtPlugin->PluginCode('class.acl_user:_set')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		 $obj = new stdClass;
		 $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		 $obj = $o->saveDataSet();

		return $obj;
	}

	function _unset($id = 0) {
	    global $db,$xtPlugin;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

		($plugin_code = $xtPlugin->PluginCode('class.acl_user:_unset')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
		
	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
	}
}