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

function smarty_function_link($params, & $smarty) {
	global $xtLink, $xtPlugin, $seo;

	($plugin_code = $xtPlugin->PluginCode('smarty_function_link:_link_top')) ? eval($plugin_code) : false;

	if(!empty($params['params_exclude']))
	$exclude = $params['params_exclude'];
	
	if(!empty($params['params_value']))
	$params['params'] = $params['params'].'='.$params['params_value'].'&'.$xtLink->_getParams($exclude);

	unset($params['params_value']);
	
	($plugin_code = $xtPlugin->PluginCode('smarty_function_link:_link_bottom')) ? eval($plugin_code) : false;
	$tmp_link = $xtLink->_link($params);

	echo $tmp_link;
}
