<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $store_handler;

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE `text`='xt_api';");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls` ,`url_h`) VALUES (NULL , 'xt_api', 'images/icons/user_comment.png', '&plugin=xt_api&pg=overview', 'adminHandler.php', '4000', 'config', 'I', 'W', 'fas fa-user-astronaut','https://xtcommerce.atlassian.net/wiki/spaces/XT41DUE/pages/8257626/');");

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE `text`='xt_api_log';");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype` ,`iconCls`) VALUES (NULL , 'xt_api_log', 'images/icons/user_comment.png', '&plugin=xt_api&load_section=xt_api_log&pg=overview', 'adminHandler.php', '4000', 'logs', 'I', 'W', 'fas fa-user-astronaut');");


if (!$this->_FieldExists('orders_exported',TABLE_ORDERS))
    $db->Execute("ALTER TABLE ".TABLE_ORDERS." ADD `orders_exported` INT( 1 ) NOT NULL DEFAULT '0';");


if (!$this->_FieldExists('bw_id',DB_PREFIX.'_plg_products_attributes'))
    $db->Execute("ALTER TABLE ".DB_PREFIX."_plg_products_attributes ADD `bw_id` INT( 11 ) NOT NULL DEFAULT '0';");

if (!$this->_FieldExists('external_id',DB_PREFIX.'_media'))
    $db->Execute("ALTER TABLE ".DB_PREFIX."_media ADD `external_id` VARCHAR( 255 ) NOT NULL DEFAULT '';");

$db->Execute("
    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_api_user` (
	`api_user_id` INT(11) NOT NULL AUTO_INCREMENT,
	`api_username` VARCHAR(50) NULL DEFAULT NULL,
	`api_password` VARCHAR(255) NULL DEFAULT NULL,
	`access_status` INT(1) NULL DEFAULT '0',
	`api_log_active` INT(1) NULL DEFAULT '0',
	`api_access_restrictions` TEXT NULL,
	PRIMARY KEY (`api_user_id`),
	INDEX `api_username` (`api_username`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8; ");

$db->Execute("
    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_api_user_restriction` (
	`restriction_id` INT(11) NOT NULL AUTO_INCREMENT,
	`api_user_id` INT(11) NULL DEFAULT NULL,
	`access_source` VARCHAR(50) NULL DEFAULT NULL,
	`comment` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`restriction_id`),
	INDEX `api_user_id` (`api_user_id`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8; ");

$db->Execute("
    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "_api_log` (
	`api_log_id` INT(11) NOT NULL AUTO_INCREMENT,
	`api_user_id` INT(11) NULL DEFAULT NULL,
	`log_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`request_service` VARCHAR(255) NULL DEFAULT NULL,
	`api_request` TEXT NULL,
	`api_response` TEXT NULL,
	`request_source` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`api_log_id`),
	INDEX `api_user_id` (`api_user_id`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8; ");
