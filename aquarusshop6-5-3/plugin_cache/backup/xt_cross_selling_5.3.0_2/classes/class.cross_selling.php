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

class cross_selling extends products_list {


	/**
	 * add cross selling entry
	 *
	 * @param int $products_id
	 * @param int $cross_sell_id
	 */
	function _addCrossSell($products_id,$cross_sell_id) {
		global $db;

		$insert_data = array();
		$insert_data['products_id']=(int)$products_id;
		$insert_data['products_id_cross_sell'] = (int)$cross_sell_id;
		$db->AutoExecute(TABLE_PRODUCTS_CROSS_SELL,$insert_data,'INSERT');

	}

	/**
	 * add cross selling for whole category
	 *
	 * @param int $categories_id
	 * @param int $cross_sell_id
	 */
	function _addCrossSellBatch($categories_id,$cross_sell_id) {
		global $db;


	}

	/**
	 * Get list with cross selling products
	 *
	 * @param int|array $products_id
	 * @return array
	 */
	function getCrossSellingProductListing($products_id, $exclude_ids = array()) {
		global $xtPlugin, $xtLink, $db;

        if(!is_array($products_id))
        {
            $products_id = (int)$products_id;
            if ($products_id=='') return false;
            $products_id = array($products_id);
        }
        if(empty($products_id)) return false;

        ($plugin_code = $xtPlugin->PluginCode('class.cross_selling.php:getCrossSellingProductListing')) ? eval($plugin_code) : false;

		$this->sql_products->setPosition('plugin_ms_cross_selling');
		if(XT_CROSS_SELLING_USE_SLAVES == 1)
		{
			$this->sql_products->setSQL_WHERE(" ");
		}
		else {
			$this->sql_products->setSQL_WHERE(" and (products_master_model = '' or products_master_model IS NULL) ");
		}

        $ids_place_holder = $products_id;
        array_walk($ids_place_holder, function(&$v){$v='?';});
        $sql_table = "INNER JOIN " . TABLE_PRODUCTS_CROSS_SELL . " pc ON pc.products_id in (". implode(',',$ids_place_holder).")";
        foreach($products_id as $id)
        {
            $params[] = $id;
        }
		$this->sql_products->setSQL_TABLE($sql_table);

		$this->sql_products->setSQL_WHERE("and pc.products_id_cross_sell=p.products_id");


		if(count($exclude_ids))
        {
            $exclude_ids_place_holder = $exclude_ids;
            array_walk($exclude_ids_place_holder, function(&$v){$v='?';});
            $sql_where = "and p.products_id NOT IN (". implode(',',$exclude_ids_place_holder).")";
            $this->sql_products->setSQL_WHERE($sql_where);
            foreach($exclude_ids as $id)
            {
                $params[] = $id;
            }
        }

        $this->sql_products->setSQL_SORT(" pc.sort_order ASC ");
		$query = $this->sql_products->getSQL_query();

		$rs = $db->CacheExecute($query,$params);

		if ($rs->RecordCount()==0) return false;

		$cross_sell_products = array();
		while (!$rs->EOF) {
			$cross_sell_products[] = $rs->fields['products_id'];
			$rs->MoveNext();
		}

		// shuffle if more than max
		if (XT_CROSS_SELLING_MAX_DISPLAY<$rs->RecordCount()) {
			shuffle($cross_sell_products);
			$cross_sell_products = array_slice($cross_sell_products, 0,XT_CROSS_SELLING_MAX_DISPLAY);
		}

		$module_content = array();
		foreach ($cross_sell_products as $key => $val) {
			$size = 'default';
			$product = new product($val,$size);// product::getProduct($val,$size); // new product($val,$size);
			$module_content[] = $product->data;
		}
		return $module_content;
	}

	function _display($products_id, $cart = false)
    {
		global $xtPlugin, $xtLink, $db;

        $module_content = false;

        $cart_products_ids = array();
        foreach($_SESSION['cart']->content as $c)
        {
            $cart_products_ids[] = $c['products_id'];
        }

        if (!$cart)
        {
		    $products_id = (int)$products_id;
		    if ($products_id=='') return false;
            $module_content = $this->getCrossSellingProductListing($products_id,
                constant('XT_CROSS_SELLING_HIDE_CART_PRODUCTS') == 1 ? $cart_products_ids : []);
        }
        else
        {
            if(count($cart_products_ids))
            {
                $module_content = $this->getCrossSellingProductListing($cart_products_ids,
                    constant('XT_CROSS_SELLING_HIDE_CART_PRODUCTS') == 1 ? $cart_products_ids : []);
            }
        }

		if (!$module_content) return false;
		$tpl_data = array('_cross_selling'=>$module_content);

		$tpl = 'cross_selling.html';
		$template = new Template();
		$template->getTemplatePath($tpl, 'xt_cross_selling', '', 'plugin');

		$tmp_data = $template->getTemplate('xt_cross_selling_smarty', $tpl, $tpl_data);
		return $tmp_data;

	}

}
