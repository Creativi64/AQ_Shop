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


class xt_api_log extends default_table {

    protected $_table = TABLE_XT_API_LOG;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'api_log_id';

    function setPosition ($position) {
        $this->position = $position;
    }

    function _getParams() {


        $params = array();

        $header['api_log_id'] = array('type' => 'hidden');
        $header['api_request'] = array('type' => 'textarea','readonly'=>'true');
        $header['api_response'] = array('type' => 'textarea','readonly'=>'true');

        $header['request_source'] = array('readonly'=>'true');
        $header['request_service'] = array('readonly'=>'true');
        $header['log_time'] = array('readonly'=>'true');
        $header['api_user_id'] = array('readonly'=>'true');

        $params['header']         = $header;
        $params['display_searchPanel']  = false;
        $params['master_key']     = $this->_master_key;

        $params['display_statusTrueBtn']  = false;
        $params['display_statusFalseBtn']  = false;

        $params['display_newBtn']  = false;
        $params['display_deleteBtn']  = false;
        $params['display_editBtn']  = true;
        $params['display_editMenu']  = false;

        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('api_log_id','log_time','request_service','request_source');
        } else {

        }

        return $params;
    }


    function _get($ID = 0) {

        if ($this->position != 'admin') return false;


        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'', $this->sql_limit,'','',' ORDER BY api_log_id DESC');

        if ($this->url_data['get_data'])
        {
            $table_data->_filterData = false;
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

        $obj = new stdClass;
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($data, $set_type='edit'){


        $obj = new stdClass();

        $obj->success = true;

        return $obj;
    }

    function _unset($id = 0) {

    }
}