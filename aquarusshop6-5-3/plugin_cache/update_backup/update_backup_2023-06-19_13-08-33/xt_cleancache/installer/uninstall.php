<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text IN ('xt_cleancache','xt_cleancache_types','xt_cleancache_logs')");

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_clean_cache");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_clean_cache_logs");
