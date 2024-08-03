<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . 'plugins/xt_coupons/classes/constants.php';

global $db;

$db->Execute("ALTER TABLE ".TABLE_COUPONS."
CHANGE COLUMN `customers_status` `customers_status` VARCHAR(1024) NULL DEFAULT '0'
");
