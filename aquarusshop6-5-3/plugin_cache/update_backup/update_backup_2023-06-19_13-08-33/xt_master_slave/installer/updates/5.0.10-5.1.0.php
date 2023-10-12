<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

// check for index
$sql = "SHOW INDEX FROM ".TABLE_PRODUCTS." WHERE Key_name = 'products_model'";
$c = $db->GetArray($sql);
if(count($c) == 0)
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD INDEX `products_model` (`products_model` ASC)");
}

$db->Execute("ALTER TABLE ".TABLE_PRODUCTS." MODIFY products_master_model VARCHAR( 255 ) CHARACTER SET utf8 NULL  AFTER products_master_flag");

if (!$this->_FieldExists('ms_load_masters_free_downloads', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_load_masters_free_downloads TINYINT( 1 ) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('ms_load_masters_main_img', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_load_masters_main_img TINYINT( 1 ) NULL AFTER products_master_model");
}

if (!$this->_FieldExists('products_keywords_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_keywords_from_master TINYINT( 1 ) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_short_description_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_short_description_from_master TINYINT( 1 ) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('products_description_from_master', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD products_description_from_master TINYINT( 1 ) NULL AFTER products_master_model");
}


if (!$this->_FieldExists('ms_filter_slave_list', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_filter_slave_list TINYINT( 1 ) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('ms_show_slave_list', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_show_slave_list TINYINT( 1 ) NULL AFTER products_master_model");
}
if (!$this->_FieldExists('ms_open_first_slave', TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD ms_open_first_slave TINYINT( 1 ) NULL AFTER products_master_model");
}

$valExists = $db->GetOne("SELECT 1 FROM ".TABLE_CONFIGURATION." WHERE config_key='_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT' ");
if (!$valExists)
    $db->Execute("INSERT INTO ".TABLE_CONFIGURATION." (`config_key`, `config_value`, `group_id`, `sort_order`, `type`, `url`) VALUES ('_SYSTEM_ADMIN_PAGE_SIZE_SLAVE_PRODUCT',       '100', 25, 30, NULL, NULL);");

if (!defined('XT_WIZARD_STARTED'))
{
    $output .= "<div style='border:1px solid #009900; background:#BDFFA9;padding:10px;'>";
    $output .=  'Bitte aktivieren Sie nun das Plugin und laden Sie dann das Browser-Fenster neu.<br />Please activate plugin and then reload browser window';
    $output .=  "</div>";
}

