<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("ALTER TABLE ".DB_PREFIX."_products_reviews
CHANGE COLUMN `review_text` `review_text` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL ,
CHANGE COLUMN `review_title` `review_title` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL,
CHANGE COLUMN `admin_comment` `admin_comment` TEXT CHARACTER SET 'utf8mb4' NULL DEFAULT NULL
");