<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class xt_api {

    protected $_table = TABLE_XT_API_USER;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'api_user_id';

    function setPosition ($position) {
        $this->position = $position;
    }


    function _getParams() {
        global $xtPlugin;

        if(empty($this->url_data['get_data']) && empty($this->url_data['edit_id']))
        {
            unset($_SESSION['filters_acl_user']);
        }

        $params = array();

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_top')) ? eval($plugin_code) : false;

        $header['api_user_id'] = array('type' => 'hidden');
        $header['api_password'] = array('type' => 'password');
        $header['api_log_active'] = array('type' => 'status');
        $header['api_access_restrictions'] = array('type' => 'textarea');


        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_getParams_rowactions')) ? eval($plugin_code) : false;


        if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){


            $params['exclude'] = array('api_password');
        }

        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = $this->_master_key;
        $params['languageTab']    = false;
        $params['display_statusTrueBtn']  = true;
        $params['display_statusFalseBtn']  = true;

        return $params;
    }

    function _get($ID = 0) {

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
            $data[0]['api_password'] = '';

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
        global $xtPlugin;

        if($data['api_password']!=''){
            $pw = new xt_password();

            if (method_exists($pw,'verify_password_security')) {
                $secure = $pw->verify_password_security($data['api_password']);
                if (is_array($secure)) {
                    $obj = new stdClass();
                    $obj->success = false;
                    $obj->error_message = $secure[0];
                    return $obj;
                }
            }

            $data['api_password'] = $pw->hash_password($data['api_password']);
        }else{
            unset($data['api_password']);
        }

        ($plugin_code = $xtPlugin->PluginCode('class.acl_user:_set')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

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

    function _setStatus($id, $status) {
        global $db;

        if ($id == 0) return false;
        if ($this->position != 'admin') return false;
        $id=(int)$id;
        if (!is_int($id)) return false;

        $db->Execute("update " . $this->_table . " set access_status = ".(int)$status." where api_user_id = ?", array($id));
    }

}