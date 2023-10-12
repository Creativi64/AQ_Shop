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

class adminDB_DataRead {
    protected $_table = null;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = null;
    protected $_master_qry = null;
    protected $_master_limit = null;
    protected $_lang_field = null;
    protected $store_lang_field;
    protected $_perm_data = null;
    protected $_joinCondition = null;
    protected $_store_lang_field= null;
    protected $_sort_order;

    var $_total_count = 0;

    function __construct($table, $table_lang = '', $table_seo = '', $master_key = '', $master_qry='', $master_limit='', $perm_data='', $filter_data = '', $sort_order='',$store_lang_field='') {

        $this->_table = trim($table);
        if($table_lang)
            $this->_table_lang = trim($table_lang);

        if($table_seo)
            $this->_table_seo = trim($table_seo);

        $this->_master_key = $master_key;

        if($master_qry)
            $this->_master_qry = $master_qry;

        if($master_limit)
            $this->_master_limit = $master_limit;

        if($sort_order)
            $this->_sort_order = $sort_order;

        $this->_lang_field = 'language_code';

        $this->_perm_data = $perm_data;

        if($filter_data)
            $this->_filterData = $filter_data;
        else
            $this->_filterData = 'true';

        $this->_store_lang_field = $store_lang_field;
    }

    /**
     * Sets join condition
     * @param string $joinCondition
     * @return adminDB_DataRead
     */
    public function setJoinCondtion($joinCondition) {
        $this->_joinCondition = $joinCondition;
        return $this;
    }

    function getTableFields($table){
        global $db;

        $query = "SHOW FIELDS FROM ".$table." ";
        $record = $db->Execute($query);
        if ($record->RecordCount() > 0) {
            while(!$record->EOF){

                $records[] = $record->fields;

                $record->MoveNext();
            } $record->Close();
        }

        foreach ($records as $key => $val) {
            $data[$val['Field']] = '';
        }

        return $data;
    }


