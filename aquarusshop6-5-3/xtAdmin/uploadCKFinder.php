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

include_once '../xtFramework/admin/main.php';

if ( ! $xtc_acl->isLoggedIn()) {
	die('login required');
}

include (_SRV_WEBROOT_ADMIN.'page_includes.php');

global $language;

$currentType = $_GET['currentType'];

$lang = isset($language, $language->code) && ($language->code=='en' || $language->code=='de') ? $language->code : 'en';
$uniqid = uniqid ();
$resourceType = 'originals';
if($currentType=='files_free' || $_REQUEST['currentType']=='files_order')
{
    $resourceType = 'free_files';
}

$connectorInfoParams = array('currentType' =>$currentType);
if($_GET['current_id']!='') $connectorInfoParams['current_id']  = (int)$_GET['current_id'];
if($_GET['link_id']!='')    $connectorInfoParams['link_id']     = (int)$_GET['link_id'];
if($_GET['mgID']!='')       $connectorInfoParams['mgID']        = (int)$_GET['mgID'];
$connectorInfoParams['dontListFiles']        = true;

($plugin_code = $xtPlugin->PluginCode('ckfinder.uploadCKFinder_php:configure')) ? eval($plugin_code) : false;

$connectorInfo = http_build_query($connectorInfoParams);

$widgetConfig = array(
    'rememberLastFolder' => false,
    'defaultLanguage' => 'de',
    'language' => $lang,
    'connectorPath' => _SRV_WEB.'uploadCKFinderConnector.php',
    'connectorInfo' => $connectorInfo,
	'chooseFiles' => false,
	'resourceType' => $resourceType,
	'height' => '350',
	'removeModules' => 'Toolbars,ContextMenu'
);

$widgetConfigJson = json_encode($widgetConfig);

?>
 <!DOCTYPE html>
 <html>
 <head>
 <meta charset="utf-8">
<title>CKFinder Upload</title>

</head>
<body>

<div id="ckfinder<?=$uniqid?>"></div>

<script type="text/javascript" src="../xtFramework/library/ckfinder3/ckfinder.js"></script>

	<script type="text/javascript">

	var config = <?=$widgetConfigJson?>;
    CKFinder.widget( 'ckfinder<?=$uniqid?>',config);
	
 </script>

</body>
</html>