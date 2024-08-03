<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/hooks/page_registry.php_bottom.php';

global $db, $filter;

$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 24, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 25, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 26, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 27, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 28, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 29, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 30, '', 0.1, 10000.00, 0, 1);");
$db->Execute("INSERT INTO ".TABLE_PAYMENT_COST." (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(".$payment_id.", 31, '', 0.1, 10000.00, 0, 1);");

$img = $filter->_filter($xml_data['xtcommerceplugin']['payment']['payment_icon']);
copy(
    _SRV_WEBROOT . _SRV_WEB_PLUGINS . "xt_skrill/images/$img",
    _SRV_WEBROOT . "media/payment/$img");

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


