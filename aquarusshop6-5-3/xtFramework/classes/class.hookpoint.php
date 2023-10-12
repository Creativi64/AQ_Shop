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

class hookpoint {

	protected $hook_dir = NULL;
	public $hook_ext = '.xplg';
	public $inc_ext = '.xinc';
	public $active_modules = array();
	public $active_modules_id = array();
	public $save_xml_files = false;

	function __construct() {
		global $db, $_SYSTEM_INSTALL_SUCCESS;

		$this->hook_dir = _SRV_WEBROOT._SRV_WEB_PLUGIN_CACHE.'hookpoints/';
		$this->_createHookDir('');

		$this->active = true;
		// load and define configs
		if ($this->active && $_SYSTEM_INSTALL_SUCCESS == 'true') {

			$rs = $db->Execute("SELECT plugin_id,code FROM ".TABLE_PLUGIN_PRODUCTS . " WHERE plugin_status = 1");
			while(!$rs->EOF){
				$this->active_modules[$rs->fields['code']] = 'true';
			//	$this->active_modules[$rs->fields['code']]['plugin_id']= $rs->fields['plugin_id'];
				$this->active_modules_id[$rs->fields['code']] = $rs->fields['plugin_id'];
				$rs->MoveNext();
			}$rs->Close();

			$this->ActiveHooks = array();
			$rs = $db->CacheExecute("SELECT hook FROM ".TABLE_PLUGIN_CODE." WHERE code_status = '1'");
			if ($rs->RecordCount()>0) {
				while (!$rs->EOF) {
					$this->ActiveHooks[]=$rs->fields['hook'];
					$rs->MoveNext();
				}$rs->Close();
				$this->ActiveHooks = array_unique($this->ActiveHooks);

				if (defined('_PRELOAD_PLG_HOOK_CODE') && _PRELOAD_PLG_HOOK_CODE==true) {
          $this->hookCodePreloaded=[];
          $record = $db->getAll("SELECT pc.hook,pc.code,pc.sortorder FROM " . TABLE_PLUGIN_CODE . " pc WHERE pc.code_status = 1 and pc.plugin_status = 1 order by pc.sortorder",false,true);

          foreach ($record as $key => $arr) {
          $this->hookCodePreloaded[$arr['hook']][]=array('code'=>$arr['code'],'sort'=>$arr['sortorder']);
          }
        }


			} else {
				$this->active = false;
			}
		}
	}

	public function getHookDir()
    {
        return $this->hook_dir;
    }


	// Experimental Hooks
	function PluginCode($hook,$plugin_code='') {
		global $InputFilter, $db;

		if (!$this->active || (defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED===true)) {
			return false;
		} elseif($hook == 'admin_dropdown.php:dropdown'){
				$add_to_sql='';
				if ($plugin_code!='') $add_to_sql=" and pc.plugin_code='".$plugin_code."'";
				$record = $db->CacheExecute(
					"SELECT pc.code FROM " . TABLE_PLUGIN_CODE . " pc INNER JOIN " . TABLE_PLUGIN_PRODUCTS . " pp ON pc.plugin_id = pp.plugin_id WHERE pc.hook = ?  and  pp.plugin_status = 1 ".$add_to_sql,
					array($hook)
				);

				if($record->RecordCount() > 0){
					$code = '';
					while(!$record->EOF){
						$code .= $record->fields['code'];
						$record->MoveNext();
					}$record->Close();
					return stripslashes($code);
				}else{
					return false;
				}

	    } elseif ((defined('_SYSTEM_USE_DB_HOOKS') &&_SYSTEM_USE_DB_HOOKS == 'true') || $hook=='_pre_include') {

			if (in_array($hook,$this->ActiveHooks)) {

				if (defined('_PRELOAD_PLG_HOOK_CODE') && _PRELOAD_PLG_HOOK_CODE==true) {

            if (isset($this->hookCodePreloaded[$hook]) && is_array($this->hookCodePreloaded[$hook])) {
              $code='';
              foreach ($this->hookCodePreloaded[$hook] as $arr) {
                $code.=$arr['code'];

              }
              return stripslashes($code);
            }
            return false;

        } else {


				$record = $db->CacheExecute(
						"SELECT pc.code FROM " . TABLE_PLUGIN_CODE . " pc WHERE pc.hook = ? AND pc.code_status = 1 and pc.plugin_status = 1 order by pc.sortorder",
						array($hook)
				);


				if($record->RecordCount() > 0){
					while(!$record->EOF){
						if (!isset($code)) $code ='';
						$code .= $record->fields['code'];
						$record->MoveNext();
					}$record->Close();

					return stripslashes($code);
				}else{
					return false;
				}
			}
			}
		}elseif(defined('_SYSTEM_USE_DB_HOOKS') && _SYSTEM_USE_DB_HOOKS != 'true'){

			if (in_array($hook,$this->ActiveHooks)) {

				$this->_checkHook($hook);
				$hook = $this->filterHook($hook);
				return $this->_loadData($this->hook_dir .$hook.'/');
			}
		}
	}

