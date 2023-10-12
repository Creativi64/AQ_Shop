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

require_once _SRV_WEBROOT.'conf/config_search.php';

class search{

	public $search_data = array();

	function __construct(){
		global $xtPlugin, $filter;

		($plugin_code = $xtPlugin->PluginCode('class.search.php:search_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$this->sql_products = new search_query();
	}

	function _search(&$data){
		global $db, $xtPlugin, $current_category_id, $xtLink, $category, $filter;

        $a =0 ;
        $data['keywords'] = $filter->_purifier->purify($data['keywords']);
        $match='!<([A-Z]\w*)(?:\s* (?:\w+) \s* = \s* (?(?=["\']) (["\'])(?:.*?\2)+ | (?:[^\s]*) ) )* \s* (\s/)? >!ix';
        $data['keywords'] = preg_replace($match,'<\1\5>', $data['keywords']);
        $data['keywords'] = preg_replace('#</*(applet|meta|form|svg|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$data['keywords']);
        $data['keywords'] = str_replace("\\",'',$data['keywords']);

        //$data['keywords'] = mysqli_real_escape_string($db->_connectionID, $data['keywords']);

		if(!empty($data['mnf'])) $data['mnf']=(int)$data['mnf'];
		if(!empty($data['cat'])) $data['cat']=(int)$data['cat'];
		
		($plugin_code = $xtPlugin->PluginCode('class.search.php:_search_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;
        
		$this->sql_products->setPosition('getSearchData');
		$this->sql_products->setFilter('Language');

		($plugin_code = $xtPlugin->PluginCode('class.search.php:_search_filter')) ? eval($plugin_code) : false;

		$this->sql_products->setFilter('Keywords', $data, '', 'array');

        if(SEARCH_CHECK_STOCK === true || !empty($data['stock_check']))
            $this->sql_products->setFilter('Stock');

		if(!empty($data['mnf']))
		$this->sql_products->setFilter('Manufacturer', $data['mnf']);

		if(!empty($data['cat']) && $data['subkat']=='on'){
			$rc = $category->getChildCategoriesIDs($data['cat']);
			$this->sql_products->setSQL_TABLE("INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id LEFT JOIN ".TABLE_CATEGORIES." c ON p2c.categories_id = c.categories_id");
			
            
			if(count($rc) > 0){			
				$this->sql_products->setSQL_WHERE("and p2c.categories_id in (".$data['cat'].", ".implode(',',$rc).")");
			}
			else{
				$this->sql_products->setSQL_WHERE("and p2c.categories_id in (".$data['cat'].")");
			}
            
           
		}elseif(!empty($data['cat'])){
			$this->sql_products->setFilter('Categorie', $data['cat']);
		}else{
            
            if( _SYSTEM_SIMPLE_GROUP_PERMISSIONS=='true'){
                $cat_list = $category->getAllCategoriesList();
                $cat_listIDs = array();
                foreach($cat_list as $k=>$v){
                    $cat_listIDs[] = $v['categories_id'];
                }
                $this->sql_products->setSQL_TABLE("INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id LEFT JOIN ".TABLE_CATEGORIES." c ON p2c.categories_id = c.categories_id");

                $this->sql_products->setSQL_WHERE("and p2c.categories_id in (".implode(',',$cat_listIDs).")");
            }
        }
        
        $module_content = array();
        
		($plugin_code = $xtPlugin->PluginCode('class.search.php:_search_SQL')) ? eval($plugin_code) : false;
		$queryData = $this->sql_products->getSQL_query('distinct p.products_id');
		$query = "".$queryData['sql']."";
		$inputarr = $queryData['placeholderParams'];

		$pages = new split_page($query, _STORE_SEARCH_RESULTS, $xtLink->_getParams(array ('next_page', 'info')), 0, true, $inputarr);

		$this->navigation_count = $pages->split_data['count'];
		$this->navigation_pages = $pages->split_data['pages'];

		$_count = count($pages->split_data['data']);
		if(!empty($data['keywords'])) $this->saveSearchResults($_count, $data['keywords']);
		
		for ($i = 0; $i < $_count;$i++) {
			$size = 'default';
			($plugin_code = $xtPlugin->PluginCode('class.search.php:_search_data')) ? eval($plugin_code) : false;
			if (version_compare(5,_SYSTEM_VERSION) == 1) // xt4
			{
				$product = new product($pages->split_data['data'][$i]['products_id'],$size);
			}
			else {
			$product = product::getProduct($pages->split_data['data'][$i]['products_id'],$size);
			}
			$module_content[] = $product->data;
		}

		($plugin_code = $xtPlugin->PluginCode('class.search.php:_search_bottom')) ? eval($plugin_code) : false;
		
		$this->search_data = $module_content;
		return $module_content;

	}
	
	public function saveSearchResults($count, $keyword)
	{
		global $db, $store_handler;

		$count = (int)$count;
		$keyword = trim((string)$keyword);
		
		if($keyword != 'Suchbegriff eingeben' && $keyword != '')
		{
			$sql = 'INSERT INTO ' . TABLE_SEARCH . '(keyword, result_count, request_count, last_date, shop_id) 
					VALUES (?, ?, "1", DATE(NOW()), ?)
					ON DUPLICATE KEY UPDATE
					request_count = (request_count + 1),
					last_date = (DATE(NOW())),
					result_count = ?;';
			
			$db->Execute($sql, array($keyword, $count, $store_handler->shop_id, $count));
		}
	}
}