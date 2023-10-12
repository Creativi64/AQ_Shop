<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if(!defined('DB_STORAGE_ENGINE'))
{
    $sel_engine = 'innodb';
    $sql_version = $db->GetOne("SELECT VERSION() AS Version");
    if(version_compare($sql_version, '5.6') == -1)
    {
        $sel_engine = 'myisam';
    }
    define('DB_STORAGE_ENGINE', $sel_engine);
}

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_clean_cache");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_clean_cache_logs");


$db->Execute("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."_clean_cache_logs` (
		`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`type` VARCHAR(64) NOT NULL,
		`change_trigger` VARCHAR(64) NOT NULL,
		`last_run` datetime NOT NULL
		) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");


$db->Execute("CREATE TABLE `".DB_PREFIX."_clean_cache` (
    `id`        int(11)     NOT NULL AUTO_INCREMENT,
    `type`      varchar(64) NOT NULL,
    `cache_type_desc` varchar(512) DEFAULT NULL,
    `last_run`  datetime    DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_UNIQUE` (`type`)
    ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
");

$db->Execute("TRUNCATE ".DB_PREFIX."_clean_cache");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('all','All caches',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('cache_feed','Feed cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('cache_category','Category cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('cache_css','CSS cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('cache_js','Javascript cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('templates_c','Templates Cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('cache_seo','SEO Optimization cache',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('adodb_logsql','DB adodb_logsql',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('system_log_cronjob','DB system_log cronjob',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('system_log_xt_export','DB system_log xt_export',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('system_log_xt_im_export','DB system_log xt_im_export',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('system_log_email','DB system_log email',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('system_log_ImageProcessing','DB system_log ImageProcessing',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('clean_cache_logs','DB clean_cache_log',NULL)");
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('adodb_cache','ADOdb Cache',NULL)" );
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('xt_cache','XT Cache',NULL)" );
$db->Execute("INSERT INTO `".DB_PREFIX."_clean_cache` (`type`,`cache_type_desc`,`last_run`) VALUES ('plg_hookpoints','Plugin hookpoints',NULL)" );


// Navigation anlegen
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('xt_cleancache_types','xt_cleancache_logs')");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_cleancache_types',      'images/icons/page_save.png', '&plugin=xt_cleancache&load_section=xt_cleancache_types', 'adminHandler.php', '4000', 'systemroot', 'G', 'W');");
$db->Execute("INSERT INTO ".TABLE_ADMIN_NAVIGATION." (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'xt_cleancache_logs',       'images/icons/folder.png',    '&plugin=xt_cleancache&load_section=xt_cleancache_logs', 'adminHandler.php', '4020', 'xt_cleancache_types', 'I', 'W');");
