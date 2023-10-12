<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/* Set SEO data for plugin */
$seo_plugin_file = _SRV_WEBROOT.'/xtFramework/classes/class.seo_plugins.php';
if (file_exists($seo_plugin_file))
{
	require_once $seo_plugin_file;
	
	 $seo_plugin = new seo_plugins();
	 $seo_plugin->setPluginSEO('xt_upcoming_products');
}
