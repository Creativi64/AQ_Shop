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
#[AllowDynamicProperties]
class getProductSQL_query extends SQL_query
{
    public array $config = [];
	function __construct($config = []) {
		global $xtPlugin;

		parent::__construct();

        $this->config = $config;

		$this->setSQL_TABLE(TABLE_PRODUCTS . " p ");
		$this->setSQL_WHERE(" p.products_id != '' ");
		($plugin_code = $xtPlugin->PluginCode('class.getProductSQL_query.php:getProductSQL_query_bottom')) ? eval($plugin_code) : false;
	}

	function getSQL_query($sql_cols = 'p.products_id', $filter_type='string') {

	    if ($this->user_position =='store') {
			$this->setFilter('GroupCheck');
			$this->setFilter('StoreCheck');
			$this->setFilter('Fsk18');
			$this->setFilter('Status');
			$this->setFilter('Seo');
			$this->setFilter('Listing');
			if (_STORE_STOCK_CHECK_DISPLAY=='false' && _SYSTEM_STOCK_HANDLING=='true') {
				$this->setFilter('Stock');
			}
	    }
		return parent::getSQL_query($sql_cols, $filter_type);
	}


	//////////////////////////////////////////////////////
	// products filter function

	function F_Status () {
		$this->setSQL_WHERE(" and p.products_status = '1'");
	}

	function F_Stock () {
		$this->setSQL_WHERE(" and p.products_quantity > 0");
	}

	function F_Seo($lang_code='') {
		global $db, $language;

		if(empty($lang_code))
			$lang_code = $language->code;

		$add_to_sql = $this->F_StoreID(TABLE_SEO_URL,'store_id','su.store_id');
		
	//	$this->setSQL_TABLE("LEFT JOIN " . TABLE_SEO_URL . " su ON (p.products_id = su.link_id and su.link_type='1' ".$add_to_sql.")");
	//	$this->setSQL_WHERE("and su.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
		
		// more performance query
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_SEO_URL . " su ON (p.products_id = su.link_id and su.link_type='1' ".$add_to_sql." and su.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql.")");

