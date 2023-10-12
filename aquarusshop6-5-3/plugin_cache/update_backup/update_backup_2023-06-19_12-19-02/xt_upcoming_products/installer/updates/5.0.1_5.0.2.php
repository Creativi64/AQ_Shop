<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $store_handler;

$stores = $store_handler->getStores();
$keys = array('ACTIVATE_XT_UPCOMING_PRODUCTS_PAGE', 'ACTIVATE_XT_UPCOMING_PRODUCTS_BOX');

foreach($keys as $key)
{
    foreach($stores as $store)
    {
        $c_val = $db->GetOne("SELECT config_value FROM " . TABLE_PLUGIN_CONFIGURATION . " WHERE config_key=? and shop_id=?", array($key, $store['id']));

        if ($c_val == 'true' || $c_val == 1)
        {
            $c_val = 1;
        }
        else
        {
            $c_val = 0;
        }

        $db->Execute("UPDATE IGNORE " . TABLE_PLUGIN_CONFIGURATION . " SET config_value=? WHERE config_key=? and shop_id=?", array($c_val, $key, $store['id']));
    }
}

$seo_plugin_file = _SRV_WEBROOT.'/xtFramework/classes/class.seo_plugins.php';
if (file_exists($seo_plugin_file))
{
    require_once $seo_plugin_file;

    $seo_plugin = new seo_plugins();
    $seo_plugin->setPluginSEO('xt_upcoming_products');
}

