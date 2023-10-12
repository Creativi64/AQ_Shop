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

include '../xtFramework/admin/main.php';


if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}

$resource = $_GET['resource'];
$param ='/[^a-zA-Z0-9_-]/';
$resource=preg_replace($param,'',$resource);

$file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'charts/chart.'.$resource.'.php';
if (file_exists($file)) {

	include $file;

	$class = 'chart_'.$resource;
	$chart = new $class;

	$chart->_get();
}

($plugin_code = $xtPlugin->PluginCode('chart-data.php:bottom')) ? eval($plugin_code) : false;
?>