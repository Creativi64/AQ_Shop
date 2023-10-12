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

global $language, $seo, $store_handler, $xtPlugin, $xtLink, $_SYSTEM_INSTALL_SUCCESS;

$logHandler = new LogHandler();
//$logHandler->timer_start();

include _SRV_WEBROOT . _SRV_WEB_CORE . 'page_registry.php';

$form = new form();

if (!is_object($_SESSION['customer'])) {
    $_SESSION['customer'] = new customer();
    $_SESSION['geoip_country'] = USER_POSITION == 'admin' ? constant('_STORE_COUNTRY') : geoip::getCountry();
}

$customers_status = new customers_status();

define('_CUST_STATUS_SHOW_PRICE', $customers_status->customers_status_show_price);
define('_CUST_STATUS_FSK18', $customers_status->customers_fsk18);
define('_CUST_STATUS_FSK18_DISPLAY', $customers_status->customers_fsk18_display);

if ($customers_status->customers_status_template != '') {
    define('_STORE_TEMPLATE', $customers_status->customers_status_template);
} else {
    define('_STORE_TEMPLATE', constant("_STORE_DEFAULT_TEMPLATE"));
}
$template = new Template();

$currency = new currency();
$_SESSION['selected_currency'] = $currency->code;

$tax_country = false;
$countries = new countries('true','store');
if(isset($_SESSION['geoip_country']) && !empty($_SESSION['geoip_country'])
    && is_array($countries->countries_list) && array_key_exists($_SESSION['geoip_country'], $countries->countries_list))
{
    $tax_country = $_SESSION['geoip_country'];
    $agent_check = new agent_check();
    if($agent_check->isBot() && defined('_STORE_COUNTRY'))
    {
        $tax_country = constant('_STORE_COUNTRY');
    }
}

$tax = new tax($tax_country);
$xtLink = new xtLink();
$xtMinify = new xt_minify();
$mediaImages = new MediaImages();
$mediaFiles = new MediaFiles();
$brotkrumen = new brotkrumen();

define('SYSTEM_STOCK_TRAFFIC', 'true');
define('SYSTEM_SHIPPING_STATUS', 'true');

$system_status = new system_status();

$manufacturer = new manufacturer();

$product = product::getProduct();

$price = new price($customers_status->customers_status_id, $customers_status->customers_status_master);

$page_data = array();

$seo = new seo_modRewrite();
($plugin_code = $xtPlugin->PluginCode('store_main_handler.php:seo')) ? eval($plugin_code) : false;

$tmp_page = '';
$tmp_page_action = '';

// UBo ++
if(constant("_SYSTEM_MOD_REWRITE") == 'true'){
    $ru_lang = '';
    $ru_page = '';
    $ru_page_action = '';
    $ru_file_ext = (trim(constant("_SYSTEM_SEO_FILE_TYPE")) != '') ? constant("_SYSTEM_SEO_FILE_TYPE") : 'html';

    $ru = $_SERVER['REQUEST_URI'];
    $ru = $seo->_cleanUpUrl($ru);
    if (is_array($ru)) {
        $ru = $ru['url'];
    }
    list($ru,) = preg_split('/\?/', $ru, 2);
    $arr_ru = preg_split('%/%', $ru);
    if (constant("_SYSTEM_SEO_URL_LANG_BASED") == 'true') {
        // Sprache speichern
        $ru_lang = array_shift($arr_ru);
    }

    if ((count($arr_ru) == 1) or ((count($arr_ru) == 2) and (trim($arr_ru[1]) == ''))) { //[LANG_CODE]/[PAGE]
        $ru_pathinfo = pathinfo($arr_ru[0]);
        if (empty($ru_pathinfo['extension']) || $ru_pathinfo['extension'] == $ru_file_ext) {
            $ru_page = $ru_pathinfo['filename'];
        }
        $tmp_page = $ru_page;

    } else if (count($arr_ru) == 2) { //[LANG_CODE]/[PAGE]/PAGE_ACTION.HTML
        $ru_page = $arr_ru[0];
        $ru_pathinfo = pathinfo($arr_ru[1]);
        if (empty($ru_pathinfo['extension']) || $ru_pathinfo['extension'] == $ru_file_ext) {
            $ru_page_action = $ru_pathinfo['filename'];
        }
        $tmp_page = $ru_page;
        $tmp_page_action = $ru_page_action;
    }

    if (_SYSTEM_SEO_URL_LANG_BASED == 'true' && $ru_lang != '')
    {
        // sprach urls behandeln
        $langStoreCheck = $language->_checkStore($ru_lang, $store_handler->shop_id);
        if ($ru_lang != $language->code && $ru_lang != $_SESSION['selected_language'] && $langStoreCheck == true)
        {
            $language = new language($ru_lang);
            $_SESSION['selected_language'] = $ru_lang;
            ($plugin_code = $xtPlugin->PluginCode('main_handler.php:change_lang_bottom')) ? eval($plugin_code) : false;
        }
        else if(!$langStoreCheck) $ru_lang = '';
    }
    if ($tmp_page != '' && empty($_GET['page'])) { // $_GET['page'] könnte per HOOK:class.seo.php:_lookUpforUrl_switch  gesetzt sein
        $_GET['page'] = $tmp_page;
    }
    if ($tmp_page_action != '' && empty($_GET['page_action'])) { // $_GET['page_action'] könnte per HOOK:class.seo.php:_lookUpforUrl_switch  gesetzt sein
        $_GET['page_action'] = $tmp_page_action;
    }
}

