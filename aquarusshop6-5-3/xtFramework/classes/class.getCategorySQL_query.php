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

class getCategorySQL_query extends SQL_query
{
	protected $_selectTables = array('c.*');
	
	function __construct() {
		global $xtPlugin;

        parent::__construct();

		$this->setSQL_TABLE(TABLE_CATEGORIES . " c ");
		$this->setSQL_WHERE(" c.categories_status = '1'");
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':class_top')) ? eval($plugin_code) : false;
	}

	function getSQL_query($sql_cols = 'c.categories_id', $filter_type ='string') {
		global $xtPlugin;
		
		if (USER_POSITION =='store') {
			$this->setFilter('GroupCheck');
			$this->setFilter('StoreCheck');
	    }
		
	    ($plugin_code = $xtPlugin->PluginCode('class.category_sql_query.php:getSQL_query_filter')) ? eval($plugin_code) : false;
	    
		return parent::getSQL_query($sql_cols);
	}
	
	function F_Seo($lang_code='') {
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_SEO_URL,'store_id','su.store_id');
        $add_to_sql2 = $this->F_StoreID(TABLE_CATEGORIES_CUSTOM_LINK_URL,'store_id','cl.store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_SEO_URL . " su ON (c.categories_id = su.link_id and su.link_type='2' ".$add_to_sql.")");
        $this->setSQL_TABLE("LEFT JOIN " . TABLE_CATEGORIES_CUSTOM_LINK_URL . " cl ON (cl.categories_id = c.categories_id ".$add_to_sql2.")");
		$this->setSQL_WHERE("and ((c.category_custom_link=0 and su.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql .") ||
		                          (c.category_custom_link=1 and cl.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql2 .")
		                          )
		");
		$this->_selectTables[] = 'su.*';
        $this->_selectTables[] = 'cl.link_url';

	}

	function F_Language($lang_code=''){
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_CATEGORIES_DESCRIPTION,'categories_store_id','cd.categories_store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON c.categories_id = cd.categories_id ".$add_to_sql);
		$this->setSQL_WHERE("and cd.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
		$this->_selectTables[] = 'cd.*';
	}

	function F_GroupCheck () {
		global $customers_status;

		if (_SYSTEM_GROUP_CHECK == 'true') {
			$perm_array = array(
				array(
					'type' => 'group_permission',
					'table' => TABLE_CATEGORIES_PERMISSION,
					'simple_permissions_table'=>TABLE_CATEGORIES_PERMISSION,
					'key' => 'categories_id',
					'simple_permissions' => 'true',
					'simple_permissions_key' => 'categories_id',
					'pref' => 'c'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
			
			$this->_selectTables[] = 'group_permission.*';
		}
	}

	function F_StoreCheck () {
		global $store_handler;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($store_handler->shop_id)) {
			$perm_array = array(
				array(
					'type'=>'shop',
					'table'=>TABLE_CATEGORIES_PERMISSION,
					'simple_permissions_table'=>TABLE_CATEGORIES_PERMISSION,
					'key'=>'categories_id',
					'simple_permissions' => 'true',
					'simple_permissions_key' => 'categories_id',
					'pref'=>'c'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
			$this->_selectTables[] = 'shop.*';
		}
	}	
	
	function F_MultiCheck ($params='') {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
	}	
	
	function F_Sorting($sort) {
			switch ($sort) {

			case 'name' :
				$this->setSQL_SORT(' cd.categories_name');
				break;

			case 'name-desc' :
				$this->setSQL_SORT(' cd.categories_name DESC');
				break;

			case 'sort_order' :
					$this->setSQL_SORT(' c.sort_order');
				break;

			case 'sort_order-desc' :
					$this->setSQL_SORT(' c.sort_order DESC');
				break;

			default:
				return false;
		}
	}
}
