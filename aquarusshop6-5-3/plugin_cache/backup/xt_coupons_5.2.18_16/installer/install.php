<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('coupons','xt_coupons','xt_coupons_token','xt_coupons_redeem')");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'coupons', 'images/icons/money_euro.png', '', '', '4000', 'shop', 'G', 'W', 'fas fa-money-check-alt');");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_coupons', 'images/icons/money_euro.png', '&plugin=xt_coupons', 'adminHandler.php', '4000', 'coupons', 'I', 'W', 'fas fa-money-check-alt');");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_coupons_token', 'images/icons/money_euro.png', '&plugin=xt_coupons&load_section=xt_coupons_token', 'adminHandler.php', '4010', 'coupons', 'I', 'W', 'fas fa-clipboard-list');");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_coupons_redeem', 'images/icons/money_euro.png', '&plugin=xt_coupons&load_section=xt_coupons_redeem', 'adminHandler.php', '4020', 'coupons', 'I', 'W', 'fas fa-clipboard-check');");

$db->Execute("
 CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_code` varchar(32) default NULL,
  `compaign_id` int(10) unsigned default NULL,
  `customers_status` varchar(1024) NULL DEFAULT '0',
  `coupon_amount` double(15,4) default '0.0000',
  `coupon_percent` smallint(5) unsigned default NULL,
  `coupon_free_shipping` tinyint(3) unsigned default NULL,
  `coupon_created_ip` varchar(15) default NULL,
  `coupon_created_date` datetime default NULL,
  `coupon_order_ordered` int(10) unsigned default NULL,
  `coupon_max_total` int(10) unsigned default NULL,
  `coupon_max_per_customer` int(10) unsigned default NULL,
  `coupon_minimum_order_value` decimal(15,4) unsigned default NULL,
  `coupon_status` tinyint(3) unsigned default NULL,
  `coupon_last_modified_date` datetime default NULL,
  `coupon_start_date` datetime default NULL,
  `coupon_expire_date` datetime default NULL,
  `coupon_tax_class`  int(10) unsigned default NULL,
  `coupon_can_decrease_shipping` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`coupon_id`),
  UNIQUE KEY `pk` (`coupon_id`),
  KEY `pk_2` (`coupon_id`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

// 1.3.3
if(!$this->_FieldExists('coupon_free_on_100_status', DB_PREFIX.'_coupons'))
{
    $db->Execute("
  ALTER TABLE ".DB_PREFIX."_coupons ADD `coupon_free_on_100_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'
");
}
///

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_categories (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned default NULL,
  `categories_id` int(10) unsigned default NULL,
  `allow` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`pk`),
  UNIQUE KEY `pk` (`pk`),
  KEY `pk_2` (`pk`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_customers (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned default NULL,
  `customers_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`pk`),
  UNIQUE KEY `pk` (`pk`),
  KEY `pk_2` (`pk`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_description (
  `coupon_id` int(10) unsigned NOT NULL default '0',
  `language_code` char(2) NOT NULL default '0',
  `coupon_name` varchar(50) default NULL,
  `coupon_description` text,
  PRIMARY KEY  (`coupon_id`,`language_code`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_generator (
  `coupons_generator_id` int(10) unsigned NOT NULL auto_increment,
  `count` int(10) unsigned default NULL,
  `items_limit` int(10) unsigned default '100',
  `mask` varchar(50) default NULL,
  `coupon_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`coupons_generator_id`),
  UNIQUE KEY `coupons_generator_id` (`coupons_generator_id`),
  KEY `coupons_generator_id_2` (`coupons_generator_id`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_im_export (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ei_type` varchar(32) default NULL,
  `ei_title` varchar(64) default NULL,
  `ei_filename` varchar(64) default NULL,
  `ei_delimiter` varchar(32) default NULL,
  `ei_limit` int(10) unsigned default NULL,
  `ei_id` varchar(32) default NULL,
  `ei_coupon` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_products (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned default NULL,
  `products_id` int(10) unsigned default NULL,
  `allow` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`pk`),
  UNIQUE KEY `pk` (`pk`),
  KEY `pk_2` (`pk`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_redeem (
  `coupons_redeem_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned default NULL,
  `coupon_token_id` int(10) unsigned default NULL,
  `redeem_date` datetime default NULL,
  `redeem_ip` varchar(40) default NULL,
  `customers_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned default NULL,
  `redeem_amount` decimal(15,4) unsigned default NULL,
  PRIMARY KEY  (`coupons_redeem_id`),
  UNIQUE KEY `coupons_redeem_id` (`coupons_redeem_id`),
  KEY `coupons_redeem_id_2` (`coupons_redeem_id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_token (
  `coupons_token_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_id` int(10) unsigned NOT NULL,
  `coupon_token_code` varchar(50) NOT NULL,
  `coupon_token_order_id` int(10) unsigned default '0',
  PRIMARY KEY  (`coupons_token_id`),
  UNIQUE KEY `coupons_code_id` (`coupons_token_id`),
  UNIQUE KEY `coupons_code_id_2` (`coupon_token_code`)
) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

// 1.3.3
if(!$this->_FieldExists('coupon_token_amount', DB_PREFIX.'_coupons_token'))
{
    $db->Execute("
  ALTER TABLE ".DB_PREFIX."_coupons_token ADD `coupon_token_amount` DOUBLE(15,4) NULL DEFAULT '0.0000'
");
}

$db->Execute("
	CREATE TABLE IF NOT EXISTS ".DB_PREFIX."_coupons_permission (
	  `pid` int(11) NOT NULL,
	  `permission` tinyint(1) DEFAULT '0',
	  `pgroup` varchar(255) NOT NULL,
	  PRIMARY KEY (`pid`,`pgroup`)
	) ENGINE=".DB_STORAGE_ENGINE." DEFAULT CHARSET=utf8;
");