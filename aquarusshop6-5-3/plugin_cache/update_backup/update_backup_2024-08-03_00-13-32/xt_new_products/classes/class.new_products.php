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

class new_products extends products_list {


	function getNewProductListing (&$data = array()) {
		global $xtPlugin, $xtLink, $db, $current_category_id;

		($plugin_code = $xtPlugin->PluginCode('plugin_new_products:getNewProductListing_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->sql_products->setPosition('new_products');

		//$this->sql_products->setFilter('Language');
		
		if (!$this->current_category_id)
		    $this->current_category_id = $current_category_id;

		if (XT_NEW_PRODUCTS_FILTER_CATEGORY == 1 && $this->current_category_id != 0)
		    $this->sql_products->setFilter('Categorie_Recursive', $this->current_category_id);

		if (is_data($_GET['filter_id']))
			$this->sql_products->setFilter('Manufacturer', (int)$_GET['filter_id']);

		if (is_data($_GET['sorting'])) {
			$this->sql_products->setFilter('Sorting', $_GET['sorting']);
		}elseif(is_data($data['sorting'])){
			$this->sql_products->setSQL_SORT($data['sorting']);
		} else {
			$this->sql_products->setSQL_SORT("p.date_added DESC");
		}

        if (!empty($data['days'])) {
            $days = (int)$data['days'];
        } else {
            $days = XT_NEW_PRODUCTS_PAGE_DAYS;
        }

		$this->sql_products->setFilter('Date', $days);

        if(isset($xtPlugin->active_modules['xt_master_slave']) &&  $xtPlugin->active_modules['xt_master_slave']== 'true')
        {
            $sql_where = " and ( p.products_master_flag = 1 || ( (p.products_master_flag is NULL || p.products_master_flag=0) && (p.products_master_model is NULL || p.products_master_model='') )) ";
            if ((int)XT_NEW_PRODUCTS_USE_SLAVES === 1)
            {
                $sql_where = " and ( p.products_master_model>'' || ((p.products_master_flag is NULL || p.products_master_flag=0) && (p.products_master_model is NULL || p.products_master_model='') )) ";
            }
            $this->sql_products->setSQL_WHERE($sql_where);
        }

		($plugin_code = $xtPlugin->PluginCode('plugin_new_products:getNewProductListing_query')) ? eval($plugin_code) : false;

		$this->sql_products->setSQL_GROUP("p.products_id");
		
		$query = $this->sql_products->getSQL_query();

		$pages = new split_page($query, $data['limit'], $xtLink->_getParams(array ('next_page', 'info')), '', 'false');

		$this->navigation_count = $pages->split_data['count'];
		$this->navigation_pages = $pages->split_data['pages'];

        $module_content = array();
		$count = count($pages->split_data['data']);
		for ($i = 0; $i < $count;$i++) {
			$size = 'default';
			($plugin_code = $xtPlugin->PluginCode('plugin_new_products:getNewProductListing_size')) ? eval($plugin_code) : false;
			$product =  product::getProduct($pages->split_data['data'][$i]['products_id'],$size);
			($plugin_code = $xtPlugin->PluginCode('plugin_new_products:getNewProductListing_data')) ? eval($plugin_code) : false;
			$module_content[] = $product->data;
		}

		($plugin_code = $xtPlugin->PluginCode('plugin_new_products:getNewProductListing_bottom')) ? eval($plugin_code) : false;

		$data['total'] = (int)$pages->split_data['total'];
		return $module_content;
	}
}
