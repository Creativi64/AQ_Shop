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

class bestseller_products extends products_list
{
    function getbestsellerProductListing (&$data = array())
    {
        global $xtPlugin, $xtLink, $db, $current_category_id;
        ($plugin_code = $xtPlugin->PluginCode('plugin_bestseller_products:getbestsellerProductListing_top')) ? eval($plugin_code) : false;

        if (isset($plugin_return_value))
            return $plugin_return_value;

        $sqlCols = ", p.products_ordered as products_ordered_DUMMY ";

        $this->sql_products->setSQL_WHERE("
                /*and products_ordered_DUMMY > 0*/
                ");

        if(isset($xtPlugin->active_modules['xt_master_slave']) &&  $xtPlugin->active_modules['xt_master_slave']== 'true')
        {
            if (XT_BESTSELLER_PRODUCTS_SHOW_TYPE == 'slave')
            {
                $this->sql_products->setSQL_WHERE("
                and ( (p.products_master_model != '' AND p.products_master_model IS NOT NULL) OR (p.products_master_model is NULL OR p.products_master_model = '')) 
                ");
            }
            else if (XT_BESTSELLER_PRODUCTS_SHOW_TYPE == 'nothing')
            {
                $this->sql_products->setSQL_WHERE(" 
                and ( (p.products_master_model is NULL OR p.products_master_model ='' ) AND (p.products_master_flag is NULL OR p.products_master_flag ='')) 
                ");
            }
            else if (XT_BESTSELLER_PRODUCTS_SHOW_TYPE == 'master')
            {
                $this->sql_products->setSQL_WHERE(" 
                and (p.products_master_flag = 1 or (p.products_master_model is NULL OR p.products_master_model = '') ) 
                ");

                $sqlCols = ",
                CASE 
                WHEN p.products_master_flag = 1 THEN ( SELECT SUM(p2.products_ordered) FROM ".TABLE_PRODUCTS ." p2 WHERE p2.products_master_model = p.products_model )
                ELSE p.products_ordered 
                END as products_ordered_DUMMY
                     ";
            }
        }

        if (!$this->current_categorey_id && $current_category_id)
            $this->current_categorey_id = $current_category_id;

        if (XT_BESTSELLER_PRODUCTS_FILTER_CATEGORY == 1 && $this->current_categorey_id != 0)
            $this->sql_products->setFilter('Categorie_Recursive', $this->current_categorey_id);

        if (is_data($_GET['filter_id']))
            $this->sql_products->setFilter('Manufacturer', (int)$_GET['filter_id']);

        // Filtern nach Preis
        if(XT_BESTSELLER_PRODUCTS_FILTER_BY_PRICE && XT_BESTSELLER_PRODUCTS_FILTER_PRICE_VALUE > 0) {
            $this->sql_products->setSQL_WHERE(" and p.products_price > ".XT_BESTSELLER_PRODUCTS_FILTER_PRICE_VALUE ." ");
        }

        // Filtern nach Verkaufszeitraum
        if(XT_BESTSELLER_PRODUCTS_FILTER_BY_DAYS && XT_BESTSELLER_PRODUCTS_FILTER_DAYS_BACKDATED > 0) {
            $this->sql_products->setSQL_TABLE("LEFT JOIN ".TABLE_ORDERS_PRODUCTS." op ON(p.products_id = op.products_id)");
            $this->sql_products->setSQL_TABLE("LEFT JOIN ".TABLE_ORDERS." o ON o.orders_id=op.orders_id");
            $this->sql_products->setSQL_WHERE(" AND o.date_purchased BETWEEN CURDATE() - INTERVAL ". XT_BESTSELLER_PRODUCTS_FILTER_DAYS_BACKDATED." DAY AND CURDATE()");
            $this->sql_products->setSQL_GROUP("op.products_id");
        }

        // Nur Artikel mit Bestand
        if(XT_BESTSELLER_PRODUCTS_FILTER_BY_STOCK) {
            $this->sql_products->setFilter('Stock');
        }

        ($plugin_code = $xtPlugin->PluginCode('plugin_bestseller_products:getbestsellerProductListing_query')) ? eval($plugin_code) : false;

        $this->sql_products->a_sql_cols = $sqlCols;
        //$this->sql_products->setSQL_SORT($sqlSort);

        /************** added by PD *******************/
        if ($this->sql_products->user_position == 'store') {
            //$this->sql_products->setFilter('GroupCheck');
            //$this->sql_products->setFilter('StoreCheck');
            $this->sql_products->setFilter('Fsk18');
            $this->sql_products->setFilter('Status');
            //$this->sql_products->setFilter('Seo');

            if (_STORE_STOCK_CHECK_DISPLAY == 'false' && _SYSTEM_STOCK_HANDLING == 'true') {
                $this->sql_products->setFilter('Stock');
            }
        }
        $this->sql_products->getFilter();
        $this->sql_products->getHooks();

        $query = $this->sql_products->getSQL_query()."
        having products_ordered_DUMMY > 0
        ORDER BY   products_ordered_DUMMY DESC 
        ";

        $_cachesecs = 0;

        if (XT_BESTSELLER_PRODUCTS_CACHE_HOURS > 0) {
            $_cachesecs = XT_BESTSELLER_PRODUCTS_CACHE_HOURS * 60 * 60;
        }

        if ($data['paging']) {

            $page_query = "SELECT * FROM ({$query}) as pagequery";
            //echo $data['limit'].'<Br/>'.$page_query;
            $pages = new split_page($page_query, $data['limit'], $xtLink->_getParams(array
            (
                'next_page',
                'info'
            )), $_cachesecs, 'false');

            $this->navigation_count = $pages->split_data['count'];
            $this->navigation_pages = $pages->split_data['pages'];
            $data = $pages->split_data["data"];
        }
        else {
            $query .= ' LIMIT 0, '.$data['limit'];
            $data = $db->GetArray($query);
        }

        $module_content = array();

        if(is_array($data))
        {
            foreach ($data as $e)
            {
                $size = 'default';
                ($plugin_code = $xtPlugin->PluginCode('plugin_bestseller_products:getbestsellerProductListing_size')) ? eval($plugin_code) : false;
                if (array_key_exists($e['products_id'], $module_content))
                {
                    continue;
                }
                $product = product::getProduct($e['products_id'], $size);
                ($plugin_code = $xtPlugin->PluginCode('plugin_bestseller_products:getbestsellerProductListing_data')) ? eval($plugin_code) : false;

                $module_content[$product->pID] = $product->data;

            }
        }

        ($plugin_code = $xtPlugin->PluginCode('plugin_bestseller_products:getbestsellerProductListing_bottom')) ? eval($plugin_code) : false;

        $data['total'] = (int)$pages->split_data['total'];
        return $module_content;
    }

    function cmp ($a, $b)
    {
        if ($a['products_ordered'] == $b['products_ordered']) {
            return 0;
        }
        return ($a['products_ordered'] > $b['products_ordered']) ? -1 : 1;
    }
}
