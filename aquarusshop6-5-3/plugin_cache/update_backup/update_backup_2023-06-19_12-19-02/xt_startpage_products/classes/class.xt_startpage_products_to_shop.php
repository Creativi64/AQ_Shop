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

class xt_startpage_products_to_shop extends product {

	function _getParams() {
		global $language;

		$params = array();
		$header['products_id'] = array('type'=>'hidden');
		
		$params['display_GetSelectedBtn'] = false;
		$params['display_checkCol']  = true;
		$params['display_deleteBtn']  = false;
		$params['display_editBtn']  = false;
		$params['display_newBtn']  = false;
		$params['display_searchPanel']  = true;
		
		$fn = new ExtFunctions();
		$UserButtons = array();
		$UserButtons['apply_selection'] = array(
			'text'=>'BUTTON_SELECT', 
			'style'=>'getselected', 
			'icon'=>'accept.png', 
			'acl'=>'edit', 
			//'success_code' => 'Ext.getCmp(\'RemoteWindowTab1\').getStore().load();return;', 
			'func' => 'doGetSelectedCustom', 
			'flag' => 'get_select_flag', 
			'flag_value' => 'true', 
			'stm' => $fn->MsgConfirm(__define("TEXT_SELECT"), 'doGetSelectedCustom')
		);
		$params['display_apply_selectionBtn'] = true;

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
		$params['UserButtons']      = $UserButtons;

		$params['include'] = array ('products_id', 'products_name_'.$language->code, 'products_model', 'products_price', 'products_status');

		return $params;
	}

	function _set($id, $set_type = 'edit') {
		global $db,$language,$filter;
		$obj = new stdClass;
		try {
			$explode = explode(",", trim($this->url_data['m_ids']));
			$explode = array_filter(array_map('trim', $explode));
			
			foreach ($explode as $v){
				$sort = 0;
				$rs = $db->Execute("SELECT MAX(startpage_products_sort) AS N FROM ".DB_PREFIX."_startpage_products 
				                    WHERE shop_id=?",
                                    array((int)$this->url_data['shop_id']));
				
				if($rs->RecordCount() > 0)
					$sort = (int)$rs->fields['N'] + 1;
				
				$db->Execute("INSERT IGNORE INTO ".DB_PREFIX."_startpage_products (shop_id, products_id, startpage_products_sort) VALUES ('".$this->url_data['shop_id']."','$v', '$sort');");
			}
		} catch (Exception $e) {
			$obj->success = false;
		}
		
		$obj->success = true;
		return $obj;
	}
}