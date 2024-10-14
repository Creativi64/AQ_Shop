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

use Smarty\Smarty;
use Smarty\Template;

function evaluate_smarty($a, $smarty_template_vars_var_name = 'tpl_data')
{
    global $template, ${$smarty_template_vars_var_name};

    $smarty = new Smarty();
    $smarty->setCompileDir(_SRV_WEBROOT . 'templates_c');

    $smarty->addTemplateDir('./'._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/');
    $smarty->addTemplateDir('./'._SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/');

    if (is_array(${$smarty_template_vars_var_name}))
    {
        foreach(${$smarty_template_vars_var_name} as $k => $v)
        {
            $smarty->assign($k, $v);
        }
    }

    \Template::addPluginsDir($smarty, array(
        _SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins'));

    $smarty->registerPlugin(Smarty::PLUGIN_MODIFIER, 'evaluate_smarty', 'evaluate_smarty', true, []);

    $html = $smarty->fetch('eval:' . $a);

    return $html;
}