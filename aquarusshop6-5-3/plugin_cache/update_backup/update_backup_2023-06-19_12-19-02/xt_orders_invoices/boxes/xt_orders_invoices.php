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

$show_box = false;

$btn = $params['button'];

if ($btn)
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

    global $db, $language, $xtLink;

    $sql = "SELECT l.caption FROM ".TABLE_PRINT_BUTTONS_LANG." l ".
        " LEFT JOIN ".TABLE_PRINT_BUTTONS. " p ON p.".COL_PRINT_BUTTONS_ID."=l.".COL_PRINT_BUTTONS_LANG_ID.
        " WHERE p.".COL_PRINT_BUTTONS_CODE."=? AND l.".COL_PRINT_BUTTONS_LANG_CODE."=?";
    $caption = $db->GetOne($sql, array($btn, $language->content_language));

    $href = $xtLink->_link(array('page'=>'PRINT_BUTTON', 'params' => "button=$btn"));
    $href = html_entity_decode($href);

    $tplFile = 'print_button_shop.html';
    $tpl_data = array(
        'caption' => $caption,
        'href'  => $href,
        'code' => $btn

    );

    $template = new Template();
    $template->getTemplatePath($tplFile, 'xt_orders_invoices', '', 'plugin');
    $html = $template->getTemplate('', $tplFile, $tpl_data);

    echo $html;

}