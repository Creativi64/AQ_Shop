<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class xt_startpage_products {

	public function _getParams() {
		$params = array();

        $csrf_param = '&sec='. $_SESSION['admin_user']['admin_key'];
		
		$params['display_GetSelectedBtn'] = false;
		$params['display_checkCol']  	= false;
		$params['display_deleteBtn']  	= false;
		$params['display_editBtn']  	= false;
		$params['display_newBtn']  		= false;
		$params['display_searchPanel']  = false;
		
		$header['text'] 			= array('type'=>'text');
		$params['header']         	= $header;
		$params['master_key']		= 'id';
		$params['default_sort']		= 'id';
		$params['exclude']			= array('status', 'id', 'text');
		
		$tabs = array();
		
		$tabs[] = array(
				'url' => 'adminHandler.php',
				'url_short' => true,
				'params' => "load_section=xt_startpage_attached_products&plugin=xt_startpage_products&shop_id='+edit_id+'".$csrf_param."'+'",
				'title' => TEXT_ATTACHED_PRODUCTS,
		);
		
		$tabs[] = array(
				'url' => 'adminHandler.php',
				'url_short' => true,
				'params' => "load_section=xt_startpage_products_to_shop&plugin=xt_startpage_products&shop_id='+edit_id+'".$csrf_param."'+'",
				'title' => TEXT_ATTACH_PRODUCTS,
		);
		
		$rowActions[] = array('iconCls' => 'ORDER_EDIT_ADD_ITEM', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_ADD_STARTPAGE_PRODUCT);
		$js = "var edit_id = record.id;\n";
		$extF = new ExtFunctions();
		
		//$js.= $extF->_RemoteWindow("TEXT_PRODUCTS_TO_CATEGORIES","TEXT_PRODUCTS",$url , '', array(), 800, 600).' new_window.show();';
		$js.= $extF->_TabRemoteWindow(TEXT_ATTACHED_PRODUCTS, $tabs, 800, 600, 'js') . "new_window.show();";
		
		$rowActionsFunctions['ORDER_EDIT_ADD_ITEM'] = $js;
		
		$params['rowActions']             = $rowActions;
		$params['rowActionsFunctions']    = $rowActionsFunctions;
		
		return $params;
	}
	
	public function setPosition ($position) {
		$this->position = $position;
	}
	
	public function _get($id = 0) {
		global $store_handler;
		$data = $store_handler->getStores();
		$obj = new stdClass;
		// For translations TEXT_STORE_NAME
		foreach ($data as &$array) {
			$array['store_name'] = $array['text'];
		}
		$count_data = count($data);
		$obj->totalCount = $count_data;
		$obj->data = $data;
		
		return $obj;
	}
	
	public function getStartPageProductListing ($category_id, $data) {
		global $xtPlugin, $xtLink, $db, $store_handler;
		
		($plugin_code = $xtPlugin->PluginCode('plugin_startpage_products:getStartPageProductListing_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->sql_products = new getProductSQL_query();
		$this->sql_products->setPosition('plugin_ms_startpage_products');
		//$this->sql_products->setFilter('Language');
		
		$this->sql_products->setSQL_TABLE("RIGHT JOIN " . DB_PREFIX . "_startpage_products spp ON (p.products_id = spp.products_id and spp.shop_id=".$store_handler->shop_id.")");
		//$this->sql_products->setSQL_WHERE("and su.language_code = '" . $lang_code . "' ".$add_to_sql);
		
		if (is_data($_GET['filter_id']))
			$this->sql_products->setFilter('Manufacturer', (int)$_GET['filter_id']);

		if (is_data($_GET['sorting'])) {
			$this->sql_products->setFilter('Sorting', $_GET['sorting']);
		}elseif(is_data($data['sorting'])){
			$this->sql_products->setSQL_SORT($data['sorting']);
		} else {
			$this->sql_products->setSQL_SORT("spp.startpage_products_sort");
		}

		($plugin_code = $xtPlugin->PluginCode('plugin_startpage_products:getStartPageProductListing_query')) ? eval($plugin_code) : false;

		$query = $this->sql_products->getSQL_query();

		$pages = new split_page($query, $data['limit'], $xtLink->_getParams(array ('next_page', 'info')));

		$this->navigation_count = $pages->split_data['count'];
		$this->navigation_pages = $pages->split_data['pages'];

		$count = count($pages->split_data['data']);
		for ($i = 0; $i < $count;$i++) {
			$size = 'default';
			($plugin_code = $xtPlugin->PluginCode('plugin_startpage_products:getStartPageProductListing_size')) ? eval($plugin_code) : false;
			$product = product::getProduct($pages->split_data['data'][$i]['products_id'],$size);
			($plugin_code = $xtPlugin->PluginCode('plugin_startpage_products:getStartPageProductListing_data')) ? eval($plugin_code) : false;
			$module_content[] = $product->data;
		}

		($plugin_code = $xtPlugin->PluginCode('plugin_startpage_products:getStartPageProductListing_bottom')) ? eval($plugin_code) : false;

		return $module_content;
	}
}