        $this->setSQL_WHERE(" and su.link_id = p.products_id ");
	}

	function F_Language($lang_code=''){
		global $db, $language, $store_handler;

		if(empty($lang_code))
			$lang_code = $language->code;

        $store_id = $store_handler->shop_id;

		//$add_to_sql = $this->F_StoreID(TABLE_PRODUCTS_DESCRIPTION,'products_store_id','pd.products_store_id');
		$this->setSQL_TABLE("LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id and pd.language_code = ".$db->Quote($lang_code) . " and pd.products_store_id = ".$db->Quote($store_id) . " ");
		//$this->setSQL_WHERE("and pd.language_code = " . $db->Quote($lang_code) . " ".$add_to_sql);
	}

	function F_Manufacturer ($man_id = 0) {
	    if (!isset($man_id) || empty($man_id)) return;
		$this->setSQL_WHERE("and p.manufacturers_id = '".$man_id."'");
	}
	function F_Startpage () {
		global $store_handler, $db;
        $store_id = (int)($store_handler->shop_id);
		$this->setSQL_WHERE("and p.products_startpage_" . $store_id ."=1 ");
	}
	function F_GroupCheck () {
		global $customers_status, $category;

		if (_SYSTEM_GROUP_CHECK == 'true' && isset($customers_status->customers_status_id)) {

			$perm_array = array(array(
				'type'=>'group_permission',
				'table'=>TABLE_PRODUCTS_PERMISSION,
				'simple_permissions_table'=>TABLE_CATEGORIES_PERMISSION,
				'key'=>'products_id',
				'simple_permissions' => _SYSTEM_SIMPLE_GROUP_PERMISSIONS,
				'simple_permissions_key' => 'products_id',
				'pref'=>'p'
			));

			if($category && isset($category->level[1])){
				$perm_array[0]['id']  = $category->level[1];
			}


			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}
	
	function F_StoreCheck () {
		global $store_handler, $category;



		if (_SYSTEM_GROUP_CHECK == 'true' && isset($store_handler->shop_id)) {
			$perm_array = array(array(
				'type'=>'shop',
				'table'=>TABLE_PRODUCTS_PERMISSION,
				'simple_permissions_table'=>TABLE_CATEGORIES_PERMISSION,
				'key'=>'products_id',
				'simple_permissions' => _SYSTEM_SIMPLE_GROUP_PERMISSIONS,
				'simple_permissions_key' => 'products_id',
				'pref'=>'p'
			));

			if($category && isset($category->level[1])){
				$perm_array[0]['id']  = $category->level[1];
			}

			$perm = new item_permission($perm_array);

			$this->setSQL_TABLE($perm->_table);
			$this->setSQL_WHERE($perm->_where);
		}
	}		
	
	function F_MultiCheck ($params='') {
		global $xtPlugin;

	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':F_MultiCheck')) ? eval($plugin_code) : false;
	}		
	
	function F_Fsk18 () {
		global $customers_status;

		if ($customers_status->customers_fsk18_display == '0') {
			$this->setSQL_WHERE("and p.products_fsk18!=1");
		}
	}

	function F_Listing () {
		global $xtPlugin;

		if($this->position != 'product_info')
		($plugin_code = $xtPlugin->PluginCode('class.getProductSQL_query.php:F_Listing')) ? eval($plugin_code) : false;

	}

	function F_Categorie ($data = 0) {
		
		$add_to_sql = $this->F_StoreID(TABLE_PRODUCTS_TO_CATEGORIES,'store_id','p2c.store_id');
		
		$this->setSQL_TABLE("INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id ".$add_to_sql);
		$this->setSQL_WHERE("and p2c.categories_id = ".$data.$add_to_sql);
	}
	function F_Categorie_Recursive ($data = 0) {
		
		$add_to_sql = $this->F_StoreID(TABLE_PRODUCTS_TO_CATEGORIES,'store_id','p2c.store_id');
		
		$this->setSQL_TABLE("INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id ".$add_to_sql." LEFT JOIN ".TABLE_CATEGORIES." c ON p2c.categories_id = c.categories_id");
		$this->setSQL_WHERE("and ".$data." in (c.categories_id, c.parent_id) ".$add_to_sql);
	}
	function F_Sorting($sort) {
		global $customers_status,$xtPlugin,$language, $db;

		$cust_status_id = (int) $customers_status->customers_status_id;
		$lang_code = $db->Quote($language->content_language);
		$add_unique_sort_field = ", p.products_id";
			switch ($sort) {


			case 'price' :
                $data = date("Y-m-d H:i:s", mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')));
				if (_SYSTEM_GROUP_CHECK == 'true') {
					$this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_PRICE_SPECIAL.' pps ON p.products_id = pps.products_id'." and (pps.date_available <= '" . $data . "' or pps.date_available = 0) and (pps.date_expired >= '" . $data . "'  or pps.date_expired = 0) and (pps.group_permission_" . $cust_status_id . "=1 or pps.group_permission_all=1)");
				}else{
					$this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_PRICE_SPECIAL.' pps ON p.products_id = pps.products_id'." and (pps.date_available <= '" . $data . "' or pps.date_available = 0) and (pps.date_expired >= '" . $data . "'  or pps.date_expired = 0)");
				}
				
				$plugin_code = $xtPlugin->PluginCode('class.product_sql_query.php:F_Sorting_price');
				if ($plugin_code) eval($plugin_code);
				else $this->setSQL_COLS(', IF(pps.specials_price>0,pps.specials_price,p.products_price) as sort_price');
				
				$this->setSQL_SORT('sort_price');
				break;

			case 'price-desc' :
                $data = date("Y-m-d H:i:s", mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')));
			if (_SYSTEM_GROUP_CHECK == 'true') {
					$this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_PRICE_SPECIAL.' pps ON p.products_id = pps.products_id'." and (pps.date_available <= '" . $data . "' or pps.date_available = 0) and (pps.date_expired >= '" . $data . "'  or pps.date_expired = 0) and (pps.group_permission_" . $cust_status_id . "=1 or pps.group_permission_all=1)");
				}else{
					$this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_PRICE_SPECIAL.' pps ON p.products_id = pps.products_id'." and (pps.date_available <= '" . $data . "' or pps.date_available = 0) and (pps.date_expired >= '" . $data . "'  or pps.date_expired = 0)");
				}
				$plugin_code = $xtPlugin->PluginCode('class.product_sql_query.php:F_Sorting_price_desc');
				if ($plugin_code) eval($plugin_code);
				else $this->setSQL_COLS(', IF(pps.specials_price>0,pps.specials_price,p.products_price) as sort_price');
				$this->setSQL_SORT('sort_price DESC'.$add_unique_sort_field);
				break;

			case 'name' :
			    $this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_DESCRIPTION.' pd ON p.products_id = pd.products_id'." and pd.language_code = $lang_code ");
				$this->setFilter('Language');
				$this->setSQL_SORT(' pd.products_name'.$add_unique_sort_field);
				break;

			case 'name-desc' :
                $this->setSQL_TABLE('LEFT JOIN '.TABLE_PRODUCTS_DESCRIPTION.' pd ON p.products_id = pd.products_id'." and pd.language_code = $lang_code");
				$this->setFilter('Language');
				$this->setSQL_SORT(' pd.products_name DESC'.$add_unique_sort_field);
				break;
				
			case 'sort' :
				$this->setSQL_SORT(' p.products_sort'.$add_unique_sort_field);
				break;

			case 'sort-desc' :
				$this->setSQL_SORT(' p.products_sort DESC'.$add_unique_sort_field);
				break;
				
			case 'order' :
				$plugin_code = $xtPlugin->PluginCode('class.product_sql_query.php:F_Sorting_order');
				if ($plugin_code) eval($plugin_code);
				else $this->setSQL_COLS(', p.products_ordered as sort_ordered');
				
				$this->setSQL_SORT(' sort_ordered'.$add_unique_sort_field);
				break;

			case 'order-desc' :
				$plugin_code = $xtPlugin->PluginCode('class.product_sql_query.php:F_Sorting_order_desc');
				if ($plugin_code) eval($plugin_code);
				else $this->setSQL_COLS(', p.products_ordered as sort_ordered');
				
				$this->setSQL_SORT(' sort_ordered DESC'.$add_unique_sort_field); //$this->setSQL_SORT(' p.products_ordered DESC');
				break;

			case 'date' :
				if ($this->position == 'products_specials')
					$this->setSQL_SORT(' s.date_added'.$add_unique_sort_field);
				else
					$this->setSQL_SORT(' p.date_added'.$add_unique_sort_field);
				break;

			case 'date-desc' :
				if ($this->position == 'products_specials')
					$this->setSQL_SORT(' s.date_added DESC'.$add_unique_sort_field);
				else
					$this->setSQL_SORT(' p.date_added DESC'.$add_unique_sort_field);
				break;

            case 'products_model' :
                $this->setSQL_SORT(' p.products_model'.$add_unique_sort_field);
                break;

            case 'products_model-desc' :
                $this->setSQL_SORT(' p.products_model DESC'.$add_unique_sort_field);
                break;

			default:
				$this->setSQL_SORT(' p.products_sort'.$add_unique_sort_field);
				break;
				//return false;
		}
		
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.getProductSQL_query.php:F_Sorting')) ? eval($plugin_code) : false;				
	}

	function F_Date ($date_limit) {
		if ($date_limit != '0') {
			$date = date("Y.m.d", mktime(1, 1, 1, date('m'), date('d') - $date_limit, date('Y')));
			$this->setSQL_WHERE(" and p.date_added > '" . $date . "' ");
		}
	}
}