    function getTableData($id = null, $store_id = 0){
        global $db, $language,$seo, $filter, $form_grid, $xtPlugin,$store_handler;

        $where = $lang_where =$ad_table = $limit = $order_by = '';
        $where_qry = false; // for usage in hook propably

        if ($id !== null) {
            $where_qry = true;
            $ad_table = '';
            $where = " WHERE ".$this->_master_key." = '".$id."'";
        }
        if($this->_master_qry){
            if ($id !== null){
                $where .= " and ".$this->_master_qry;
            }else{
                $where_qry = true;
                $where .= " where ".$this->_master_qry;
            }
        }
        if ( !empty( (int)$store_id) ) {
            $lang_where = " AND products_store_id = '".(int)$store_id."' ";
        }

        if(isset($form_grid) && $form_grid->params['SortField'] &&  $this->_sort_order==''){
            $order_by = " order by ".$form_grid->params['SortField'] .' '.$form_grid->params['SortDir'];
        }elseif($this->_sort_order!=''){
            $order_by = $this->_sort_order;
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_where')) ? eval($plugin_code) : false;

        if ($id == null && !isset($_GET['noFilter']))
        {
            // to_filter - klassen mit direktem mapping von db-tabelle auf klasse per DB_PREFIX + Klasse
            // bzw mit bekanntem mapping, zb DB_PREFIX_products_reviews > xt_reviews
            $to_filter = array("plg_products_attributes","products", "customers", "products_reviews", "acl_user", "seo_url_redirect", "seo_url_redirect_deleted", "content", "mail_templates", "pdf_manager");
            // einige xt klassen verwenden stattdessen class.XYZPost.php direkt in ihren _get()'s, zB order, LogHandler
            $where_ar = [];
            $table_class = '';

            foreach($to_filter as $table ){
                if($this->_table == DB_PREFIX."_".$table){

                    $where_ar = array();

                    if ($this->_table == DB_PREFIX."_products_reviews")  {
                        $table_class ="xt_reviews";
                    }
                    else if($this->_table == DB_PREFIX."_seo_url_redirect_deleted")  {
                        $table_class = "redirect_deleted";
                        $this->_table = TABLE_SEO_URL_REDIRECT;
                    }
                    else if($this->_table == TABLE_SEO_URL_REDIRECT)  {
                        $table_class = "redirect_404";
                    }
                else if($this->_table == DB_PREFIX."_plg_products_attributes")  {
                    $table_class ="xt_master_slave";
                }
                    else if (file_exists(_SRV_WEBROOT."xtFramework/admin/filter/class.".$table ."Post.php")){
                        $table_class = $table;
                    }

                    if (file_exists(_SRV_WEBROOT."xtFramework/admin/filter/class.".$table_class ."Post.php")){
                    require_once(_SRV_WEBROOT."xtFramework/admin/filter/class.formFilter.php");
                    require_once(_SRV_WEBROOT."xtFramework/admin/filter/class.".$table_class ."Post.php");
                        break;
                    }
                }
            }

            // !im hook prüfen ob wirklich zutreffend
            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_where_filter')) ? eval($plugin_code) : false;

            if( (is_array($where_ar)) && ( sizeof($where_ar)>0 )){
                if(strlen($where)>0) $where.=" AND "; else  $where.=" WHERE ";
                $where .= implode(" AND ", $where_ar);
            }
        }

        $ad_table = $this->_joinCondition . ' ' . $ad_table;
        $count_query = 'select COUNT(*) from ' . $this->_table ." $ad_table ". ' ' . $where;
        $this->_total_count = $db->GetOne($count_query);

        // in welchem format kommt die tabelle  'xt_products' oder 'xt_products p' ?
        $table_or_column = $this->_table;
        $table_name_parts = explode(' ', $this->_table);
        if(count($table_name_parts) > 1)
            $table_or_column = end($table_name_parts);
        $count_query = 'select COUNT(DISTINCT '.$table_or_column .'.'.$this->_master_key.') from ' . $this->_table ." $ad_table ". ' ' . $where;
        $this->_total_count = $db->GetOne($count_query);

        if($this->_master_limit){
            $limit = " LIMIT ".$this->_master_limit;
        }

        $query = "SELECT * FROM ".$this->_table ." $ad_table  ".$where . $order_by . $limit;

        $record = $db->Execute($query);
        if ($record->RecordCount() > 0) {
            while(!$record->EOF){
                $records = $record->fields;

                if(is_array($this->_perm_data)){
                    foreach ($this->_perm_data as $key => $val) {
                        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_perm')) ? eval($plugin_code) : false;

                        $val['id'] = $records[$this->_master_key];
                        $this->$key = new permission($val);
                        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_perm_data')) ? eval($plugin_code) : false;

                        $fields = '';
                        $fields = $this->$key->_get();
                        if(is_array($fields))
                            $records = array_merge($records, $fields);

                    }
                }

                $_lang_data = array();
                if ($this->_store_lang_field!='') // we pass store_lang_id
                {

                    $stores = $store_handler->getStores();

                    if ($this->_table_lang !== null && $records[$this->_master_key]) {

                        foreach ($stores as $store) {

                            foreach ($language->_getLanguageList() as $key => $val) {


                                $lang_record = $db->Execute("SELECT * FROM ".$this->_table_lang ." 
		    					                             WHERE ".$this->_master_key." = '".$records[$this->_master_key]."' 
		    					                                    and ".$this->_lang_field . " = '".$val['code']."'
		    														and ".$this->_store_lang_field."='".$store['id']."'");

                                if($lang_record->RecordCount() > 0){
                                    $langdata = $lang_record->fields;
                                } else {
                                    $langdata = $this->getTableFields($this->_table_lang);
                                    $langdata[$this->_store_lang_field] = $store['id'];
                                }

                                ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData__store_lang_data')) ? eval($plugin_code) : false;

                                unset($langdata[$this->_master_key]);
                                unset($langdata[$this->_lang_field]);
                                unset($langdata[$this->store_lang_field]);

                                $new_langdata = adminData_setStoreLangCode($langdata, $val['code'],$store['id']);
                                $records = array_merge($records, $new_langdata);
                                if ($id !== null) {
                                    if ($this->_table_seo !== null && $records[$this->_master_key]) {


                                        $s_filter = $seo->_getFilter($this->_master_key);
                                        $s_filter = " and link_type='".$s_filter."'";

                                        $seo_qry = "SELECT * FROM " . $this->_table_seo . " where link_id = '".$records[$this->_master_key]."'".$s_filter." and ".$this->_lang_field . " = '".$val['code']."' and store_id='".$store['id']."'";
                                        $seo_record = $db->Execute($seo_qry);
                                        if($seo_record->RecordCount() > 0){
                                            unset($seo_record->fields['url_md5']);
                                            unset($seo_record->fields['link_type']);
                                            unset($seo_record->fields['link_id']);
                                            unset($seo_record->fields['language_code']);

                                            $_seo_data = adminData_setStoreLangCode($seo_record->fields, $val['code'],$store['id']);
                                            $records = array_merge($records, $_seo_data);
                                        }else{
                                            $tmp_fields = array('url_text'=>'', 'meta_title'=>'', 'meta_keywords'=>'', 'meta_description'=>'');
                                            $_seo_data = adminData_setStoreLangCode($tmp_fields, $val['code'],$store['id']);
                                            $records = array_merge($records, $_seo_data);
                                        }
                                    }
                                }


                            }
                        }
                    }

                }
                else
                {
                    if ($this->_table_lang !== null && $records[$this->_master_key]) {
                        foreach ($language->_getLanguageList() as $key => $val) {
                            $lang_record = $db->Execute("SELECT * FROM ".$this->_table_lang ." WHERE ".$this->_master_key." = '".$records[$this->_master_key]."' and ".$this->_lang_field . " = '".$val['code']."'" .$lang_where);
                            if($lang_record->RecordCount() > 0){
                                $langdata = $lang_record->fields;
                            } else {
                                $langdata = $this->getTableFields($this->_table_lang);
                            }
                            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_lang_data')) ? eval($plugin_code) : false;

                            unset($langdata[$this->_master_key]);
                            unset($langdata[$this->_lang_field]);
                            $new_langdata = adminData_setLangCode($langdata, $val['code']);
                            $records = array_merge($records, $new_langdata);
                            if ($id !== null) {
                                if ($this->_table_seo !== null && $records[$this->_master_key]) {


                                    $s_filter = $seo->_getFilter($this->_master_key);
                                    $s_filter = " and link_type='".$s_filter."'";

                                    $seo_qry = "SELECT * FROM " . $this->_table_seo . " where link_id = '".$records[$this->_master_key]."'".$s_filter." and ".$this->_lang_field . " = '".$val['code']."'";
                                    $seo_record = $db->Execute($seo_qry);
                                    if($seo_record->RecordCount() > 0){
                                        unset($seo_record->fields['url_md5']);
                                        unset($seo_record->fields['link_type']);
                                        unset($seo_record->fields['link_id']);
                                        unset($seo_record->fields['language_code']);

                                        $_seo_data = adminData_setLangCode($seo_record->fields, $val['code']);
                                        $records = array_merge($records, $_seo_data);
                                    }else{
                                        $tmp_fields = array('url_text'=>'', 'meta_title'=>'', 'meta_keywords'=>'', 'meta_description'=>'');
                                        $_seo_data = adminData_setLangCode($tmp_fields, $val['code']);
                                        $records = array_merge($records, $_seo_data);
                                    }
                                }
                            }
                        }
                    }
                }

                ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getTableData_data')) ? eval($plugin_code) : false;

                //__debug($this->_filterData);

                if($this->_filterData=='true'){
                    $clean_records = array();
                    foreach ($records as $clean_key => $clean_val) {

                        //	echo Test;

                        $clean_records[$clean_key] = _filterText($clean_val, 'full');
                    }

                    $data[] = $clean_records;
                }else{
                    $data[] = $records;
                }

                $record->MoveNext();
            } $record->Close();

        } else return array();
        return $data;
    }

    function getData($id = null, $store_id = 0) {
        global $language;
        $data = $this->getTableData($id, $store_id);


        return $data;

    }

    function getHeader () {
        global $language;

        $data = array();
        if ($this->_table !== null) {
            $data = $this->getTableFields($this->_table);
        }

        if ($this->_table_lang !== null) {
            $langdata = $this->getTableFields($this->_table_lang);
            unset($langdata[$this->_master_key]);
            unset($langdata[$this->_lang_field]);
            foreach ($language->_getLanguageList() as $key => $val) {
                $new_langdata = adminData_setLangCode($langdata, $val['code']);
                if (count($new_langdata) > 0) {
                    $data = array_merge($data, $new_langdata);
                }
            }
        }

        return array($data);
    }

}
