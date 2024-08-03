<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DROP TABLE IF EXISTS ".$DB_PREFIX.'exportimport_log');
