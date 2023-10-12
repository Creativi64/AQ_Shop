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

function smarty_function_txt($params, & $smarty) {

    global $language, $db;

	if(!isset($params['key']))
		return false;

    $tpl_lang = $smarty->getTemplateVars('template_language');
    if(!empty($tpl_lang) && $tpl_lang != $language->content_language)
    {
        $txt = $params['key'];
        $txt_tmp = false;
        foreach(array('both', 'store', 'admin') as $cls)
        {
            $txt_tmp = $db->GetOne("SELECT language_value FROM ".TABLE_LANGUAGE_CONTENT." WHERE language_key=? and language_code=? and class=?", array(strtoupper($params['key']), $tpl_lang, $cls));
            if($txt_tmp) break;
        }
        if($txt_tmp) {
            $txt = $txt_tmp;
        }
        echo $txt;
        return;
    }

	$txt = strtoupper($params['key']);
    if (defined($txt))
	   $txt = str_replace('\n','<br />',constant($txt));
	else $txt = $params['key'];

	if(isset($params['show']) && !$params['show'])
        $smarty->assign('_txt_'.$params['key'],$txt);
    else
    echo $txt;

}
?>