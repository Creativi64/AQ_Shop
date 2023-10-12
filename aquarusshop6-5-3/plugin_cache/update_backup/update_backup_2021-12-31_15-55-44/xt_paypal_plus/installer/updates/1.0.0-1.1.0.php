<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='in_paypal_plus' AND TABLE_NAME='".TABLE_PAYMENT."'");
if ($colExists ){
    $db->Execute("ALTER TABLE ".TABLE_PAYMENT." DROP `in_paypal_plus` ");
}