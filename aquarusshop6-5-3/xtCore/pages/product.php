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

if(!defined('TABLE_MEDIA_DOWNLOAD_IP'))
	define('TABLE_MEDIA_DOWNLOAD_IP', 'xt_media_download_ip');

($plugin_code = $xtPlugin->PluginCode('module_product.php:top')) ? eval($plugin_code) : false;

if (!$p_info->is_product) {
	if (_SYSTEM_MOD_REWRITE_404 == 'true') header("HTTP/1.0 404 Not Found");
	$tmp_link  = $xtLink->_link(array('page'=>'404'));
	$xtLink->_redirect($tmp_link);
}

if (isset($_GET['dl_media'])) {
    // public download allowed ?
    if (_STORE_PRODUCT_DOWNLOAD_PUBLIC_ALLOWED=='false' && !$_SESSION['registered_customer']) {
        $tmp_link  = $xtLink->_link(array('page'=>'product','params'=>'info='.$current_product_id));
        $brotkrumen->_setSnapshot($tmp_link);
        $info->_addInfoSession(TEXT_PUBLIC_DOWNLOAD_REQUIRE_LOGIN,'error');
        $tmp_link  = $xtLink->_link(array('page'=>'customer','paction'=>'login'));
        $xtLink->_redirect($tmp_link);      
    }
    $download = new download();
    $download->_deleteOutOfDateLinks();
    $download->servePublicFile($_GET['dl_media']);
}

($plugin_code = $xtPlugin->PluginCode('module_product.php:data')) ? eval($plugin_code) : false;

$p_info->getBreadCrumbNavigation($current_product_id);
$template = new Template();
$tpl_data = $p_info->data;
$tpl_data['product'] = $p_info->data;

$man = array('manufacturers_id' => $p_info->data['manufacturers_id']);
$tpl_data['manufacturer'] = $manufacturer->buildData( $man );

if ($tpl_data['product_template']!='') {
	$tpl=$tpl_data['product_template'];
} else {
	$tpl='product.html';
}

$tpl_data['message']= $info->info_content;

($plugin_code = $xtPlugin->PluginCode('module_product.php:default_tpl_data')) ? eval($plugin_code) : false;
$page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/product/'.$tpl, $tpl_data);
