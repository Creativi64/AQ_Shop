<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if($this->_FieldExists('type',DB_PREFIX."_clean_cache_logs"))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_clean_cache_logs` CHANGE COLUMN `type` `type` VARCHAR(64) NOT NULL ;");
if($this->_FieldExists('change_trigger',DB_PREFIX."_clean_cache_logs"))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_clean_cache_logs` CHANGE COLUMN `change_trigger` `change_trigger` VARCHAR(64) NOT NULL ;");
if($this->_FieldExists('last_modified',DB_PREFIX."_clean_cache_logs"))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_clean_cache_logs` DROP COLUMN `last_modified` ;");
if($this->_FieldExists('date_added',DB_PREFIX."_clean_cache_logs"))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_clean_cache_logs` DROP COLUMN `date_added` ;");


$db->Execute("DROP TABLE IF EXISTS `".DB_PREFIX."_clean_cache`");

$db->Execute("CREATE TABLE `".DB_PREFIX."_clean_cache` (
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `type`      varchar(64) NOT NULL,
    `cache_type_desc` varchar(512) DEFAULT NULL,
    `last_run`  datetime    DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_UNIQUE` (`type`)
)");

$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (1, 'all','All Caches',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (2, 'cache_feed','Cache Feed',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (3, 'cache_category','Cache Category',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (4, 'cache_css','Cache CSS',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (5, 'cache_js','Cache Javascript',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (6, 'templates_c','Cache Templates',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (7, 'cache_seo','Cache SEO Optimization',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (8, 'adodb_logsql','DB adodb_logsql',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (9, 'system_log_cronjob','DB system_log cronjob',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (10,'system_log_xt_export','DB system_log xt_export',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (11,'system_log_xt_im_export','DB system_log xt_im_export',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (12,'system_log_email','DB system_log email',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (13,'system_log_ImageProcessing','DB system_log ImageProcessing',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`id`,`type`,`cache_type_desc`,`last_run`) VALUES (14,'clean_cache_logs','DB clean_cache_log',NULL)");

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text IN ('xt_cleancache','xt_cleancache_types','xt_cleancache_logs')");

$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_cleancache_types',      'images/icons/page_save.png', '&plugin=xt_cleancache&load_section=xt_cleancache_types', 'adminHandler.php', '4000', 'systemroot', 'G', 'W');");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_cleancache_logs',       'images/icons/folder.png',    '&plugin=xt_cleancache&load_section=xt_cleancache_logs', 'adminHandler.php', '4020', 'xt_cleancache_types', 'I', 'W');");
