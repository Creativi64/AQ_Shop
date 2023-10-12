<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

// DROP / TRUNCATE
$db->Execute("DROP TABLE IF EXISTS ".DB_PREFIX."_tmp_products");
$db->Execute("TRUNCATE ".DB_PREFIX."_tmp_plg_products_to_attributes");

// TINYINT's
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes CHANGE COLUMN `status` `status` TINYINT UNSIGNED DEFAULT 1;');

$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `products_master_flag` `products_master_flag` TINYINT UNSIGNED DEFAULT 0;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `products_description_from_master` `products_description_from_master` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `products_short_description_from_master` `products_short_description_from_master` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `products_keywords_from_master` `products_keywords_from_master` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `ms_load_masters_main_img` `ms_load_masters_main_img` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `ms_load_masters_free_downloads` `ms_load_masters_free_downloads` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `products_image_from_master` `products_image_from_master` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `ms_filter_slave_list` `ms_filter_slave_list` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `ms_show_slave_list` `ms_show_slave_list` TINYINT UNSIGNED DEFAULT 2;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_products CHANGE COLUMN `ms_open_first_slave` `ms_open_first_slave` TINYINT UNSIGNED DEFAULT 2;');

if (!$this->_FieldExists('ms_filter_slave_list_hide_on_product', DB_PREFIX.'_products'))
{
    $db->Execute('ALTER TABLE ' . DB_PREFIX . '_products ADD COLUMN `ms_filter_slave_list_hide_on_product` TINYINT UNSIGNED DEFAULT 2 AFTER ms_filter_slave_list;');
}

// INT'S
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes CHANGE COLUMN `attributes_id` `attributes_id` INT UNSIGNED NOT NULL auto_increment;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes CHANGE COLUMN `attributes_parent` `attributes_parent` INT UNSIGNED NULL DEFAULT 0;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes CHANGE COLUMN `attributes_templates_id` `attributes_templates_id` INT NOT NULL;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes CHANGE COLUMN `sort_order` `sort_order` INT DEFAULT 0;');

$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes_description CHANGE COLUMN `attributes_id` `attributes_id` INT NOT NULL;');

$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_to_attributes CHANGE COLUMN `products_id` `products_id` INT NOT NULL;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_to_attributes CHANGE COLUMN `attributes_id` `attributes_id` INT NOT NULL;');
$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_to_attributes CHANGE COLUMN `attributes_parent_id` `attributes_parent_id` INT NOT NULL;');

$db->Execute('ALTER TABLE '.DB_PREFIX.'_plg_products_attributes_templates CHANGE COLUMN `attributes_templates_id` `attributes_templates_id` INT NOT NULL auto_increment;');


$db->Execute('DELETE p2a 
FROM '.DB_PREFIX.'_plg_products_to_attributes p2a
INNER JOIN '.DB_PREFIX.'_products p ON p.products_id = p2a.products_id
WHERE p.products_master_flag = 1');
