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

global $xtLink, $brotkrumen, $xtPlugin, $_content, $page;

$show_index_boxes = false;

if (empty($current_content_id)) {

	$xtLink->_redirect($xtLink->_link(array('page'=>'index')));

} else {
	// download ?
	if (isset($_GET['dl_media'])) {
		$download = new download();
		$download->_deleteOutOfDateLinks();
		$download->servePublicFile($_GET['dl_media']);
	}	
	
	($plugin_code = $xtPlugin->PluginCode('module_content.php:content_top')) ? eval($plugin_code) : false;

	$shop_content_data =  $_content->getHookContent($current_content_id, 'true');

	if ($shop_content_data['content_status']=='0' || !$shop_content_data['content_id']) {
		if (constant('_SYSTEM_MOD_REWRITE_404') == 'true') header("HTTP/1.0 404 Not Found");
		
		$tmp_link  = $xtLink->_link(array('page'=>'404'));
		$xtLink->_redirect($tmp_link);
	}
	// content form ?
	if ($shop_content_data['content_form']!='' && file_exists(_SRV_WEBROOT.'xtCore/forms/'.$shop_content_data['content_form'])) {

		include _SRV_WEBROOT.'xtCore/forms/'.$shop_content_data['content_form'];

	} else {

		($plugin_code = $xtPlugin->PluginCode('module_content.php:content_data')) ? eval($plugin_code) : false;

		if (is_array($shop_content_data['subcontent'])) {
			$subdata = $shop_content_data['subcontent'];
			($plugin_code = $xtPlugin->PluginCode('module_content.php:sub_content_data')) ? eval($plugin_code) : false;
		}

        $path = $_content->getPath($current_content_id);
        array_shift($path);
        $path = array_reverse($path);

        foreach ($path as $parent_content_id)
        {
            $parent_content_data =  $_content->getHookContent($parent_content_id, 'true');
            $navigation_link = array('page'=>'content', 'type'=>'content','name'=>$parent_content_data['title'],'id'=>$parent_content_data['content_id'],'seo_url' => $parent_content_data['url_text']);
            $navigation_link = $xtLink->_link($navigation_link);
            $brotkrumen->_addItem($navigation_link,$parent_content_data['title']);
        }

        $navigation_link = array('page'=>'content', 'type'=>'content','name'=>$shop_content_data['title'],'id'=>$shop_content_data['content_id'],'seo_url' => $shop_content_data['url_text']);
        $navigation_link = $xtLink->_link($navigation_link);
        
		$brotkrumen->_addItem($navigation_link,$shop_content_data['title']);
		
		$template = new Template();
        
        if($page->default_page=="index" && $shop_content_data['content_hook'] == 4){
            $content = $_content->getHookContent('4');
            $tpl = 'default.html';
            $tpl_data = array();
            $tpl_data = $content;
        }else{
            $tpl_data = array('data'=>$shop_content_data, 'subdata'=>$subdata);
            $tpl = 'content.html';
        }

		if (isset($_GET['popup'])) {
			$no_index_tag = true; 
            $index_tpl = 'popup.html';
			$tpl = 'popup_content.html';
		}
		
		($plugin_code = $xtPlugin->PluginCode('module_content.php:tpl_data')) ? eval($plugin_code) : false;
		$page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/'.$tpl, $tpl_data);
	}
}
