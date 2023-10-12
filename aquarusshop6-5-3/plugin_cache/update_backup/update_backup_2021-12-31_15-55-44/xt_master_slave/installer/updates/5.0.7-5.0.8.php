<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT',       '100', 25, 30, NULL, NULL);");