<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';

global $db;

$db->Execute("ALTER TABLE `".TABLE_SHIPCLOUD_PACKAGES."` CHANGE COLUMN `".COL_SHIPCLOUD_PACKAGE_WEIGHT."` `".COL_SHIPCLOUD_PACKAGE_WEIGHT."` DOUBLE(10,2) DEFAULT 1 ");
