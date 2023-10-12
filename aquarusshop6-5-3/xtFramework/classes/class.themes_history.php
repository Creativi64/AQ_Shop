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

defined ( '_VALID_CALL' ) or die ( 'Direct Access is not allowed.' );


class themes_history extends xt_backend_cls {
	
	var $master_id = 'file';
	
	function _getParams() {
		global $language;
		
		$params = array ();
		
		$params ['header'] = array ();
		$params ['master_key'] = $this->master_id;
		$params ['default_sort'] = 'file_time';
		
		$params ['SortField'] = 'file_time';
		$params ['SortDir'] = 'DESC';
		
		
		if($this->url_data['pg']=='overview'){
			$params['include'] = array ('id','file', 'file_time', 'code', 'url');
		}

        $rowActions[] = array('iconCls' => 'start', 'qtipIndex' => 'qtip1', 'tooltip' => 'Run');

        if(!$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $js = "var edit_id = record.get('file');";
            $js .= "var edit_theme = record.get('theme');";

            $js .= "Ext.Msg.show({
			   title:'" . __text('TEXT_THEME_RESTORE') . "',
			   msg: '" . __text('TEXT_THEME_RESTORE_DIALOG') . "',
			   buttons: Ext.Msg.YESNO,
			   animEl: 'elId',
				 fn: function(btn) {if (btn == 'yes') {addTab('row_actions.php?type=restore_theme&seckey=" . _SYSTEM_SECURITY_KEY . "&id='+edit_id+'&theme='+edit_theme,'".__text('TEXT_THEME_RESTORE')."');}},
			   icon: Ext.MessageBox.QUESTION
			});";

            $rowActionsFunctions['start'] = $js;

            $js = "function runImport(edit_id,btn){
	  		var edit_id = edit_id;
	  		if (btn == 'yes') {
	  			addTab('row_actions.php?type=api_csv_export&seckey="._SYSTEM_SECURITY_KEY."&id='+edit_id,'".__text('TEXT_THEME_RESTORE')."');  
			}

		};";

            $params['rowActionsJavascript'] = $js;

            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }
		
		$params ['display_searchPanel'] = false;
		$params ['display_newBtn'] = false;
		$params ['display_editBtn'] = false;
		$params ['display_deleteBtn'] = false;

		
		return $params;
	}
	
	function _get($pID = 0) {
		if ($this->position != 'admin')
			return false;
		
		if ($pID === 'new') {
			$obj = $this->_set ( array (), 'new' );
			$pID = $obj->new_id;
		}
		
		$data = $this->getThemesHistory ($this->url_data['theme_dir']);

				
		$obj = new stdClass;
		$obj->totalCount = count($data);
		
		if($obj->totalCount==0){	
			$data[] =  array('id'=>'', 'file'=>'', 'file_time'=>'');
		}
		
		$obj->data = $data;
		return $obj;
	}
	
	function _set($data, $set_type='edit') {
		
		
		
		
	}
	
	
	/**
	 * load themes from template directory
	 * @param string $theme_code
	 * @return Ambigous <multitype:multitype:NULL unknown string  , unknown>
	 */
	private function getThemesHistory($theme_code = '') {
		global $db;
		
		$theme = $theme_code;
		$param = '/[^a-zA-Z0-9_-]/';
		$theme = preg_replace($param, '', $theme);
		// check if theme directory exists
		$theme_path = _SRV_WEBROOT . 'templates/' . $theme.'/less/';
		
		if (!is_dir($theme_path)) {
			return false;
		}
		
		$files =  scandir($theme_path);
		$history_files = array();
		$id = 0;
		if (count($files)>0) {
			
			foreach ($files as $fileid => $file ) {
				if (strstr($file,'org')) {
					$id++;
					$filetime=filemtime ( $theme_path.$file );
					$history_files[]=array('id'=>$id,'file'=>$file,'file_time'=>date ("d.m.Y H:i:s.", $filetime),'theme'=>$theme_code);
				}
				
			}
			
		}
		return $history_files;
	}
	
	
}