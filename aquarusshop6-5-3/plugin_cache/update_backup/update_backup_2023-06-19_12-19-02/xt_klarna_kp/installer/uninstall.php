<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $db, $store_handler;

// ########################### Westnavi
$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` LIKE 'xt_klarna_kp%';");

// configuration entries
$db->Execute("DELETE FROM `".TABLE_CONFIGURATION_GROUP."` WHERE `group_id`=101; ");
$db->Execute("DELETE FROM `".TABLE_CONFIGURATION_GROUP."` WHERE `group_title`='TEXT_KLARNA_CONFIG'; ");

$stores = $store_handler->getStores();
foreach ($stores as $store)
{
    $db->Execute("DELETE FROM " . DB_PREFIX . "_config_" . $store['id'] . "  WHERE config_key LIKE '%_KLARNA%';");
}

// cleanup order columns, dont drop kp_order_id
$drops = array('kp_order_ref', 'kp_order_status', 'kp_order_fraud_status', 'kp_order_data');
foreach($drops as $col)
{
    if ($this->_FieldExists($col, TABLE_ORDERS))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` DROP COLUMN `".$col."` ;");
    }
}
// cleanup order indexes
$drops = array('idx_kp_order_id', 'idx_kp_order_ref', 'idx_kp_order_status', 'idx_kp_order_fraud_status');
$idxs = $db->GetArray('SHOW INDEX FROM '. TABLE_ORDERS);
foreach($idxs as $idx)
{
    if (in_array($idx['Key_name'], $drops))
    {
        $db->Execute("DROP INDEX `" . $idx['Key_name'] . "` ON `".TABLE_ORDERS."` ;");
    }
}
