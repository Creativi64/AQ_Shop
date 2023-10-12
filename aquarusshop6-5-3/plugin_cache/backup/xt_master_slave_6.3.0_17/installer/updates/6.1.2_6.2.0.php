<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if (!$this->_FieldExists('load_mains_imgs', DB_PREFIX.'_products'))
{
    $db->Execute('ALTER TABLE ' . DB_PREFIX . '_products ADD COLUMN `load_mains_imgs` TINYINT UNSIGNED DEFAULT 2 AFTER ms_load_masters_main_img;');
}