if (USER_POSITION != 'admin') $seo_return = $seo->_lookUpforUrl();

// UBO --
$language->_getLanguageContent(USER_POSITION);


if (isset($_POST['page'])) {
    $tmp_page = $_POST['page'];
} elseif (isset($_GET['page'])) {
    $tmp_page = $_GET['page'];
}

if (isset($_POST['page_action'])) {
    $tmp_page_action = $_POST['page_action'];
} elseif (isset($_GET['page_action'])) {
    $tmp_page_action = $_GET['page_action'];
}

// könnte man das eher prüfen, irgendwie nicht, weil $tmp_page und $tmp_page_action werden müssen?
$exclude_pages_for_lang_exists_test = ['callback', 'index'];
($plugin_code = $xtPlugin->PluginCode('main_handler.php:check_lang_exists')) ? eval($plugin_code) : false;

$page_exists = defined('PAGE_'.strtoupper($tmp_page));
$check_lang = (!$page_exists && !in_array($tmp_page, $exclude_pages_for_lang_exists_test)) // zb callback
    || $page_exists;

if( $check_lang && constant('USER_POSITION') == 'store' && _SYSTEM_SEO_URL_LANG_BASED == 'true'
    && !empty($ru) && $ru!= 'index.php' && $ru!= 'cronjob.php' && $ru!= 'cronstep.php'
    && !$language->_checkStore($ru_lang))
{
    if (_SYSTEM_MOD_REWRITE_404=='true') {
        $tmp_page = '';
        $tmp_page_action = '';
    }else{
        $tmp_link  = $xtLink->_index();
        $xtLink->_redirect($tmp_link);
    }
}

$info = new info();

$page_data = array('page' => $tmp_page, 'page_action' => $tmp_page_action);
$page = new page($page_data);

// checking if page available
if ($tmp_page != $page->page_name && USER_POSITION != 'admin' && $tmp_page != '') {
    // redirect to 404
    if (constant("_SYSTEM_MOD_REWRITE_404") == 'true') {
   
        $seo->faultHandler(404);
    } else {
   
		$seo->Log404page();
        $tmp_link = $xtLink->_link(array('page' => 'index'));
        $xtLink->_redirect($tmp_link);
    }
}

if (($page->page_name=='404') && (USER_POSITION != 'admin') && (!defined('XT_WIZARD_STARTED'))&& ( (strpos($_SERVER['PHP_SELF'],'cronjob.php') === false))){
     $seo->Log404page();
}

$current_product_id = 0;
$current_category_id = 0;
if (isset ($_GET['info'])) {
    $current_product_id = (int)$_GET['info'];
    $current_category_id = _getSingleValue(array('value' => 'categories_id', 'table' => TABLE_PRODUCTS_TO_CATEGORIES, 'key' => 'products_id', 'key_val' => $current_product_id, 'key_where' => ' and master_link=1'));
}

$_content = new content();

$system_shipping_link = new system_shipping_link();

if($_SYSTEM_INSTALL_SUCCESS == true && !defined('XT_WIZARD_STARTED') && USER_POSITION == 'store')
{
    if (!is_object($_SESSION['cart']))
    {
        $_SESSION['cart'] = new cart();
    }
    else $_SESSION['cart']->_refresh();  // passion-d
}

if (!empty($current_product_id))
{
    $p_info = product::getProduct($current_product_id, 'full', '', '', 'product_info');
}

if (isset ($_GET['cat'])) {
    $current_category_id = (int)$_GET['cat'];
}

if (isset ($_GET['mnf'])) {
    $current_manufacturer_id = (int)$_GET['mnf'];
}

if (isset ($_GET['coID'])) {
    $current_content_id = (int)$_GET['coID'];
}

$category = new category($current_category_id);



if(!defined('XT_WIZARD_STARTED'))
{
    $meta_tags = new meta_tags();
}

($plugin_code = $xtPlugin->PluginCode('store_main_handler.php:bottom')) ? eval($plugin_code) : false;

