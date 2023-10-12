<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $store_handler;

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('xt_startpage_products')");
$db->Execute("
		INSERT INTO
		".TABLE_ADMIN_NAVIGATION." (`text`, `icon`, `url_i`, `url_d`, `sortorder`, `parent`, `type`, `navtype`, `cls`, `handler`, `iconCls`)
		VALUES ('xt_startpage_products', 'images/icons/database_gear.png', '&plugin=xt_startpage_products', 'adminHandler.php', '5002', 'shop', 'I', 'W', NULL, NULL, 'fas fa-home')");

$create = "CREATE TABLE IF NOT EXISTS  `" . DB_PREFIX . "_startpage_products` (
	 `startpage_products_id` int(11) NOT NULL AUTO_INCREMENT,
	 `shop_id` int(11) NOT NULL,
	 `products_id` int(11) NOT NULL,
	 `startpage_products_sort` int(11) NOT NULL DEFAULT 0,
	 PRIMARY KEY (`startpage_products_id`),
	 UNIQUE KEY `shop_id_products_id_unique` (`shop_id`,`products_id`)
	) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8";

$db->Execute($create);