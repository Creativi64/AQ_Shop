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

global $xtMinify, $xtPlugin;

if(file_exists(_SRV_WEBROOT._SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/css/stylesheet.css'))
    $xtMinify->add_resource(_SRV_WEB_TEMPLATES._STORE_TEMPLATE.'/css/stylesheet.css',50);
else
    $xtMinify->add_resource(_SRV_WEB_TEMPLATES._SYSTEM_TEMPLATE.'/css/stylesheet.css',50);

include_once _SRV_WEBROOT.'conf/config_froala.php';

if(!defined('FROALA_CDN_VERSION')) define('FROALA_CDN_VERSION', 'latest');
?>

<link href="https://cdn.jsdelivr.net/npm/froala-editor@<?php echo FROALA_CDN_VERSION; ?>/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />

<?php
($plugin_code = $xtPlugin->PluginCode('styles.php:top')) ? eval($plugin_code) : false;
($plugin_code = $xtPlugin->PluginCode('styles.php:bottom')) ? eval($plugin_code) : false;