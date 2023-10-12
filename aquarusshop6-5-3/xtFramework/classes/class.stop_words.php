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

class stop_words extends xt_backend_cls {

    protected $_table = TABLE_SEO_STOP_WORDS;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'stop_word_id';

    function _getParams() {
        $params = array();

        $header['language_code_plus_all'] = array(
            'type' => 'dropdown', 								// you can modyfy the auto type
            'url'  => 'DropdownData.php?get=language_codes_plus_all'
        );

        $header['stopword_lookup'] = array('type' => 'textfield');
        $header['stopword_replacement'] = array('type' => 'textfield');
        $header['stop_word_id'] = array('type' => 'hidden');
        $header['replace_word'] = array('type' => 'status');

        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = 'stop_word_id';
        $params['languageTab']    = 0;
        $params['PageSize']       = 50;

        $params['include']        = array ('stop_word_id', 'language_code_plus_all', 'stopword_lookup', 'stopword_replacement','replace_word');
        $params['exclude']        = array ('');
        $params['display_searchPanel']  = true;


        return $params;
    }

    function _getSearchIDs($search_data) {
        global $filter;

        $sql_tablecols = array('language_code',
            'stopword_lookup'
        );

        $sql_where = [];
        foreach ($sql_tablecols as $tablecol) {
            $sql_where[]= "(".$tablecol." LIKE '%".$filter->_filter($search_data)."%')";
        }

        $sql_data_array = '';
        if(is_array($sql_where)){
            $sql_data_array = " (".implode(' or ', $sql_where).")";
        }

        return $sql_data_array;
    }

    function _get($ID = 0) {
        global $xtPlugin, $db, $language;
        $where='';
        if ($this->position != 'admin') return false;
		$obj = new stdClass;
        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;

        if($this->url_data['query']){
            $sql_where = $this->_getSearchIDs($this->url_data['query']);
            $where .= $sql_where;
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        if(!defined('TEXT_LANGUAGE_CODE_PLUS_ALL'))
        {
            if ($language->code == 'de')
            {
                define('TEXT_LANGUAGE_CODE_PLUS_ALL', 'Sprache');
            }
            else
            {
                define('TEXT_LANGUAGE_CODE_PLUS_ALL', 'Language');
            }
        }
        if(!defined('TEXT_ALL_LANGUAGES'))
        {
            if ($language->code == 'de')
            {
                define('TEXT_ALL_LANGUAGES', 'Alle Sprachen');
            }
            else
            {
                define('TEXT_ALL_LANGUAGES', 'All languages');
            }
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->sql_limit);
        if ($this->url_data['get_data']){
            $data = $table_data->getData();
        }elseif($ID){
            $data = $table_data->getData($ID);
        }else{
            $data = $table_data->getHeader();
        }

        if ($this->url_data['get_data'] || $ID)
        {
            foreach ($data as $k => $v)
            {
                $data[$k]['language_code_plus_all'] = $v['language_code'];
            }
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

        $data['language_code'] = $data['language_code_plus_all'];

        $db->Execute("DELETE FROM ". $this->_table ." WHERE language_code = '' ");

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

        $obj = new stdClass;
        $obj->success = true;
        return $obj;
    }
}