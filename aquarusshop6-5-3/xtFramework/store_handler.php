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

global $xtPlugin, $db;

$store_handler = new multistore();
$store_handler->determineStoreId();
$store_handler->loadStoreConfigMainData(); // backend > shop-einstellungen > xyz-shop > mein shop  // ohne sprachabhängige daten
//$store_handler->getLicenseFileInfo(array('key'));

$lang_code_init = '';
if(array_value($_REQUEST, 'language') && array_value($_REQUEST, 'load_section') != 'language_sync' )
{
    // einfacher pre check
    $exists = $db->GetOne('select 1 FROM '.TABLE_LANGUAGES. ' WHERE `code` = ?', [$_REQUEST['language']]);
    if($exists)
        $_SESSION['selected_language']=$_REQUEST['language']; // xtAdmin
}
else if(array_value($_REQUEST, 'new_lang'))
{
    // einfacher pre check
    $exists = $db->GetOne('select 1 FROM '.TABLE_LANGUAGES. ' WHERE `code` = ?', [$_REQUEST['new_lang']]);
    if($exists)
        $_SESSION['selected_language']=$_REQUEST['new_lang'];
}

$language = new language( $_SESSION['selected_language'] ?? '');
$language->_setLocale();

$store_handler->loadConfig();

($plugin_code = $xtPlugin->PluginCode('store_handler.php:bottom')) ? eval($plugin_code) : false;

