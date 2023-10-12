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

function smarty_function_content($params, & $smarty) {
	global $_content, $xtPlugin;

	$block = (int) $params['block_id'];
	$cont = (int) $params['cont_id'];

	$nested = false;
	if ($params['levels'] =='nested') {
	    $nested = true;
	}

	if (!isset($block) && !isset($cont)) return;

	if($block!=''){

		if (isset($params['levels'])) {
			$content_array = $_content->getContentBox($block, $nested);
		} else {
			$content_array = $_content->get_Content_Links($block);
		}

		($plugin_code = $xtPlugin->PluginCode('smarty_function_content:content_array')) ? eval($plugin_code) : false;

		$smarty->assign('_content_'.$block,$content_array);
	}

	if($cont!=''){
	if ($params['is_id']=='false') {
		$content = $_content->getHookContent($cont);
	} else {
		$content = $_content->getHookContent($cont, 'true');
	}

	($plugin_code = $xtPlugin->PluginCode('smarty_function_content:content')) ? eval($plugin_code) : false;
	$smarty->assign('_content_'.$cont,$content);
	}

	return;
}
?>