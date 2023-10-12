<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET TYPE='I' WHERE text='payment' AND type='G'");
$db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='paypal_plus_transactions'");
$db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='paypal_plus_refunds'");
$db->Execute("DROP TABLE IF EXISTS " . DB_PREFIX . "_plg_paypal_plus_refunds");
	
$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='in_paypal_plus' AND TABLE_NAME='".TABLE_PAYMENT."'");
if ($colExists ){
    $db->Execute("ALTER TABLE ".TABLE_PAYMENT." DROP `in_paypal_plus` ");
}

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPP_PAYMENTID' AND TABLE_NAME='".TABLE_ORDERS."'");
if ($colExists ){
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." DROP `PPP_PAYMENTID` ");
}
$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPP_SALEID' AND TABLE_NAME='".TABLE_ORDERS."'");
if ($colExists ){
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." DROP `PPP_SALEID` ");
}

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPPLUS_EMAIL' AND TABLE_NAME='".TABLE_CUSTOMERS_ADDRESSES."'");
if ($colExists ){
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_ADDRESSES." DROP `PPPLUS_EMAIL` ");
}

?>