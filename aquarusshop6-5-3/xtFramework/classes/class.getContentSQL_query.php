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

class getContentSQL_query extends SQL_query
{

	function __construct() {
		global $xtPlugin;

        parent::__construct();

		$this->setSQL_TABLE(TABLE_CONTENT . " c ");
		$this->setSQL_WHERE(" c.content_id != '' ");

		($plugin_code = $xtPlugin->PluginCode('class.getContentSQL_query.php:getContentSQL_query_bottom')) ? eval($plugin_code) : false;
	}

	function getSQL_query($sql_cols = 'c.content_id', $filter_type='string')
    {
		return parent::getSQL_query($sql_cols, $filter_type);
	}


	//////////////////////////////////////////////////////

	function F_Status () {
		$this->setSQL_WHERE(" and c.content_status = '1'");
	}
	
	function F_Seo($lang_code='') {
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_SEO_URL,'store_id','su.store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_SEO_URL . " su ON (c.content_id = su.link_id and su.link_type='3' ".$add_to_sql.")");
		$this->setSQL_WHERE("and su.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
	}

	function F_Language($lang_code=''){
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;
		
		$add_to_sql = $this->F_StoreID(TABLE_CONTENT_ELEMENTS,'content_store_id','cd.content_store_id');
		
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_CONTENT_ELEMENTS . " cd ON c.content_id = cd.content_id");
		$this->setSQL_WHERE("and cd.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
	}

	function F_GroupCheck () {
		global $customers_status;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($customers_status->customers_status_id)) {

			$perm_array = array(
				array(
					'type'=>'group_permission',
					'key'=>'content_id',
					'simple_permissions' => 'true',
					'simple_permissions_key' => 'permission_id',
					'pref'=>'c'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}

	function F_StoreCheck () {
		global $store_handler;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($store_handler->shop_id)) {
			$perm_array = array(
				array(
					'type'=>'shop',
					'key'=>'content_id',
					'simple_permissions' => 'true',
					'simple_permissions_key' => 'permission_id',
					'pref'=>'c'
				)
			);

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}

	function F_Block ($data = 0) {
		$this->setSQL_TABLE("INNER JOIN " . TABLE_CONTENT_TO_BLOCK . " c2b ON c2b.content_id = c.content_id");
		$this->setSQL_WHERE("and c2b.content_id = ".$data);
	}

	function F_Block_Recursive ($data = 0) {
		$this->setSQL_TABLE("INNER JOIN " . TABLE_CONTENT_TO_BLOCK . " c2b ON c2b.content_id = c.content_id");
		$this->setSQL_WHERE("and ".$data." in (c.content_id, c.content_parent)");
	}

	function F_MultiCheck ($params='') {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
		
	}		
	
	function F_Sorting($sort) {
			switch ($sort) {
			case 'name' :
				$this->setFilter('Language');
				$this->setSQL_SORT(' cd.content_title');
				break;

			case 'name-desc' :
				$this->setFilter('Language');
				$this->setSQL_SORT(' cd.content_title DESC');
				break;

			default:
				return false;
		}
	}
}
