<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


global $db, $store_handler;

$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_api'");
$db->Execute("DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text` = 'xt_api_log'");

$db->Execute("DROP TABLE IF EXISTS " . DB_PREFIX . "_api_user");
$db->Execute("DROP TABLE IF EXISTS " . DB_PREFIX . "_api_user_restriction");
$db->Execute("DROP TABLE IF EXISTS " . DB_PREFIX . "_api_log");

