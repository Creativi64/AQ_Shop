<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');


global $db, $store_handler;


$db->Execute("DROP TABLE IF EXISTS " . DB_PREFIX . "_plg_twofa_users");

if ($this->_FieldExists('plugin_xt_twofa_status', TABLE_ADMIN_ACL_AREA_USER)) {
    $db->Execute("ALTER TABLE `" . TABLE_ADMIN_ACL_AREA_USER . "` DROP COLUMN `plugin_xt_twofa_status` ;");
}


