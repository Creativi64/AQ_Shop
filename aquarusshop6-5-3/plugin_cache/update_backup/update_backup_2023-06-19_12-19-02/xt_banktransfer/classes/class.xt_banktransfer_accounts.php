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


class xt_banktransfer_accounts extends default_table {

    protected $_table = TABLE_XT_BANKTRANSFER;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'account_id';

    function _getParams() {


        $params = array();

        $header['account_id'] = array('type' => 'hidden');
        $header['customer_id'] = array('type' => 'hidden');

        $header['sepa_mandat'] = array('type' => 'status');

        $params['display_checkCol'] = true;
        $params['display_statusTrueBtn'] = true;
        $params['display_statusFalseBtn'] = true;

        $params['header']         = $header;
        $params['display_searchPanel']  = false;
        $params['master_key']     = $this->_master_key;

        return $params;
    }

    function _get($ID = 0) {
        global $xtPlugin, $db, $language;

        if ($this->position != 'admin') return false;

        $obj = new stdClass();

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'customer_id='.(int)$this->url_data['customers_id'], $this->sql_limit);

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

    function _set($data, $set_type='edit'){
        global $db,$language,$filter;

        if($set_type=='new'){
            $data['customer_id'] = (int)$this->url_data['customers_id'];
        }


        $obj = new stdClass;
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $obj->success = true;

        return $obj;
    }

    function _setStatus ($id, $status)
    {
        global $db, $xtPlugin;
        ($plugin_code = $xtPlugin->PluginCode('class.xt_banktransfer_accounts.php:_setStatus')) ? eval($plugin_code) : false;
        $id = (int)$id;
        if (!is_int($id)) return false;
        $db->Execute("update " . $this->_table . " set sepa_mandat =? where account_id = ? ",array($status,(int)$id));

    }

    public static function getAccountsForCustomer($customers_id)
    {
        global $db;

        $rs = $db->GetArray('SELECT * FROM '.TABLE_XT_BANKTRANSFER.' WHERE customer_id=?', $customers_id);

        return $rs;
    }

}