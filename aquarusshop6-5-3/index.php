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

if(version_compare(PHP_VERSION, '8.1.0', '<')) {
    die('xt:Commerce Systemvorraussetzung: Mindestens PHP 8.1 erforderlich. Sie verwenden PHP '.PHP_VERSION.'. Bitte kontaktieren Sie Ihren Webhoster mit Hinweis auf: https://www.php.net/supported-versions.php<br><br>
xt:Commerce system requirement: At least PHP 8.1 is required. You are using PHP '.PHP_VERSION.'. Please contact your web host with reference to: https://www.php.net/supported-versions.php
');
}

$total_queries = 0;

$display_output = true;
$message_data = false;
include ('xtCore/main.php');

global $brotkrumen, $xtLink, $page, $xtPlugin;

$index_tpl = 'index.html';
$show_index_boxes = 'true';
$no_index_tag = false;
$brotkrumen->_addItem($xtLink->_link(array('page'=>'index', 'exclude'=>'cat_info_coID_mnf')),TEXT_HOME);

if(empty($_SESSION["registered_customer"])
    && defined('_STORE_STAY_ON_PAGE_AFTER_CUSTOMER_LOGIN') && constant('_STORE_STAY_ON_PAGE_AFTER_CUSTOMER_LOGIN') == 1
    && $page->page_name != 'customer'
    && $page->page_name != '404'
    && !array_key_exists('noSnapshot', $_REQUEST))
{
    global $db, $language, $store_handler, $current_product_id, $current_category_id, $current_content_id, $current_manufacturer_id;
    $tmp_link = false;

    switch($page->page_name)
    {
        case '': // also index
            $tmp_link  = $xtLink->_index();
            break;
        case 'product':
            $tmp_link  = $xtLink->_link(array('page'=>'product','params'=>'info='.$current_product_id));
            break;
        case 'category':
        case 'categorie':
            $tmp_link  = $xtLink->_link(array('page'=>'categorie','params'=>'cat='.$current_category_id));
            break;
        case 'content':
            $tmp_link  = $xtLink->_link(array('page'=>'content','params'=>'coID='.$current_content_id));
            break;
        case 'cart':
            $tmp_link  = $xtLink->_link(array('page'=>'cart'));
            break;
        case 'manufacturer':
        case 'manufacturers':
            $tmp_link  = $xtLink->_link(array('page'=>'manufacturer','params'=>'mnf='.$current_manufacturer_id));
            break;
        case 'search':
            $tmp_link  = $xtLink->_link(array('page'=>'search','params'=>'keywords='.$_REQUEST["keywords"]));
            break;
        default:
            // seo plgs verarbeiten, xt_new_product zB
            $tmp_link_arr = array('page' => $page->page_name);
            $plg_code = $db->GetOne("SELECT p.plugin_code FROM ".TABLE_SEO_URL." t
        							LEFT JOIN " . TABLE_PLUGIN_CODE . " p ON p.plugin_id = t.link_id
        						    WHERE t.link_type = '1000' and t.store_id=? and t.language_code=? and p.plugin_code=? ",
                array($store_handler->shop_id, $language->code, $page->page_name));
            if($plg_code)
            {
                $query = '';
                if (is_array($_GET))
                {
                    $params = array();
                    foreach ($_GET as $k => $v)
                    {
                        if (in_array($k, array('page', 'page_action', 'plugin')))
                        {
                            continue;
                        }
                        $params[$k] = $v;
                    }
                    if (count($params))
                    {
                        $query = '?' . http_build_query($params);
                    }
                }
                $tmp_link = $xtLink->_link(array('page' => $page->page_name, 'params' => $query));
            }
    }
    ($plugin_code = $xtPlugin->PluginCode('index.php:setSnapshot')) ? eval($plugin_code) : false;

    if($tmp_link != false)
    {
        $brotkrumen->_setSnapshot($tmp_link);
    }
}

include $page->loaded_page;

if ($_SESSION['customer']->customer_info['account_type'] == '0') {
	$account = true;
    $guest_account = false;
}
elseif ($_SESSION['registered_customer'])
{
    $guest_account = true;
}
$a = !isset($_SESSION['registered_customer']) ?: $_SESSION['registered_customer'];


if ($display_output) {
    global $xtPlugin;
    $template = new Template();
    $tpl_data = array('content'=> !empty($page_data) ? $page_data : 'empty page_data',
        'message'=>$message_data,
        'account'=>$account,
        'guest_account' => $guest_account,
        'page'=>$page->page_name,
        'show_index_boxes'=>$show_index_boxes,
        'registered_customer'=> isset($_SESSION['registered_customer']) ?: $_SESSION['registered_customer'],
        'top_navigation'=>$brotkrumen->_output(),
    );
    ($plugin_code = $xtPlugin->PluginCode('index.php:tpl_data')) ? eval($plugin_code) : false;
    $show_shop_content = $template->getTemplate('smarty', '/'.$index_tpl, $tpl_data);

    include _SRV_WEBROOT._SRV_WEB_CORE.'display.php';

    _show_debug_values();
}else{
	($plugin_code = $xtPlugin->PluginCode('index.php:display_output')) ? eval($plugin_code) : false;
}
if ($db->debug==true && isset($dbLogger)) {
	__debug($dbLogger);
	//$dbLogger->_showMultipleQueries(0,'xt_products_reviews');
}
