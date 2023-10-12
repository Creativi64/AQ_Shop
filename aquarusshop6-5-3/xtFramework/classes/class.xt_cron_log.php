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


class xt_cron_log extends default_table {

	protected $_table = TABLE_SYSTEM_LOG;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'log_id';

	function _getParams() {


		$params = array();

		$header['cron_id'] = array('type' => 'hidden');

		$params['header']         = $header;
		$params['display_searchPanel']  = false;
		$params['master_key']     = $this->_master_key;

        $params['display_statusTrueBtn']  = false;
        $params['display_statusFalseBtn']  = false;

        $params['display_newBtn']  = false;
        $params['display_deleteBtn']  = true;
        $params['display_editBtn']  = false;

        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('log_id','runtime','class','created');
        } else {

        }

		return $params;
	}


	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;


		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}		
		
		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'message_source="cronjob" and identification='.(int)$this->url_data['cron_id'], $this->sql_limit,'','',' ORDER BY log_id DESC');

		if ($this->url_data['get_data'])
		{
			$table_data->_filterData = false;
			$data = $table_data->getData();
            foreach ($data as $key=>$value) {
                $arr = unserialize($data[$key]['data']);
				$runTimeAndError = array();
				if(isset($arr['runtime']))
					$runTimeAndError[] = $arr['runtime'];
				if(isset($arr['error'])) {
					$runTimeAndError[] = $arr['error'];
				}
				$data[$key]['runtime'] = implode(' : ',$runTimeAndError);
            }

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
		global $db,$language,$filter;


		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();

		$obj->success = true;

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