	function _loadData($path){

		$func_file_extension = $this->hook_ext;
		$func_directory_array = array();
			if ($func_dir = @dir($path)) {
				$code = '';
		    	while ($func_file = $func_dir->read()) {
		      		if (!is_dir($path . $func_file)) {
		        		if (substr($func_file, strrpos($func_file, '.')) == $func_file_extension) {
		        			$code .= file_get_contents($path.$func_file);
		        		}
		      		}
		    	}
		    	$func_dir->close();
		  	}
		return stripslashes($code);
	}

	function filterHook($hook){

		$hook = str_replace(':','_', $hook);
		$hook = str_replace('.','_', $hook);

		return $hook;
	}

	function _checkHookDir($hook){

		$hook = $this->filterHook($hook);

		if(is_dir($this->hook_dir.$hook))
		return true;
		else
		return false;

	}

	function _checkHookFile($hook, $file){
		$hook = $this->filterHook($hook);

		if(is_dir($this->hook_dir.$hook.'/'.$file))
		return true;
		else
		return false;
	}

	function _createHookDir($hook){
		if (_SYSTEM_USE_DB_HOOKS == 'true') return false;
		$hook = $this->filterHook($hook);

		if(!$this->_checkHookDir($hook)){
			mkdir($this->hook_dir.$hook);
			chmod($this->hook_dir.$hook, 0777);
		}
	}

	function _createHookFiles($hook, $id=''){
		global $db;
		if (_SYSTEM_USE_DB_HOOKS == 'true') return false;

		$record = $db->Execute(
			"SELECT pc.plugin_id, pc.code, pc.sortorder  FROM " . TABLE_PLUGIN_CODE . " pc INNER JOIN " . TABLE_PLUGIN_PRODUCTS . " pp ON pc.plugin_id = pp.plugin_id WHERE pc.hook = ? AND pc.code_status = 1 and  pp.plugin_status = 1",
			array($hook)
		);

		if($record->RecordCount() > 0){
			while(!$record->EOF){

				$plugin_code = $this->_getPluginCode($record->fields['plugin_id']);

				if($record->fields['sortorder'])
				$sort = $record->fields['sortorder'];
				else
				$sort = '0';

				$filename = $sort.'_'.$plugin_code;

				$this->_createHookFile($hook, $record->fields['code'], $filename);

				$record->MoveNext();
			}$record->Close();
			return true;
		}else{
			return false;
		}

	}

	function _getPluginCode($id){
		global $db;

		$record = $db->Execute("SELECT code FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE plugin_id = ?", array($id));

		if($record->RecordCount() > 0){
			return $record->fields['code'];
		}else{
			return false;
		}


	}

	function _getPluginStatus($code){
		global $db;

		$record = $db->Execute("SELECT plugin_status FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE code = ?", array($code));

		if($record->RecordCount() > 0){
			if($record->fields['plugin_status']=='1')
				return true;
			else
				return false;
		}else{
			return false;
		}
	}

	function _createHookFile($hook, $data, $file=''){
		if (_SYSTEM_USE_DB_HOOKS == 'true') return false;

		$hook = $this->filterHook($hook);

 		$file = $file.$this->hook_ext;

	    if(!$this->_checkHookDir($hook)){
			$this->_createHookDir($hook);
		}

		$fp = fopen($this->hook_dir.$hook.'/'.$file, "w+");
	    fputs($fp, $data);
	    fclose($fp);
		chmod($this->hook_dir.$hook.'/'.$file, 0777);

	}

