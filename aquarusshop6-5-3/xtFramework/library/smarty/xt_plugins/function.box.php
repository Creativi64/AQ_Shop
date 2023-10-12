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


function smarty_function_box($params, & $smarty) {
	global $xtPlugin, /*$template,*/ $category, $product, $manufacturer, $xtLink, $language;

	($plugin_code = $xtPlugin->PluginCode('smarty_function_box.php:top')) ? eval($plugin_code) : false;
	if(isset($plugin_return_value))
	return $plugin_return_value;

	$show_box = true;
	$tpl_data = array();

	$box = new box($params);
	if($box->loaded_box != false){
		include $box->loaded_box;
	} else{
        if (!isset($params['htmlonly']))
            $show_box = false;

    }

	($plugin_code = $xtPlugin->PluginCode('smarty_function_box.php:data')) ? eval($plugin_code) : false;

	if($show_box==true){
		$template = new Template();

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

		if((isset($params['type']) && $params['type']=='core') || empty($params['type'])){
			$path = _SRV_WEB_CORE.'boxes/';
		}elseif($params['type']=='user'){

			$template->getTemplatePath($tpl, $params['name'], 'boxes', 'plugin');
			$path = '';

		}else{
			$path = $params['type'];
		}

		($plugin_code = $xtPlugin->PluginCode('smarty_function_box.php:bottom')) ? eval($plugin_code) : false;

		$box_content = $template->getTemplate($params['name'].'_smarty', '/'.$path.$tpl, $tpl_data);

		if((isset($params['return']) && $params['return']=='true'))
		    return $box_content;
		echo $box_content;
	}
}
?>