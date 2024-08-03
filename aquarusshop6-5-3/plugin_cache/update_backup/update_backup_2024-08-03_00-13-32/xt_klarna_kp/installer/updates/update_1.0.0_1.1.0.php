<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $store_handler;

if (!$this->_FieldExists('kp_order_test_mode', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `kp_order_test_mode` INT(1) NULL AFTER `kp_order_id`;");
}

$store_config_change = [
    '_KLARNA_CONFIG_MID_TEST' => '_KLARNA_CONFIG_KP_MID_TEST' ,
    '_KLARNA_CONFIG_PWD_TEST' => '_KLARNA_CONFIG_KP_PWD_TEST',
    '_KLARNA_CONFIG_TESTMODE' => '_KLARNA_CONFIG_KP_TESTMODE',
    '_KLARNA_CONFIG_MID'  => '_KLARNA_CONFIG_KP_MID',
    '_KLARNA_CONFIG_PWD'  => '_KLARNA_CONFIG_KP_PWD',

    '_KLARNA_CONFIG_LOGO_DESIGN' => '_KLARNA_CONFIG_KP_LOGO_DESIGN'
];

$store_config_new = [
    '_KLARNA_CONFIG_KP_TESTMODE_CUSTOMER_GROUP' => [
        'config_value' => '',
        'group_id' => 101,
        'sort_order' => 10,
        'type' => 'dropdown',
        'url' => 'customers_status&add_empty'
    ]
];

foreach($store_handler->getStores() as $store)
{
    foreach($store_config_change as $oldKey => $newKey)
    {
        $db->Execute('UPDATE '.TABLE_CONFIGURATION_MULTI.$store['id']." SET config_key=? WHERE config_key=?", [$newKey, $oldKey]);
    }
    $db->Execute('UPDATE '.TABLE_CONFIGURATION_MULTI.$store['id']." SET type='hidden' WHERE config_key='_KLARNA_CONFIG_KP_LOGO_DESIGN'");

    foreach($store_config_new as $k => $c)
    {
        $keyExists = $db->GetOne(" SELECT 1 FROM ". DB_PREFIX . "_config_" . $store['id'] . " WHERE config_key=?", [$k]);
        if(!$keyExists)
        {
            $db->Execute("INSERT INTO " . DB_PREFIX . "_config_" . $store['id'] . " (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) 
            VALUES 
            (?, ?, ?, ?, ?, ?);", [$k, $c['config_value'], $c['group_id'], $c['sort_order'], $c['type'], $c['url']]);
        }
    }
}