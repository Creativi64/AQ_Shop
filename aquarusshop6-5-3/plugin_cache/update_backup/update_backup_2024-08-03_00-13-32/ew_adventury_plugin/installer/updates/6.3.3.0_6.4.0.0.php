<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $product_id int */

$s = ' $f = _SRV_WEBROOT . _SRV_WEB_PLUGINS . "ew_adventury_plugin/installer/uninstall.php";  if (file_exists($f)) { require_once $f; }  ';
$db->Execute("UPDATE ".TABLE_PLUGIN_SQL." SET uninstall = ? WHERE plugin_id = ?",[$s, $product_id]);

$db->Execute("DELETE FROM ".TABLE_PLUGIN_CONFIGURATION." WHERE config_key = 'CONFIG_EW_ADVENTURY_PLUGIN_COOKIEALERT'");
