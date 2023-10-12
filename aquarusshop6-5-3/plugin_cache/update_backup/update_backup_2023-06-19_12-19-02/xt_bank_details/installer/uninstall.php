<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/constants.php';

global $db;

// ########################### Westnavi
$db->Execute("
    DELETE FROM `".TABLE_ADMIN_NAVIGATION."` WHERE `text`='xt_bank_details';
");


// ########################### Tables
$db->Execute("
    DROP TABLE IF EXISTS ".TABLE_BAD_BANK_DETAILS."
    ");


$db->Execute("
    DROP TABLE IF EXISTS ".TABLE_BAD_ORDER_BANK_DETAILS."
    ");


