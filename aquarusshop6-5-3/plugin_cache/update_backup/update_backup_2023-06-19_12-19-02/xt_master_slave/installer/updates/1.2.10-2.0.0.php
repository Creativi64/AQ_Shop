<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$config_key_order = array(
    'XT_MASTER_SLAVE_ACTIVE',

    'XT_MASTER_SLAVE_OPEN_SLAVE',
    '_PLUGIN_MASTER_SLAVE_STAY_ON_MASTER_URL',

    '_PLUGIN_MASTER_SLAVE_SHOW_SLAVE_LIST',
    'XT_MASTER_SLAVE_FILTERLIST_ON_SELECTION',

    'XT_MASTER_SLAVE_SLAVE_ORDER',

    '_PLUGIN_MASTER_SLAVE_SHOW_MAX_PRODUCTS',

    'XT_MASTER_SLAVE_LOAD_MASTER_IMAGE_IN_SLAVE',
    'XT_MASTER_SLAVE_USE_MASTER_FREE_FILES',

    'XT_MASTER_SLAVE_SHOP_SEARCH',

    'XT_MASTER_SLAVE_INHERIT_ASSIGNED_MASTER_IMAGES',

    'XT_MASTER_SLAVE_SUM_SLAVE_QUANTITY_FOR_GRADUATED_PRICE',
);

$i=0;
foreach($config_key_order as $config_key)
{
    $db->CacheExecute("UPDATE ".TABLE_PLUGIN_CONFIGURATION. " SET `sort_order`=? WHERE `config_key`=?", array($i*10, $config_key));

    $i++;
}

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='attributes_color' AND TABLE_NAME='".DB_PREFIX."_plg_products_attributes'");
if (!$colExists ){
    $db->CacheExecute("ALTER TABLE ".DB_PREFIX."_plg_products_attributes ADD attributes_color varchar(2048) default NULL AFTER attributes_image");
}
