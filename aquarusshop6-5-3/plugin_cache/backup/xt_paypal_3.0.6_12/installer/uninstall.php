<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='paypal_transactions'");
$db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='paypal_refunds'");
$db->Execute("DROP TABLE IF EXISTS " . TABLE_PAYPAL_REFUNDS);
?>