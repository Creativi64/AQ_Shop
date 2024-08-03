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

class auto_cross_sell extends getProductSQL_query {

    /**
     * @param $products_ids int|array
     */
    function F_AutoCrossSell ($products_ids)
    {
        if(!is_array($products_ids)) $products_ids = [$products_ids];
        foreach($products_ids as $k => $v) $products_ids[$k] = (int) $v;
        $products_ids = array_filter(array_map('trim', $products_ids));
        if(empty($products_ids)) $products_ids = [0];
        $this->setSQL_TABLE(" LEFT JOIN ".TABLE_ORDERS_PRODUCTS." bop on bop.products_id = p.products_id ");
        $this->setSQL_WHERE(" AND aop.products_id IN (".implode(',',$products_ids).") and aop.products_id!=bop.products_id and aop.orders_id = bop.orders_id ");
    }

    function getSQL_query($sql_cols = 'DISTINCT p.products_id', $filter_type='string')
    {
        return parent::getSQL_query($sql_cols, $filter_type);
    }


    /**
     * @param int $products_id
     * if $product_id is given modul looks for single product cross sells, ignoring products in cart
     * if $product_id == 0 modul looks for all cross sells of cart products, also ignoring products in cart
     * @return array|bool
     */
    protected function _getAutoCrossSell($products_id = 0) {
        global $xtPlugin, $xtLink, $db;

        $cart_product_ids = !empty($products_id) ? [$products_id] : [];
        foreach ($_SESSION['cart']->content as $key => $val) {
            if($val['products_id']!='')
                $cart_product_ids[] = (int) $val['products_id'];
        }

        $limit = (int)constant('XT_AUTO_CROSS_SELL_MAX_DISPLAY');
        if(empty($limit)) $limit = 4;
        else if($limit > 40) $limit = 40;

        $this->reset();
        $this->setSQL_TABLE(" ".TABLE_ORDERS_PRODUCTS." aop, ".TABLE_PRODUCTS." p ", true);
        $this->setFilter('AutoCrossSell', $products_id ? $products_id : $cart_product_ids, 'and', 'array');

        $exclude_sql = '';
        $params = [];

        // ignore products already in cart
        $ids_place_holder = $cart_product_ids;
        array_walk($ids_place_holder, function(&$v){$v='?';});
        $exclude_sql = "AND bop.products_id NOT IN (". implode(',',$ids_place_holder).")";
        foreach($cart_product_ids as $id)
        {
            $params[] = $id;
        }
        $this->setSQL_WHERE(" $exclude_sql ");
        $query = $this->getSQL_query();

        ($plugin_code = $xtPlugin->PluginCode('class.auto_cross_sell.php:listing')) ? eval($plugin_code) : false;

        $rs = $db->CacheGetArray($query, $params);
        if (!$rs || !is_array($rs) || !count($rs)) return false;

        $rs = array_column($rs, 'products_id');
        $rs = array_diff($rs, $cart_product_ids);
        if(count($rs) > $limit)
            $rs_keys = array_rand($rs, $limit);

        $size = 'default';
        $module_content = [];
        foreach ($rs_keys as $key)
        {
            $product = product::getProduct($rs[$key],$size);
            $module_content[] = $product->data;
        }

        return $module_content;
    }

    /**
     *  auto cross selling for shopping cart display or a given products_id
     *  Display list with cross selling products (based on previous orders)
     *
     * @param int $products_id
     * @return bool|string
     */
    function _display($products_id = 0)
    {
        global $xtPlugin, $xtLink, $db;

        if (!empty($products_id))
        {
            $module_content = $this->_getAutoCrossSell($products_id);
        }
        else if (is_array($_SESSION['cart']->content) && count($_SESSION['cart']->content))
        {
            $module_content = $this->_getAutoCrossSell();
        }
        else {
            return false;
        }
        if (empty($module_content)) return false;

        $tpl_data = array('_auto_cross_sell'=>$module_content);
        $tpl = 'auto_cross_sell.html';
        $template = new Template();
        $template->getTemplatePath($tpl, 'xt_auto_cross_sell', '', 'plugin');

        $tmp_data = $template->getTemplate('xt_auto_cross_sell_smarty', $tpl, $tpl_data);
        return $tmp_data;
    }

    function F_Seo($lang_code='')
    {
        $a = 0;
    }

}
