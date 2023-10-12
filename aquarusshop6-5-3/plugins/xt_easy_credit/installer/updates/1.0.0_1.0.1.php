<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_EASY_CREDIT_FINANCING', DB_PREFIX.'_easy_credit_financing');

global $db;

$idxs = $db->GetArray("SHOW INDEXES FROM ".TABLE_EASY_CREDIT_FINANCING. " WHERE Key_name = 'PRIMARY'");
if(count($idxs))
    $db->Execute("ALTER TABLE ".TABLE_EASY_CREDIT_FINANCING. " DROP PRIMARY KEY");
$db->Execute("ALTER TABLE ".TABLE_EASY_CREDIT_FINANCING. " CHANGE `orders_id` `orders_id` INT(11) NOT NULL");
$db->Execute("ALTER TABLE ".TABLE_EASY_CREDIT_FINANCING. " ADD PRIMARY KEY(`orders_id` DESC)");
