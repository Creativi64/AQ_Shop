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

$time = new LogHandler();
$time->_start();

$brotkrumen->_addItem($xtLink->_link(array('page'=>'content', 'params'=>'coID='.$shop_content_data['content_id'],'seo_url' => $shop_content_data['url_text'])),$shop_content_data['title']);
 
$template = new Template();  
$tpl = 'sitemap.html';  
$result = $template->isTemplateCache($tpl);
$cacheID = $template->getTemplateCacheID($tpl);


function prepareData($data, $textKey = 'name', $idKey = 'id',$linkKey = 'link' ,$subKey = 'sub') {
    if (!is_array($data)) return false;
    foreach ($data as $key => $val) {
        if ($val[$subKey]) {
            $val[$subKey] = prepareData($val[$subKey], $textKey, $idKey, $linkKey ,$subKey);
        }
        $new_data[$key] = array('id' => $val[$idKey],
                                'title' => $val[$textKey],
                                'link' => $val[$linkKey],
                                'sub' => $val[$subKey],
                                );
    }
    return $new_data;
}



$_cats = $category->getChildCategories(0, 0, true);
$_cats = prepareData($_cats,'categories_name','categories_id','categories_link');

$_blocks = $_content->getBlocks();


$_man = $manufacturer->getManufacturerList();

$add_data['_data'] = array();



$add_data['_data'] = prepareData($_blocks);
foreach ($add_data['_data'] as $c => $block) {
     $add_data['_data_content'][$c]['sub'] = $_content->getChildcontent(0, 0, $block['id'], true);
}

foreach ($_cats as $cat){
    $add_data['_data_categories'][] = $cat;
}
$_man = prepareData($_man, 'manufacturers_name', 'manufacturers_id');
foreach ($_man as $man){
    $add_data['_data_manufacturers'][] = $man;
}



$tpl_data = array('message'=>$info->info_content,'data'=>$shop_content_data, 'subdata'=>$subdata);
if (is_array($add_data)) $tpl_data = array_merge($tpl_data,$add_data);
//__debug($tpl_data);
($plugin_code = $xtPlugin->PluginCode('module_content.php:tpl_data')) ? eval($plugin_code) : false;
$page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'forms/'.$tpl, $tpl_data);
$time->_stop();
//$time->timer_display();
?>