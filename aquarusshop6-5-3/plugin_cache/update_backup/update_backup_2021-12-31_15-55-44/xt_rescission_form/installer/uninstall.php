<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$emailTpls = $db->GetAll("SELECT `tpl_id` FROM ". TABLE_MAIL_TEMPLATES ." WHERE `tpl_type` LIKE 'rescission_form' ");
foreach($emailTpls as $tpl)
{
	$db->Execute("DELETE FROM ".TABLE_MAIL_TEMPLATES_CONTENT." WHERE `tpl_id` = ".$tpl['tpl_id']);
	$db->Execute("DELETE FROM ".TABLE_MAIL_TEMPLATES." WHERE `tpl_id` = ".$tpl['tpl_id']);
}

$seo_plugin_file = _SRV_WEBROOT.'/xtFramework/classes/class.seo_plugins.php';
if (file_exists($seo_plugin_file))
{
	require_once $seo_plugin_file;

	$seo_plugin = new seo_plugins();
	$seo_plugin->unsetPluginSEO('xt_rescission_form');
}
