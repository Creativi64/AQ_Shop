<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/constants.php';

global $db;

$db->Execute("UPDATE `".TABLE_SHIPPER."` SET `".COL_SHIPPER_TRACKING_URL."` = REPLACE(`".COL_SHIPPER_TRACKING_URL."`, 'http:', 'https:') ");
