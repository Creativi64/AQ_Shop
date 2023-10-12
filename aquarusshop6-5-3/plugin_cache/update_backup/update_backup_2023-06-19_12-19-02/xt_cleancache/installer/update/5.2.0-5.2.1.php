<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (!defined("TABLE_CLEANCACHE"))
    define('TABLE_CLEANCACHE', DB_PREFIX.'_clean_cache');
if (!defined("TABLE_CLEANCACHE_LOGS"))
    define('TABLE_CLEANCACHE_LOGS', DB_PREFIX.'_clean_cache_logs');

global $db;

$db->Execute('DELETE FROM '. TABLE_CLEANCACHE." WHERE type='adodb_cache'");
$db->Execute('INSERT INTO '. TABLE_CLEANCACHE." (`type`,`cache_type_desc`,`last_run`) VALUES ('adodb_cache','ADOdb Cache',NULL)" );

$db->Execute('DELETE FROM '. TABLE_CLEANCACHE." WHERE type='xt_cache'");
$db->Execute('INSERT INTO '. TABLE_CLEANCACHE." (`type`,`cache_type_desc`,`last_run`) VALUES ('xt_cache','XT Cache',NULL)" );

$db->Execute('DELETE FROM '. TABLE_CLEANCACHE." WHERE type='plg_hookpoints'");
$db->Execute('INSERT INTO '. TABLE_CLEANCACHE." (`type`,`cache_type_desc`,`last_run`) VALUES ('plg_hookpoints','Plugin hookpoints',NULL)" );
