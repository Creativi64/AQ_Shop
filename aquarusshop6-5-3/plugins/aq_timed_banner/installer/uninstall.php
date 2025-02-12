<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

// Remove admin navigation entry
$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text = 'aq_timed_banner'");

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_aq_timed_banner");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_aq_timed_banner_description");