    function _createHookFileXML($hook, $data, $file=''){
		if (_SYSTEM_USE_DB_HOOKS == 'true') return false;
        if($this->save_xml_files != true) {
          return;
        }

        $hook = $this->filterHook($hook);

        $file = $file.'.xml';

        if(!$this->_checkHookDir($hook)){
            $this->_createHookDir($hook);
        }
        $fp = fopen($this->hook_dir.$hook.'/'.$file, "w+");
        fputs($fp, "\n".'<code>');
        fputs($fp, "\n".'<hook>'.trim($data['hook']).'</hook>');
        fputs($fp, "\n".'<phpcode><![CDATA[');

        fputs($fp, "\n".$data['code']);

        fputs($fp, "\n".']]></phpcode>');
        fputs($fp, "\n".'<order>'.trim($data['sortorder']).'</order>');
        fputs($fp, "\n".'<active>'.trim($data['code_status']).'</active>');
        fputs($fp, "\n".'</code>');
        fclose($fp);
        chmod($this->hook_dir.$hook.'/'.$file, 0777);

    }

	function _checkHook($hook){
		if (_SYSTEM_USE_DB_HOOKS == 'true') return false;

		if(!$this->_checkHookDir($hook)){
			$this->_createHookDir($hook);
			$this->_createHookFiles($hook);
		}
	}

	/**
	 * delete hook file
	 *
	 * @param string $hook
	 * @param string $file
	 */
	function _deleteHookFile($hook, $file){

		$hook = $this->filterHook($hook);

		$file = $file.$this->hook_ext;
		if (file_exists($file)) {
			unlink($this->hook_dir.$hook.'/'.$file);
		}

	}

	function _deleteHookFiles($id){
		global $db;

		$record = $db->Execute(
			"SELECT p.code, pc.sortorder, pc.hook FROM " . TABLE_PLUGIN_PRODUCTS . " p, " . TABLE_PLUGIN_CODE . " pc WHERE pc.id = ? and p.plugin_id = pc.plugin_id ",
			array($id)
		);
		if($record->RecordCount() > 0){

		  $hook = trim($this->filterHook($record->fields['hook']));

           if ($func_dir = @dir($this->hook_dir.$hook)) {
		    	while ($func_file = $func_dir->read()) {
		      		if (!is_dir($this->hook_dir.$hook . $func_file)) {
		        		if (substr($func_file, strrpos($func_file, '.')) == $this->hook_ext) {
								if(strstr($func_file, $record->fields['code']))
                                unlink($this->hook_dir.$hook.'/'.$func_file);
		        		}

                        // UBo
                        if($this->save_xml_files = true) {
                            if (substr($func_file, strrpos($func_file, '.')) == '.xml') {
                                if(strstr($func_file, $record->fields['code']))
                                unlink($this->hook_dir.$hook.'/'.$func_file);
                            }
                        }
                    }
		    	}
		    	$func_dir->close();
		  	}
		}
	}

	function _MultiDeleteHookFiles($plugin_id){
		global $db;

		$record = $db->Execute("SELECT id FROM " . TABLE_PLUGIN_CODE . " pc WHERE plugin_id = ?", array($plugin_id));
		if($record->RecordCount() > 0){
			while(!$record->EOF){

				$this->_deleteHookFiles($record->fields['id']);

				$record->MoveNext();
			}$record->Close();
		}
	}


	/**
	 * deactivate a plugin if license file is missing and inform shop admin about deactivation
	 * @param $plugin
	 */
	function NoLicenceDeactivatePlugin($plugin) {
		global $db,$logHandler;

		// deactivate plugin in database
		$db->Execute("update " . TABLE_PLUGIN_PRODUCTS . " set plugin_status = '0' where code = ?", array($plugin));

		// add entry into systemlog
		$log_data = array();
		$log_data['deactivation_time'] = microtime();
		$logHandler->_addLog('missing_licence',$plugin,'',$log_data);

		// send mail to admin
		mail(_CORE_DEBUG_MAIL_ADDRESS,'Missing Plugin Licence - xt:Commerce','Plugin '.$plugin.' has beed deactivated due missing/wrong Licence File - Please contact xt:Commerce Support.');

		die('Plugin '.$plugin.' deactivated - missing/wrong Licence File.');
	}

	function _MultiCreateHookFiles($plugin_id){
		global $db;

		$record = $db->Execute("SELECT * FROM " . TABLE_PLUGIN_CODE . " WHERE plugin_id = ?", array($plugin_id));
		if($record->RecordCount() > 0){
			while(!$record->EOF){

				$plugin_code = $this->_getPluginCode($record->fields['plugin_id']);

				if($record->fields['sortorder'])
				$sort = $record->fields['sortorder'];
				else
				$sort = '0';

				$filename = $sort.'_'.$plugin_code;

				$this->_createHookFile($record->fields['hook'], $record->fields['code'], $filename);

				$record->MoveNext();
			}$record->Close();
		}
	}
}
