<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/hooks/page_registry.php_bottom.php';

global $db;

$sql = "UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET type='text' WHERE config_key='XT_SKRILL_MERCHANT_SECRET'";
$db->Execute($sql);

$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_SKRILL_REFUNDS . " (
		  `refunds_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `refunds_type` varchar(50),
		  `refund_total` decimal(15,4) default 0,
		  `transaction_id` varchar(30) default NULL,
		  `orders_id` int(11) NULL default NULL,
		  `callback_log_id` int(11) NULL default NULL,
		  `refunded` tinyint(1) NULL default 0,
		  `success` tinyint(1) NULL default 0,
		  `refund_memo` varchar(255),
		  `date_added` datetime default NULL,
		  `error_data` longtext default NULL,
		  `error_msg` varchar(255) default NULL,
		  `callback_data` longtext default NULL,
		  PRIMARY KEY  (`refunds_id`)
		 );");


$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('skrill_transactions','skrill_refunds')");
$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET `type` = 'G' WHERE `text` = 'order'");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'skrill_transactions', 'images/icons/money_euro.png', '&plugin=xt_skrill', 'adminHandler.php', '4000', 'order', 'I', 'W');");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'skrill_refunds', 'images/icons/money_euro.png', '&plugin=xt_skrill', 'adminHandler.php', '4000', 'order', 'I', 'W');");

$output .= "
<div style='padding:10px; border:1px solid #005da2; background: #AFD0E1'>Aktivieren Sie nun Plugin und die Zahlungsweise und laden das Backend neu.<br /> Activate plugin and payment method and reload backend.</div>
";
