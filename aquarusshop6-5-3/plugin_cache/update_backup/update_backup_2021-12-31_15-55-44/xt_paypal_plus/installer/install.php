<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 24, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 25, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 26, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 27, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 28, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 29, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 30, '', 0, 10000.00, 0, 1);");
$db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 31, '', 0, 10000.00, 0, 1);");

$db->Execute("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "_plg_paypal_plus_refunds (
		  `ppp_refunds_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,          
		  `ppp_refunds_type` varchar(50),
		  `total` decimal(15,4) default 0,
		  `transaction_id` varchar(30) default NULL,
		  `ppp_payment` varchar(30) default NULL,
		  `orders_id` int(11) NULL default NULL,
		  `callback_log_id` int(11) NULL default NULL,
		  `status` tinyint(1) NULL default NULL,
		  `ppp_refunded` tinyint(1) NULL default 0,
		  `success` tinyint(1) NULL default 0,
		  `ppp_refund_memo` varchar(255),
		  `date_added` datetime default NULL,
		  `error_data` longtext default NULL,
		  `error_msg` varchar(255) default NULL,
		  `callback_data` longtext default NULL,
		  PRIMARY KEY  (`ppp_refunds_id`)
		 );");

$blockId = $db->GetOne("SELECT pid FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='paypal_plus_transactions'");
if ($blockId==0){
	$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET TYPE='G' WHERE text='payment' AND type='I'");
	$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET `type` = 'G' WHERE `text` = 'order'");
	$db->Execute("UPDATE `".TABLE_ADMIN_NAVIGATION."` SET `parent`='order' WHERE `text`='paypal_transactions' ");
	$db->Execute("UPDATE `".TABLE_ADMIN_NAVIGATION."` SET `parent`='order' WHERE `text`='paypal_refunds' ");
	$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('paypal_plus_transactions','paypal_plus_refunds')");
	$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_plus_transactions', 'images/icons/money_euro.png', '&plugin=xt_paypal_plus', 'adminHandler.php', '4000', 'order', 'I', 'W');");
	$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_plus_refunds', 'images/icons/money_euro.png', '&plugin=xt_paypal_plus', 'adminHandler.php', '4000', 'order', 'I', 'W');");
}

/*
$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='in_paypal_plus' AND TABLE_NAME='".TABLE_PAYMENT."'");
if (!$colExists ){
    $db->Execute("ALTER TABLE ".TABLE_PAYMENT." ADD `in_paypal_plus` int(1) DEFAULT '0' ");
}
*/

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPP_PAYMENTID' AND TABLE_NAME='".TABLE_ORDERS."'");
if (!$colExists ){
	$db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD `PPP_PAYMENTID` varchar(255) default NULL ");
}

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPP_SALEID' AND TABLE_NAME='".TABLE_ORDERS."'");
if (!$colExists ){
	$db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD `PPP_SALEID` varchar(255) default NULL ");
}

$colExists = $db->GetOne("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='"._SYSTEM_DATABASE_DATABASE."' AND COLUMN_NAME='PPPLUS_EMAIL' AND TABLE_NAME='".TABLE_CUSTOMERS_ADDRESSES."'");
if (!$colExists ){
	$db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_ADDRESSES." ADD `PPPLUS_EMAIL` varchar(255) default NULL ");
}

// set default payment status
// default open/offen exists? 16
$statusId = $db->GetOne("SELECT status_id FROM ".TABLE_SYSTEM_STATUS." ss WHERE ss.status_id = 16 AND ss.status_class = 'order_status'");
if($statusId)
{
	$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value=? WHERE config_key IN
	('XT_PAYPAL_PLUS_ORDER_STATUS_CANCELED','XT_PAYPAL_PLUS_ORDER_STATUS_CREATED','XT_PAYPAL_PLUS_ORDER_STATUS_EXPIRED','XT_PAYPAL_PLUS_ORDER_STATUS_FAILED','XT_PAYPAL_PLUS_ORDER_STATUS_PENDING' )",
		array($statusId));
}
// default canceled/storniert exists? 32
$statusId = $db->GetOne("SELECT status_id FROM ".TABLE_SYSTEM_STATUS." ss WHERE ss.status_id = 32 AND ss.status_class = 'order_status'");
if($statusId)
{
	$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value=? WHERE config_key IN
	('XT_PAYPAL_PLUS_ORDER_STATUS_REFUNDED' )",
		array($statusId));
}
// default received/erhalten exists?  23
$statusId = $db->GetOne("SELECT status_id FROM ".TABLE_SYSTEM_STATUS." ss WHERE ss.status_id = 23 AND ss.status_class = 'order_status'");
if($statusId)
{
	$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET config_value=? WHERE config_key IN
	('XT_PAYPAL_PLUS_ORDER_STATUS_APPROVED' )",
		array($statusId));
}


?>