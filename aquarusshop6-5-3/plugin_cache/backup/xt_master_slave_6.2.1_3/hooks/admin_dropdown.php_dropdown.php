<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get'] == 'plg_xt_master_slave_redirect_to_slaves') {
	if (!isset($result)) $result = array();
	$result[] = array('id' => 'true', 'name' => XT_MASTER_SLAVE_STAY_IN_MASTER_TRUE, 'desc' => XT_MASTER_SLAVE_STAY_IN_MASTER_TRUE);
	$result[] = array('id' => 'false', 'name' => XT_MASTER_SLAVE_STAY_IN_MASTER_FALSE, 'desc' => XT_MASTER_SLAVE_STAY_IN_MASTER_FALSE);
	$result[] = array('id' => 'ajax', 'name' => XT_MASTER_SLAVE_STAY_IN_MASTER_PLUS_AJAX, 'desc' => XT_MASTER_SLAVE_STAY_IN_MASTER_PLUS_AJAX);
}

if ($request['get'] == 'plg_xt_master_slave_shop_search') {
	if (!isset($result)) $result = array();
	$result[] = array('id' => 'master', 'name' => XT_MASTER_SLAVE_MASTER_PRODUCTS, 'desc' => XT_MASTER_SLAVE_MASTER_PRODUCTS);
	$result[] = array('id' => 'slave', 'name' => XT_MASTER_SLAVE_SLAVE_PRODUCTS, 'desc' => XT_MASTER_SLAVE_SLAVE_PRODUCTS);
}


$isVariant = $request['is_variant'] == 'true' ? true : false;
$isMain = $request['is_variant'] == 'false' ? true : false;
$text_override = $isVariant ? __define('HEADING_CONFIGURATION').' '.__define('TEXT_MAIN_PRODUCT' ) : __define('TEXT_PLUGIN').'-'.__define('HEADING_CONFIGURATION');

if (in_array($request['get'],
    ['ms_open_first_slave',
    'ms_show_slave_list',
    'ms_filter_slave_list',
    'ms_filter_slave_list_hide_on_product',
    'ms_load_masters_free_downloads',
    'products_keywords_from_master',
    'products_short_description_from_master',
    'products_description_from_master'])
) {
    $isVariant = $request['is_variant'] == 'true' ? true : false;
    $text_override = $isVariant ? __define('HEADING_CONFIGURATION').' '.__define('TEXT_MAIN_PRODUCT' ) : __define('TEXT_PLUGIN').'-'.__define('HEADING_CONFIGURATION');
    if (!isset($result)) $result = array();
    $result[] = array('id' => '2', 'name' => $text_override, 'desc' => $text_override);
    $result[] = array('id' => '0', 'name' => __define('TEXT_FALSE'), 'desc' => __define('TEXT_FALSE'));
    $result[] = array('id' => '1', 'name' => __define('TEXT_TRUE'), 'desc' => __define('TEXT_TRUE'));
}

if (in_array($request['get'],
    [
        'ms_load_masters_main_img'])

) {
    if (!isset($result)) $result = array();
    $result[] = array('id' => '0', 'name' => __define('TEXT_FALSE'), 'desc' => __define('TEXT_FALSE'));
    if ($isVariant || $isMain) $result[] = array('id' => '2', 'name' => $text_override, 'desc' => $text_override);
    $result[] = array('id' => '1', 'name' => __define('TEXT_PREPEND_IMAGE'), 'desc' => __define('TEXT_PREPEND_IMAGE'));
    $result[] = array('id' => '3', 'name' => __define('TEXT_APPEND_IMAGE'), 'desc' => __define('TEXT_APPEND_IMAGE'));
}

if (in_array($request['get'],
    [
        'load_mains_imgs'])

) {
    if (!isset($result)) $result = array();
    $result[] = array('id' => '0', 'name' => __define('TEXT_FALSE'), 'desc' => __define('TEXT_FALSE'));
    if ($isVariant || $isMain) $result[] = array('id' => '2', 'name' => $text_override, 'desc' => $text_override);
    $result[] = array('id' => '1', 'name' => __define('TEXT_PREPEND_IMAGES'), 'desc' => __define('TEXT_PREPEND_IMAGES'));
    $result[] = array('id' => '3', 'name' => __define('TEXT_APPEND_IMAGES'), 'desc' => __define('TEXT_APPEND_IMAGES'));
}

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_functions.php';
if (isset($xtPlugin->active_modules['xt_master_slave'])) {
    switch ($request['get']) {
        case 'products_model':
            require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';
            $pmodel_ms = new xt_master_slave();
            $result = $pmodel_ms->getProductsMaster();
            break;
        case 'attrib_tree':
            require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';
            $tree_ms = new xt_master_slave();
            $result = $tree_ms->getAttribTree();
            break;
        case 'attrib_parent':
            require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';
            $ap = new xt_master_slave();
            $result = $ap->getAttribParent();
            break;
		  case 'attribute_templates':
            require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';
			
            $ap = new xt_master_slave();
            $result = $ap->getAttributeTemplate();
            break;	
        case 'products_option_template':
            $result = $dropdown->getProductOptionTemplate('xt_master_slave/templates/options/', true, 'true');
            break;
        case 'products_option_list_template':
            $result = $dropdown->getProductOptionListTemplate('xt_master_slave/templates/product_listing/', true, 'true');
            break;
        case 'master_price_view':
            $result = xt_master_slave_functions::get_master_price_view_flags();
            break;
    }
}
