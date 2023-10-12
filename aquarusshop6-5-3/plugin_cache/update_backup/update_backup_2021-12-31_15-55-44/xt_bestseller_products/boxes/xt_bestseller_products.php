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

if (XT_BESTSELLER_PRODUCTS_ACTIVATED == 1 && ACTIVATE_XT_BESTSELLER_PRODUCTS_BOX == 1) {
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . '/xt_bestseller_products/classes/class.bestseller_products.php';

    if ($params['limit']) {
        $limit = $params['limit'];
    } else {
        $limit = XT_BESTSELLER_PRODUCTS_BOX_LIMIT;
    }

    $bestseller_products_data_array = array
    (
        'limit' => $limit,
        'sorting' => $params['order_by'],
        'paging' => false
    );

    global $current_category_id;
    $bestseller_products_box = new bestseller_products($current_category_id);
    $bestseller_products_list = $bestseller_products_box->getbestsellerProductListing($bestseller_products_data_array);

    if (count($bestseller_products_list) != 0) {
        if (ACTIVATE_XT_BESTSELLER_PRODUCTS_PAGE == 1) {
            $show_more_link = true;
        } else {
            $show_more_link = false;
        }

        $serv_url = explode('/', $_SERVER['REQUEST_URI']);
        $serv_url_tmp = explode('?', end($serv_url));

        $tpl_data = array
        (
            '_bestseller_products' => $bestseller_products_list,
            '_show_more_link' => $show_more_link,
            'curr_url' => $serv_url_tmp[0]
        );

        $show_box = true;
    } else {
        $show_box = false;
    }
} else {
    $show_box = false;
}
