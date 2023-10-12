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

if (!defined("TABLE_CLEANCACHE"))
	define('TABLE_CLEANCACHE', DB_PREFIX.'_clean_cache');
if (!defined("TABLE_CLEANCACHE_LOGS"))
	define('TABLE_CLEANCACHE_LOGS', DB_PREFIX.'_clean_cache_logs');

class xt_cleancache_types{

	protected $_table = TABLE_CLEANCACHE;
	protected $_table_lang = '';
	protected $_table_seo = null;
	protected $_master_key = 'id';


	public function __construct(){
		global $xtPlugin;
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'cleancache_types')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;		
		
	}

	public function setPosition ($position) {
		$this->position = $position;
	}

	public function _getParams() {
		global $language;


		$params = array();
		$header['id'] = array('disabled' => 'true');
		$header['cache_type_desc'] = array('type'=>'text');
		$header['last_run'] = array('type' => 'true');
		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;

		$params['include'] = array('id',  'cache_type_desc', 'last_run');

		// Row Actions Run Export
		$rowActions[] = array('iconCls' => 'run_export', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_CLEANCACHE_TYPES);

        //if ($this->url_data['edit_id'])
        //  $js = "var edit_id = ".$this->url_data['edit_id'].";";
        //else
		$js = "var edit_id = record.data.id;";
		
		$js.= "Ext.Msg.confirm('".TEXT_START."','".TEXT_START_ASK."',function(btn){runEmport(edit_id,btn);})";
		
		$rowActionsFunctions['run_export'] = $js;
		
		$js = "function runEmport(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
				addTab('row_actions.php?type=cleancache&typeid='+edit_id,'".TEXT_START."');
			}
		};";

		$params['rowActionsJavascript'] = $js;
		$params['rowActions']             = $rowActions;

		if (!$this->url_data['edit_id'] && $this->url_data['new'] != true)
			$params['rowActionsFunctions']    = $rowActionsFunctions;
		
		$params['display_newBtn'] = false;
		$params['display_editBtn'] = false;
        $params['display_deleteBtn'] = false;
		$params['display_checkCol']  = false;
		$params['display_statusTrueBtn']  = false;
		$params['display_statusFalseBtn']  = false;
		
		$params['display_searchPanel']  = false;

		return $params;
	}

	public function _get($ID = 0) {
		global $xtPlugin, $db, $language,$filter;

		if ($this->position != 'admin') return false;

		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}	

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', $this->sql_limit);

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

        $obj = new stdClass;
		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;

	}

	public function _set($data, $set_type='edit'){
		global $db,$language,$filter;

		 $obj = new stdClass;

		 $oC = new adminDB_DataSave(TABLE_CLEANCACHE, $data);
		 $objC = $oC->saveDataSet();

		 if ($objC->success) {
		     $obj->success = true;
		 } else {
		     $obj->failed = true;
		 }

		return $obj;
	}

	public function _unset($id = 0) {
	    return false;
        global $db;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;

	    $db->Execute("DELETE FROM ". TABLE_CLEANCACHE ." WHERE ".$this->_master_key." = ".$id);
	}
}
