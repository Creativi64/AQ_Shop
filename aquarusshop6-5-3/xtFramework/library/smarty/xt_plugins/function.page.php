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

/**
 * @param $params
 * @param $smarty Smarty
 * @return mixed
 */
function smarty_function_page($params, & $smarty) {
	global $xtPlugin, $template, $category, $product, $manufacturer, $xtLink;

	($plugin_code = $xtPlugin->PluginCode('smarty_function_page.php:top')) ? eval($plugin_code) : false;
	if(isset($plugin_return_value))
	return $plugin_return_value;

	$show_page = true;
	$page_data = '';

	if(isset($params['async']) && $params['async'] === true)
    {
        static $tpl = null;
        if(empty($tpl)) {
            $tpl = new Template();
        }
        $params['connector'] = 'CONNECTOR_PAGE';
        $params['timestamp'] = time();
        $params['callback'] = 'replaceDomElement';
        $params['callback_args'] = [ 'id' => $params['name'].'_'.$params['timestamp'], 'html' => '# to be computed in xtCore/connector/page.php #'];

        $tplFile = 'async_page.tpl.html';
        $tpl_data = [
            'params'    => $params,
            'params_js' => json_encode($params, JSON_PRETTY_PRINT)
        ];
        $tpl->getTemplatePath($tplFile, '', 'xtCore/pages', 'default');
        $page_data = $tpl->getTemplate('', $tplFile, $tpl_data);

        $show_page = false;
    }
    else
    {
        $subpage = new subpage($params);
        if ($subpage->loaded_subpage != false)
        {
            include $subpage->loaded_subpage;
        }
        else
        {
            $show_page = false;
        }


    }

    echo $page_data;
}
