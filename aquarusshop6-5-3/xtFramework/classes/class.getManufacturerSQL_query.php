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

class getManufacturerSQL_query extends SQL_query
{
	function __construct() {
		global $xtPlugin;

        parent::__construct();
		
		$this->setSQL_TABLE(TABLE_MANUFACTURERS . " m ");
		$this->setSQL_WHERE(" m.manufacturers_id != '0'");
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':class_top')) ? eval($plugin_code) : false;

	}

	function getSQL_query($sql_cols = 'm.manufacturers_id', $filter_type ='string') {
		
		if(USER_POSITION=='store')
		    $this->setFilter('StoreCheck');
		
        return parent::getSQL_query($sql_cols);
	}
	
	function F_Seo($lang_code='') {
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_SEO_URL,'store_id','su.store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_SEO_URL . " su ON (m.manufacturers_id = su.link_id and su.link_type='4' ".$add_to_sql.")");
		$this->setSQL_WHERE("and su.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);

	}

	function F_Language($lang_code=''){
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_MANUFACTURERS_DESCRIPTION,'manufacturers_store_id','mi.manufacturers_store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_MANUFACTURERS_DESCRIPTION . " mi ON m.manufacturers_id = mi.manufacturers_id ".$add_to_sql." ");
		$this->setSQL_WHERE("and mi.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
	}

	function F_Sorting($sort='') {
			switch ($sort) {
			default:
			case 'name' :
				$this->setSQL_SORT(' m.manufacturers_name');
				break;

			case 'name-desc' :
				$this->setSQL_SORT(' m.manufacturers_name DESC');
				break;
		}
	}

	function F_StoreCheck () {
		global $store_handler;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($store_handler->shop_id)) {
			$perm_array = array(
				array(
					'type'=>'shop',
					'table'=>TABLE_MANUFACTURERS_PERMISSION,
					'key'=>'manufacturers_id',
					'pref'=>'m'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}	
	
	function F_MultiCheck ($params='') {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
	}		
	
	function F_GroupCheck () {
	 global $customers_status;
	
	 if (_SYSTEM_GROUP_CHECK == 'true' && isset($customers_status->customers_status_id)) {
	
	  $perm_array = array(
		  array(
			  'type'=>'group_permission',
	          'table'=>TABLE_PRODUCTS_PERMISSION,
	          'simple_permissions_table'=>TABLE_CATEGORIES_PERMISSION,
	          'key'=>'products_id',
	          'simple_permissions' => 'true',
	          'simple_permissions_key' => 'permission_id',
	          'pref'=>'p'
		  )
	  );
	
	  $perm = new item_permission($perm_array);
	
	  $this->setSQL_TABLE($perm->_table);
	  $this->setSQL_WHERE($perm->_where);
	 }
	}
}
