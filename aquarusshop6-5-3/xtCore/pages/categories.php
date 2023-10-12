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

global $xtLink, $xtPlugin, $category, $manufacturer;

$page_data = '';
if (empty($current_category_id)) {

$xtLink->_redirect($xtLink->_link(array('page'=>'index')));

} else {
	
    ($plugin_code = $xtPlugin->PluginCode('module_categories.php:top')) ? eval($plugin_code) : false;
    if(isset($plugin_return_value))
        return $plugin_return_value;

    // breadcrump navi must be set always
    $category->getBreadCrumbNavigation($current_category_id);
	
	$cat_template = new Template();
	$template_file_product_list = '/'._SRV_WEB_CORE.'pages/product_listing/'.$category->current_category_data['listing_template'];

	// check if cached
	// aggressive caching
	if ($cat_template->isTemplateCache($template_file_product_list)) {
        //$products_listing = $cat_template->getTemplate('listing_smarty',$template_file_product_list,$tpl_data);

        $products_listing = $cat_template->getCachedTemplate($template_file_product_list);
	}
	else
    {
        $error_product_listing = false;
        $heading_text = '';

		//simple chmod of subcategories
		if( _SYSTEM_SIMPLE_GROUP_PERMISSIONS=='true' && count($category->level)>2){
			$root_cat_id = $category->level[1];
			$root_cat = new category($root_cat_id);
			// redirect to 404 page if category dont exists
			if (!is_array($root_cat->current_category_data)) {
				if (_SYSTEM_MOD_REWRITE_404 == 'true') header("HTTP/1.0 404 Not Found");
				$tmp_link  = $xtLink->_link(array('page'=>'404'));
				$xtLink->_redirect($tmp_link);
			}
		}

		// redirect to 404 page if category dont exists
		if (!is_array($category->current_category_data)) {
			if (_SYSTEM_MOD_REWRITE_404 == 'true') header("HTTP/1.0 404 Not Found");
			$tmp_link  = $xtLink->_link(array('page'=>'404'));
			$xtLink->_redirect($tmp_link);
		}

		$list = new products_list($current_category_id);

		$data = array_merge($category->buildData($category->current_category_data),array('categorie_listing' => $category->getCategoryListing('',1)));

		$manID = false;
		if (isset($_GET['filter_id']))
			$manID = (int)$_GET['filter_id'];
		if (isset($_GET['mnf']))
			$manID = (int)$_GET['mnf'];

		if ($manID) {
			$man = array('manufacturers_id' => $manID);
			$man_data = $manufacturer->buildData($man);
			$data = array_merge($data, array('MANUFACTURER' => $man_data));
		}

		if ($category->current_category_data['categories_template']) {
			$template_file_category_list = '/'._SRV_WEB_CORE.'pages/categorie_listing/'.$category->current_category_data['categories_template'];

			$categories_listing = $cat_template->getTemplate('cat_listing_smarty',$template_file_category_list, $data);

		}

        if(!empty($_REQUEST['withStockOnly']) || (!empty($_SESSION['session_withStockOnly']) && array_key_exists('session_withStockOnly', $_REQUEST)))
        {
            $list->sql_products->setFilter('Stock');
            $_SESSION['session_withStockOnly'] = 1;
        }
        else {
            $_SESSION['session_withStockOnly'] = 0;
        }

		$tpl_product_listing = $list->getProductListing();

        if (is_array($tpl_product_listing) && count($tpl_product_listing) > 0)
        {
            $prod_man_list = new products_list($current_category_id);
            if(!empty($_REQUEST['withStockOnly']) || (!empty($_SESSION['session_withStockOnly']) && array_key_exists('session_withStockOnly', $_REQUEST)))
            {
                $prod_man_list->sql_products->setFilter('Stock');
            }
            $manufacturers = $prod_man_list->getProductListingManufacturers();

            if(count($manufacturers))
            {
                $manufacturers_dropdown['options'] = [];

                foreach ($manufacturers as $m)
                {
                    if (is_array($m->data))
                    {
                        $manufacturers_dropdown['options'][] = array('id' => $m->data['manufacturers_id'], 'text' => $m->data['manufacturers_name'], 'data' => $m->data);
                    }
                }

                $mnf_sortFunction = function($a, $b) { return $a['text'] <=> $b['text']; };
                ($plugin_code = $xtPlugin->PluginCode('module_categories.php:mnf_sortFunction')) ? eval($plugin_code) : false;
                if(is_callable($mnf_sortFunction)) usort($manufacturers_dropdown['options'], $mnf_sortFunction);

                array_unshift ($manufacturers_dropdown['options'], array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));

                if (isset($_GET['mnf']))
                {
                    $heading_text = sprintf(HEADING_PRODUCTS_MANUFACTURERS, $man_data['NAME']);
                    $manufacturers_dropdown = $category->getCategoriesDropDown($manID);
                }
            }
		}

		$sort_dropdown = is_array($tpl_product_listing) ? $list->getSortDropdown():'';
		if(isset($_GET['sorting']) && is_array($sort_dropdown) && $list->isSortDropdownDefault($sort_dropdown, $_GET['sorting'])){
			$sort_default = $_GET['sorting'];
		}
		else if ($data["products_sorting"] != ''){
			$sort_default = $data["products_sorting"];
			if($data["products_sorting2"] != 'ASC')
                $sort_default .= '-desc';
		}
        else {
            $sort_default = '';
        }

		$tpl_data = array('categories' => $categories_listing,
				'category_data'=>$category->current_category_data,
				'heading_text' => $heading_text,
				'product_listing' => $tpl_product_listing,
				'current_category_id'=>$current_category_id,
				'MANUFACTURER_DROPDOWN' => $manufacturers_dropdown,
				'NAVIGATION_COUNT' => $list->navigation_count,
				'NAVIGATION_PAGES' => $list->navigation_pages,
				'sort_default' => $sort_default,
            	'filter_default' => isset($_GET['filter_id']) ? $_GET['filter_id'] : 0,
				'sort_dropdown' => $sort_dropdown);

		($plugin_code = $xtPlugin->PluginCode('module_categories.php:tpl_data')) ? eval($plugin_code) : false;

        $tpl_data = array_merge($tpl_data, array('error_listing' =>$error_product_listing));

		$products_listing = $cat_template->getTemplate('listing_smarty',$template_file_product_list,$tpl_data);
		
	}
	
  	$page_data = $products_listing;
}
