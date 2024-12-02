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

class plugin_installed extends plugin{

	protected $_table = TABLE_PLUGIN_PRODUCTS;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'plugin_id';
	
	protected $_approvedPluginDevelopers = array(
		"xt:Commerce",
	);

	function _setPluginId() {
		if($this->url_data['edit_id'])
		$this->_plugin_id = (int)$this->url_data['edit_id'];
	}

	function _getParams() {
		global $language,$store_handler,$xtPlugin;

		$this->_setPluginId();

		$params = array();

        $csrf_param = '&sec='. $_SESSION['admin_user']['admin_key'];

		if ($this->url_data['edit_id'])
			$jsEditId = "var edit_id = ".$this->url_data['edit_id'].";";
		else
			$jsEditId = "var edit_id = record.id;";
		
        if (_SYSTEM_DEMO_MODE=='false') {
		    $rowActions[] = array('iconCls' => 'plugin_hookpoints', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_PLUGIN_HOOKPOINTS'));
            $rowActionsFunctions['plugin_hookpoints'] = $jsEditId."addTab('adminHandler.php?load_section=plugin_hookpoints&pg=overview&pHID='+edit_id,'".__text('TEXT_PLUGIN_HOOKPOINTS')."')";
        }
        
        if ($this->url_data['edit_id']) {
        	list($documentation_link, $marketplace_link) = $this->getPluginLinks($this->url_data['edit_id']);
        	
        	if (!empty($marketplace_link)) {
		        $rowActions[] = array('iconCls' => 'marketplace_link', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_MARKETPLACE_LINK'));
		        $rowActionsFunctions['marketplace_link'] = "window.open('$marketplace_link','_blank');";
        	}
	        
        	if (!empty($documentation_link)) {
		        $rowActions[] = array('iconCls' => 'documentation_link', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_DOCUMENTATION_LINK'));
		        $rowActionsFunctions['documentation_link'] = "window.open('$documentation_link','_blank');";
        	}
        }
		$extF = new ExtFunctions();

        // plugin changelog
        $rowActions[] = array('iconCls' => 'PLUGIN_CHANGELOG', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_PLUGIN_CHANGELOG'));

        $rowActionsFunctions['PLUGIN_CHANGELOG'] =  $jsEditId."Ext.Msg.show({
	    										title: 'Plugin-Changelog',
	    										msg: 'Plugin Changelog anzeigen?',
	    										buttons: Ext.Msg.YESNOCANCEL,
	    										fn: function(btn){getChangelog(edit_id,btn);}
	    										})";
        $js = "function getChangelog(edit_id,btn){".
            "if (btn == 'yes') {"
            .$extF->_RemoteWindow("TEXT_PLUGIN_CHANGELOG","TEXT_PLUGIN_CHANGELOG","plugin_install.php?query=changelog&plugin_id='+edit_id+'", '', array(), 800, 600).' new_window.show();'.
            "}".
            "if (btn == 'no') {

			}".
            "};";
        // plugin update

	    $rowActions[] = array('iconCls' => 'update_plugin', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_PLUGIN_UPDATE'));
	    $rowActionsFunctions['update_plugin'] =  $jsEditId."Ext.Msg.show({
	    										title: 'Pluginupdate',
	    										msg: 'Plugin update?',
	    										buttons: Ext.Msg.YESNOCANCEL,
	    										fn: function(btn){doUpdate(edit_id,btn);}
	    										})";

		$js .= "function doUpdate(edit_id,btn){".
	  		"if (btn == 'yes') {"
	  		.$extF->_RemoteWindow("TEXT_UPDATE_PLUGIN","TEXT_UPDATE_PLUGIN","plugin_install.php?save=true&plugin_id='+edit_id+'", '', array(), 800, 600).' new_window.show();'.
			"}".
			"if (btn == 'no') {

			}".
		"};";

