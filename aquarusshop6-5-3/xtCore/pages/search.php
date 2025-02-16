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

global $xtPlugin, $manufacturer, $category, $brotkrumen, $xtLink, $info, $filter;

$search_flag = false;

$_GET['keywords'] = preg_replace('/[[:^print:][äÄöÖüÜ]]/', '', $_GET['keywords']);
$_GET['keywords'] = rawurlencode($_GET['keywords']);
$_GET['keywords'] = rawurldecode($_GET['keywords']);// using rawdecode instead decode to allow +
$_GET['keywords'] = $filter->_purifier->purify($_GET['keywords']);

($plugin_code = $xtPlugin->PluginCode('module_search.php:top')) ? eval($plugin_code) : false;

if(constant('SEARCH_USE_MANUFACTURERS') == true)
{
    $tmp_data_man[] = array('id'=>'', 'text'=>TEXT_LOOK_FOR_ALL);
    $manufacturers_list = $manufacturer->getManufacturerList('default','box');
    if (is_array($manufacturers_list)) {
        $manufacturers_list = array_merge($tmp_data_man, $manufacturers_list);
    } else {
        $manufacturers_list = isset($tmp_data) ? $tmp_data : [];
    }
}

$start = microtime(true);
$cat_list = [];
if(constant('SEARCH_USE_CATEGORIES') == true)
{
    if(constant('SEARCH_USE_CATEGORIES_FIRST_LEVEL_ONLY') == true)
    {
        $cat_list = $category->getChildCategories(0, 1, false, false);
        foreach ($cat_list as &$cat)
        {
            $cat['text'] = $cat['categories_name'];
            $cat['id'] = $cat['categories_id'];
        }
    } else
    {
        $cats = $category->getCategoryBox(0, true, 0, '');
        $cat_list = array();
        flatten_categories($cats, $cat_list);
    }
}
$end = microtime(true);
//$info->_addInfo('  micro diff '.($end - $start).'');

$tmp_data[] = array('id'=>'', 'text'=>TEXT_LOOK_FOR_ALL);
$cat_list = array_merge($tmp_data, $cat_list);

$search = new search();
$search_result = array();

($plugin_code = $xtPlugin->PluginCode('module_search.php:search')) ? eval($plugin_code) : false;

$template = new Template();
if(empty($_REQUEST['advancedSearch'])) $search_flag = true;
if(isset($_GET['keywords']) && ($_GET['keywords']=='' || strlen($_GET['keywords'])<SEARCH_MIN_LENGTH)) {
    $search_flag = false;
    $info->_addInfo(ERROR_NO_KEYWORDS);
}

if(!empty($_GET['keywords']) && $search_flag==true)
{
    global $db;

    $search_array = $_GET;
    $search_array['keywords'] = mysqli_real_escape_string($db->_connectionID, $search_array['keywords']);
    $search->_search($search_array);
}

($plugin_code = $xtPlugin->PluginCode('module_search.php:search_data')) ? eval($plugin_code) : false;

if(is_countable($search->search_data) && count($search->search_data)>0){
    $search_result = array('product_listing' => $search->search_data,
        'NAVIGATION_COUNT' => $search->navigation_count,
        'NAVIGATION_PAGES' => $search->navigation_pages);

    $tpl = 'product_listing/'._STORE_TEMPLATE_PRODUCT_SEARCH_RESULT;

}else{
    if($search_flag==true){
        $info->_addInfo(ERROR_NO_SEARCH_RESULT, 'warning');
    }
    $tpl = 'search.html';
}

$brotkrumen->_addItem($xtLink->_link(array('page'=>'search')),TEXT_SEARCH);

//GET Params for Templates
if(isset($_GET['cat'])){
    $default_cat = (int)$_GET['cat'];
}
else{
    $default_cat = '';
}

if(isset($_GET['mnf'])){
    $default_mnf = (int)$_GET['mnf'];
}
else{
    $default_mnf = '';
}

if(constant('SEARCH_USE_CATEGORIES_FIRST_LEVEL_ONLY') == true || (isset($_GET['subkat']) && $_GET['subkat'] == 'on')){
    $checked_subcat = 'checked';
}
else{
    $checked_subcat = '';
}

if(isset($_GET['sdesc']) && $_GET['sdesc'] == 'on'){
    $checked_sdesc = 'checked';
}
else{
    $checked_sdesc = '';
}

if(isset($_GET['desc']) && $_GET['desc'] == 'on'){
    $checked_desc = 'checked';
}
else{
    $checked_desc = '';
}

$use_stock_check = true;
if(SEARCH_CHECK_STOCK !== false)
{
    $use_stock_check = false;
}
if(isset($_REQUEST['stock_check']) && $_REQUEST['stock_check'] == 'on'){
    $stock_check = true;
}
else{
    $stock_check = false;
}

$tpl_data = array(	'message'=>$info->info_content,
    'mnf_data'=>$manufacturers_list,
    'default_mnf'=>$default_mnf,
    'cat_data'=>$cat_list,
    'default_cat'=>$default_cat,
    'checked_subcat'=>$checked_subcat,
    'checked_desc'=>$checked_desc,
    'checked_sdesc'=>$checked_sdesc,
    'use_stock_check'=>$use_stock_check,
    'stock_check'=>$stock_check,
    'keywords' => $search_array['keywords']);
$tpl_data = array_merge($tpl_data, $search_result);



($plugin_code = $xtPlugin->PluginCode('module_search.php:default_tpl_data')) ? eval($plugin_code) : false;
$page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/'.$tpl, $tpl_data);


function flatten_categories($source, &$result = array())
{
    foreach($source as $category)
    {
        $nsbp = $category['level'] > 1 ? '&nbsp' : '';
        $cat['categories_name'] = $category['categories_name'];
        $cat['text'] = str_repeat($nsbp, (int)$category['level'] - 1). str_repeat('-', (int)$category['level'] - 1) .$nsbp.  $category['categories_name'];
        $cat['id'] = $category['categories_id'];
        $result[] = $cat;
        flatten_categories($category['sub'], $result);
    }
}