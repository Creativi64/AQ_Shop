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

function smarty_function_box_cache($params, & $smarty) {
	global $xtPlugin, $category, $product, $manufacturer, $xtLink, $language;

	$template = new Template();

    $template->content_smarty = new Smarty();
    $template->content_smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $template->content_smarty->setCacheLifetime(SMARTY_CACHE_LIFETIME);

	$show_box = true;

	$box = new box($params);
	if($box->loaded_box == false){
		if (!isset($params['htmlonly']))
			$show_box = false;

	}

	if($show_box==true){

		if (!empty($params['cache_id'])) {
			$template->setAdditionalCacheID($params['cache_id']);
		}

		if(isset($params['lang']) && $params['lang']=='true'){
			$params['name'] = $language->code.'_'.$params['name'];
			if($params['tpl'])
				$params['tpl'] = $language->code.'_'.$params['tpl'];
		}

		if(empty($params['tpl'])){
			$tpl = 'box_'.$params['name'].'.html';
		}else{
			$tpl = $params['tpl'];
		}

		if(empty($params['type']) || $params['type']=='core'){
			$path = _SRV_WEB_CORE.'boxes/';
		}elseif($params['type']=='user'){

			$template->getTemplatePath($tpl, $params['name'], 'boxes', 'plugin');
			$path = '';

		}else{
			$path = $params['type'];
		}

		$tpl_name = '/'.$path.$tpl;
		$tpl_data = $params;

		if (!$template->isTemplateCache($tpl_name)) {
			if (file_exists($box->loaded_box))
				include $box->loaded_box;
		}

		if($show_box==true) // include $box->loaded_box may changed the value of show_box
		{
			$box_content = $template->getTemplate($params['name'].'_smarty', $tpl_name, $tpl_data, false, true);
			echo $box_content;
		}

	}
}
?>