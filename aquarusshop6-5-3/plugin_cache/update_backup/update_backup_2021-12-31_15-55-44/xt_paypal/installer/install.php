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

$db->Execute("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "_plg_paypal_refunds (
          `refunds_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,          
          `refunds_type` varchar(50),
          `total` decimal(15,4) default 0,
          `transaction_id` varchar(20) default NULL,
          `orders_id` int(11) NULL default NULL,
          `callback_log_id` int(11) NULL default NULL,
          `status` tinyint(1) NULL default NULL,
          `refunded` tinyint(1) NULL default 0,
          `success` tinyint(1) NULL default 0,
          `refund_memo` varchar(255),
          `date_added` datetime default NULL,
          `error_data` longtext default NULL,
          `error_msg` varchar(255) default NULL,
          `callback_data` longtext default NULL,
          PRIMARY KEY  (`refunds_id`)
         );");

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('paypal_transactions','paypal_refunds')");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_transactions', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_refunds', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");

$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET `type` = 'G' WHERE `text` = 'order'");

$cols = $db->MetaColumns(TABLE_ORDERS);
if(!array_key_exists('AUTHORIZATION_ID', $cols)){
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD COLUMN `authorization_id` VARCHAR(255) NOT NULL DEFAULT ''");
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD COLUMN `authorization_amount` DECIMAL(15,4) NULL AFTER `authorization_id`, ADD COLUMN `authorization_expire` DATETIME NULL AFTER `authorization_amount`");
}

$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET sort_order=59 WHERE config_key='XT_PAYPAL_ORDER_STATUS_NEW'");
