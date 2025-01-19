<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_aq_timed_banner");
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_aq_timed_banner_description");
