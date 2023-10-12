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


function smarty_function_captcha($params, & $smarty)
{
	global $xtPlugin, $template, $category, $product, $manufacturer, $xtLink, $language;

	($plugin_code = $xtPlugin->PluginCode('smarty_function_captcha.php:top')) ? eval($plugin_code) : false;
	if(isset($plugin_return_value))
	return $plugin_return_value;


    if (isset($_SESSION['registered_customer']) && !isset($params['ignore_registered_customer']))
    {
    } else {
        global $xtLink;
        $tpl_data_captcha = array(
            'captcha_link' => $xtLink->_link(array('default_page' => 'captcha.php', 'conn' => 'SSL')),
            'ignore_registered_customer' => isset($params['ignore_registered_customer']) ? true : false
        );

        $tpl_captcha = 'captcha.html';
        $template = new Template();
        $template->getTemplatePath($tpl_captcha, '/xtCore/forms', '', 'default');

        $captcha_html = $template->getTemplate('captcha', $tpl_captcha, $tpl_data_captcha);

        ($plugin_code = $xtPlugin->PluginCode('smarty_function_captcha.php:data')) ? eval($plugin_code) : false;

        echo $captcha_html;
    }

    ($plugin_code = $xtPlugin->PluginCode('smarty_function_captcha.php:bottom')) ? eval($plugin_code) : false;
}