        if (_SYSTEM_DEBUG_FINAL == 'true') {
		$jsDelete="var record_ids = edit_id;".
				  	"if (btn=='yes'){deleteSql=true;}".
					"if (btn=='no'){deleteSql=false;}".
					"if (btn != 'cancel') {".
					"var conn = new Ext.data.Connection();".
					" conn.request({".
					" url: 'adminHandler.php?load_section=plugin_installed', ".
					" method:'GET',".
					" params: {'m_ids': record_ids, multiFlag_unset:'true',deleteSql: deleteSql},".
					" success: function(responseObject) { plugin_installedds.reload(); },".
					" waitMsg: '".__text('TEXT_LOADING')."',".
					" failure: function(){ ".$extF->MsgAlert(__text('TEXT_FAILURE'))." } ".
					" });".
					"}";
		
	    $rowActions[] = array('iconCls' => 'deletePlugin', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_PLUGIN_DELETE'));
	    $rowActionsFunctions['deletePlugin'] =  $jsEditId."Ext.Msg.show({
	    										title: '".__text('TEXT_PLUGIN_DELETE')."',
	    										msg: '".__text('TEXT_PLUGIN_SQL_DELETE')."',
	    										buttons: Ext.Msg.YESNOCANCEL,
	    										fn: function(btn){
	    											".$jsDelete."
	    										}
	    										})";
	  
	    
        }
	    
        $jsUpdate="var record_ids = edit_id;".
        		"if (btn == 'yes') {".
        		"var conn = new Ext.data.Connection();".
        		" conn.request({".
        		" url: 'adminHandler.php?load_section=plugin_installed&pg=updateTranslations".$csrf_param."', ".
        		" method:'GET',".
        		" params: {'plugin_id': record_ids},".
        		" success: function(responseObject) { plugin_installedds.reload(); },".
        		" waitMsg: '".__text("TEXT_LOADING")."',".
        		" failure: function(){ ".$extF->MsgAlert(__text("TEXT_FAILURE"))." } ".
        		" });".
        		"}";
        $rowActions[] = array('iconCls' => 'update_translations', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_UPDATE_TRANSLATIONS'));
        $rowActionsFunctions['update_translations'] =  $jsEditId."Ext.Msg.show({
	    										title: '".__text('TEXT_PLUGIN_UPDATE')."',
	    										msg: '".__text('TEXT_PLUGIN_UPDATE_TRANSLATIONS')."',
	    										buttons: Ext.Msg.YESNOCANCEL,
	    										fn: function(btn){
	    											".$jsUpdate."
	    										}
	    										})";
        
		$params['rowActionsJavascript'] = $js;
        	
		($plugin_code = $xtPlugin->PluginCode('class.plugin_installed.php:_getParams_bottom')) ? eval($plugin_code) : false;

		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;

		$params['default_sort']   = 'type';

		$params['GroupField']     = 'type';
		$params['SortField']      = 'type';
		$params['SortDir']        = 'ASC';

		$pageSize = (int)_SYSTEM_ADMIN_PAGE_SIZE_PLUGIN;
		if($pageSize && is_int($pageSize)) $params['PageSize'] = $pageSize;

		$header['plugin_id'] = array('type' => 'hidden');

		$header['name'] = array('readonly' => true);
		$header['code'] = array('readonly' => true);

		//$header['active'] = array('type' => 'dropdown', 'url'  => 'DropdownData.php?get=status_truefalse');

		$header['db_install'] = array('type' => 'textarea');
		$header['db_uninstall'] = array('type' => 'textarea');

		//$header['active'] = array('renderer' => 'status');


		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('plugin_id','plugin_status', 'name', 'version', 'version_available', 'code', 'url','type', 'developer');
		}else{

			$edit_data = $this->getConfigHeaderData();

			$params['include'] = array ('plugin_id','name','code', 'plugin_status');

			if (count($edit_data['header'])>0) {
			$header = array_merge($header,$edit_data['header']);
			foreach ($edit_data['header'] as $key => $arr) {
				$params['include'] = array_merge($params['include'],array($key));
			}

			$params['grouping'] = $edit_data['grouping'];
			$params['panelSettings']  = $edit_data['panelSettings'];
			}

		}

		$params['display_newBtn'] = false;
		if (_SYSTEM_DEBUG_FINAL == 'true') {
		$params['display_deleteBtn'] = false;
		}
        $params['display_searchPanel']  = true;
		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;

		return $params;
	}
	
	public function updateTranslations() {
		global $db;
		$obj = new stdClass();
		$obj->success = false;
		
		$plugin_id = $this->url_data['plugin_id'];
		
		$query = "SELECT * FROM " . TABLE_PLUGIN_PRODUCTS ." where plugin_id = ?";
		
		$res = $db->Execute($query, [$plugin_id]);
		if ($res->RecordCount()>0) {
			$code = $res->fields['code'];
			
			// check for _lng.xml file
			$lng_xml = _SRV_WEBROOT . _SRV_WEB_PLUGINS . $code . DIRECTORY_SEPARATOR . "installer" . DIRECTORY_SEPARATOR . $code . "_lng.xml";
			$xml_data = array('xtcommerceplugin' => array('language_content'));
			require_once _SRV_WEBROOT . 'xtFramework/classes/class.language_sync.php';
			
			$lang_sync = new language_sync();
			$lang_sync->downloadPluginTranslations($code);
			
			if (is_file($lng_xml)) {
				$lng_xml_data = $this->xmlToArray($lng_xml);
					
				if (isset($lng_xml_data['xtcommerceplugin']['language_content']['phrase'])) {
					$xml_data['xtcommerceplugin']['language_content']=$lng_xml_data['xtcommerceplugin']['language_content'];
				}
			}
			
			// language vars ?
			if (isset($xml_data['xtcommerceplugin']['language_content']['phrase']) && is_array($xml_data['xtcommerceplugin']['language_content']['phrase'])) {
				if (is_array($xml_data['xtcommerceplugin']['language_content']['phrase'][0])) {
					foreach ($xml_data['xtcommerceplugin']['language_content']['phrase'] as $key => $val) {
						$this->debug_output .=$this->_addLangContent($code,$val);
					}
				} else {
					$val = $xml_data['xtcommerceplugin']['language_content']['phrase'];
					$this->debug_output .=$this->_addLangContent($code,$val);
				}
			}

            self::clearLanguageCache();
		}
	}
	
	/**
	 * Get plugin links
	 * @param unknown $plugin_id
	 * @return multitype:NULL |multitype:string
	 */
	protected function getPluginLinks($plugin_id) {
		global $db;
		$query = "SELECT `documentation_link`, `marketplace_link`, `developer` FROM `" . TABLE_PLUGIN_PRODUCTS . "` WHERE `plugin_id`=? LIMIT 1";
		$res = $db->Execute($query, array($plugin_id));
		
		if ($res->RecordCount() > 0) {
			$documentation_link = $res->fields['documentation_link'];
			$marketplace_link = $res->fields['marketplace_link']; 
			
			if (strpos($marketplace_link, "addons.xt-commerce.com") === false) {
				$marketplace_link = "";
			}
			
			if (!in_array($res->fields['developer'], $this->_approvedPluginDevelopers)) {
				$documentation_link = "";
			}
			
			return array($documentation_link, $marketplace_link);
		}
		
		return array('', '');
	}

    function _getSearchSQL_Where($search_data) {
        global $filter;

        $sql_tablecols = array('name','description','plugin_id','version','url','code');

        foreach ($sql_tablecols as $tablecol) {
            $sql_where[]= "(".$tablecol." LIKE '%".$filter->_filter($search_data)."%')";
        }

        $sql_where_str = "(".implode(' or ', $sql_where).")";

        return $sql_where_str;
    }

	function _get($ID = 0) {
		global $xtPlugin, $db, $language,$store_handler;
		$obj = new stdClass;
		$stores = $store_handler->getStores();

		$ID = (int)$ID;

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}  elseif($ID!=0) {

			// query for config values
			foreach ($stores as $sdata) {
				$query = "SELECT * FROM " . TABLE_PLUGIN_CONFIGURATION . " where plugin_id = ? and shop_id=? order by sort_order ASC";
				$rs = $db->Execute($query, array($ID, $sdata['id']));

				while (!$rs->EOF) {
					$conf_data['conf_'.$rs->fields['config_key'].'_shop_'.$sdata['id']]=$rs->fields['config_value'];
					$rs->MoveNext();
				}$rs->Close();

			}
		}
        //build where
        if(isset($this->url_data['query']) && strlen(trim($this->url_data['query']))>1){
            $this->sql_where = $this->_getSearchSQL_Where($this->url_data['query']);
            $this->sql_where .= ' GROUP BY plugin_id';
        }else{
            $this->sql_where = "";
        }

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $this->sql_where);

		if ($this->url_data['get_data'])
		$data = $table_data->getData();
		elseif($ID)
		$data = $table_data->getData($ID);
		else
		$data = $table_data->getHeader();

		if (is_array($conf_data)) $data[0] = array_merge($data[0],$conf_data);

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db, $language, $filter, $xtPlugin;

		if ($this->position != 'admin') return false;

        ($plugin_code = $xtPlugin->PluginCode('class.plugin_installed.php:_set_top')) ? eval($plugin_code) : false;

		if(!_SYSTEM_USE_DB_HOOKS){

			if(!$data['plugin_status'] || $data['plugin_status']!='1'){
				$xtPlugin->_MultiDeleteHookFiles($data['plugin_id']);
			}else{
				$xtPlugin->_MultiCreateHookFiles($data['plugin_id']);
			}
		}

		$obj = new stdClass;
		$oP = new adminDB_DataSave(TABLE_PLUGIN_PRODUCTS, $data, false, __CLASS__);
		$obj = $oP->saveDataSet();
		
		if ($data['plugin_status']!='1') {
			$data['plugin_status']='0';
			$db->Execute("UPDATE " . TABLE_PAYMENT . " SET status = 0 WHERE plugin_installed = ?",array($data['plugin_id']));
		}
		$db->Execute(
				"update " . TABLE_PLUGIN_CODE . " set plugin_status = ? where ".$this->_master_key." = ?",
				array($data['plugin_status'], $data['plugin_id'])
		);

		$this->setPluginConfig($data);
		($plugin_code = $xtPlugin->PluginCode('class.plugin_installed.php:_set_bottom')) ? eval($plugin_code) : false;
		return $obj;
	}

	function _unset($id = 0) {
		global $db;

		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id = (int)$id;

		$this->DeletePlugin($id);
	}

	function _setStatus($id, $status, $is_installer = false) {
		global $db,$xtPlugin, $is_pro_version;

		($plugin_code = $xtPlugin->PluginCode('class.plugin_installed.php:_setStatus_top')) ? eval($plugin_code) : false;

        $iskco = $db->GetOne('SELECT 1 FROM ' . TABLE_PLUGIN_PRODUCTS . ' WHERE plugin_id=? AND code=?', array($id, 'xt_klarna_kco'));
        if($iskco)
        {
            if (!$is_pro_version)
            {
                if (!$is_installer && $status == 0 && !array_key_exists('pluginHistoryId', $_REQUEST))
                {
                    throw new Exception(__text('ERROR_PLUGIN_CANNOT_BE_CHANGED'));
                }
                $status = 1;
            }
            else
            {
                if ($status == 1 && !array_key_exists('pluginHistoryId', $_REQUEST))
                {
                    throw new Exception(__text('TEXT_ERROR_PLUGIN_ONLY_IN_FREE_VERSION'));
                }
                $status = 0;
            }
        }

        global $is_pro_version;
        $plgLicType = $this->getRequiredStoreLicenseTypeByPluginId($id);
        $plgLicTypeError = false;
        if($status == 1)
        {
            if ($is_pro_version && $plgLicType == 'FREE')
            {
                $plgLicTypeError = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_FREE_VERSION'));
            }
            else if (!$is_pro_version && $plgLicType == 'PRO')
            {
                $plgLicTypeError = sprintf(__text('TEXT_ERROR_PLUGIN_ONLY_IN_PRO_VERSION'));
            }
        }
        if($plgLicTypeError)
        {
            throw new Exception($plgLicTypeError);
        }
		
		$id = (int)$id;
		if (!is_int($id)) return false;
		
		
		if(!_SYSTEM_USE_DB_HOOKS){
				$xtPlugin->_MultiDeleteHookFiles($id);
			if($status=='1'){
				$xtPlugin->_MultiCreateHookFiles($id);
			}
		}
		
		if($status == 0) // when deactivating plg  deactivate plg_status in hooks table first
		{
			$db->Execute(
				"update " . TABLE_PLUGIN_CODE . " set plugin_status = ? where ".$this->_master_key." = ?",
				array($status, $id)
			);
		}
		
		$db->Execute(
			"update " . $this->_table . " set plugin_status = ? where ".$this->_master_key." = ?",
			array($status, $id)
		);
		
		if($status == 1) // on activate plg  activate plg_status in hooks table after plg activation
		{
		$db->Execute(
				"update " . TABLE_PLUGIN_CODE . " set plugin_status = ? where ".$this->_master_key." = ?",
				array($status, $id)
		);
		}

		if(!$status)
			$db->Execute("UPDATE " . TABLE_PAYMENT . " SET status = 0 WHERE plugin_installed = ?",array($id));
		
		($plugin_code = $xtPlugin->PluginCode('class.plugin_installed.php:_setStatus_bottom')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

        xt_cache::deleteCache('language_content');
        $db->cacheFlush();
	}
}