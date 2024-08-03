<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute('ALTER TABLE '. DB_PREFIX.'_clean_cache'.' CHANGE COLUMN `type` `type` VARCHAR(32) NOT NULL ;');
