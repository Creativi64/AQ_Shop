<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (!defined("TABLE_CLEANCACHE"))
    define('TABLE_CLEANCACHE', DB_PREFIX.'_clean_cache');
if (!defined("TABLE_CLEANCACHE_LOGS"))
    define('TABLE_CLEANCACHE_LOGS', DB_PREFIX.'_clean_cache_logs');

global $db;

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_clean_cache");

$db->Execute("CREATE TABLE `".DB_PREFIX."_clean_cache` (
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `type_class`  varchar(64) NOT NULL,
    `type`   varchar(64) NOT NULL,
    `cache_type_desc` varchar(512) DEFAULT NULL,
    `last_run`  datetime    DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_UNIQUE` (`type`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('all_files', 'files', 'TEXT_CLEAN_CACHE_ALL_FILES', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('dir_templates_c_cache', 'files', 'TEXT_CLEAN_CACHE_DIR_TEMPLATES_C_CACHE', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('dir_templates_c', 'files', 'TEXT_CLEAN_CACHE_DIR_TEMPLATES_C', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('dir_cache', 'files', 'TEXT_CLEAN_CACHE_DIR_CACHE', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_css', 'files', 'TEXT_CLEAN_CACHE_CSS', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_js', 'files', 'TEXT_CLEAN_CACHE_JS', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_category', 'files', 'TEXT_CLEAN_CACHE_CATEGORY', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_product', 'files', 'TEXT_CLEAN_CACHE_PRODUCT', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_manufacturers', 'files', 'TEXT_CLEAN_CACHE_MANUFACTURER', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('adodb_cache', 'files', 'TEXT_CLEAN_CACHE_ADODB', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('xt_cache', 'files', 'TEXT_CLEAN_CACHE_XT_CACHE', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('xt_cache_language', 'files', 'TEXT_CLEAN_CACHE_LANGUAGE', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('plg_hookpoints', 'files', 'TEXT_CLEAN_CACHE_HOOKPOINTS', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('cache_feed', 'files', 'TEXT_CLEAN_CACHE_FEED', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('opcache', 'files', 'TEXT_CLEAN_CACHE_OPCACHE', NULL)");

$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('all_db', 'db', 'TEXT_CLEAN_CACHE_ALL_DB', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('adodb_logsql', 'db', 'TEXT_CLEAN_CACHE_SQL_LOG', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('system_log_cronjob', 'db', 'TEXT_CLEAN_CACHE_CRONJOB_LOG', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('system_log_xt_export', 'db', 'TEXT_CLEAN_CACHE_XT_EXPORT_LOG', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('system_log_xt_im_export', 'db', 'TEXT_CLEAN_CACHE_XT_IM_EXPORT_LOG', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('system_log_email', 'db', 'TEXT_CLEAN_CACHE_MAIL_LOG', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('system_log_ImageProcessing', 'db', 'TEXT_CLEAN_CACHE_IMAGE_PROCESSING', NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`type_class`, `cache_type_desc`,`last_run`) VALUES ('clean_cache_logs', 'db', 'TEXT_CLEAN_CACHE_SELF_LOGS', NULL)");
