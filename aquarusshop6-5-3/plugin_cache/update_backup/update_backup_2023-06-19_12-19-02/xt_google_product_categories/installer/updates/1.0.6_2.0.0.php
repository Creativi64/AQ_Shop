<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.xt_google_product_categories.php';

global $db;

// backup products table
if (!$this->_FieldExists('google_product_cat_BAK',TABLE_PRODUCTS))
{
    $sql = "ALTER TABLE ".TABLE_PRODUCTS. " ADD COLUMN `google_product_cat_BAK` VARCHAR(2048)  CHARACTER SET utf8 COLLATE  utf8_unicode_ci AFTER `google_product_cat`";
    $db->Execute($sql);
    $sql = "UPDATE ".TABLE_PRODUCTS. " SET `google_product_cat_BAK`=`google_product_cat`";
    $db->Execute($sql);
}

// backup categories table
if (!$this->_FieldExists('google_product_cat_BAK',TABLE_CATEGORIES))
{
    $sql = "ALTER TABLE ".TABLE_CATEGORIES. " ADD COLUMN `google_product_cat_BAK` VARCHAR(2048) CHARACTER SET utf8 COLLATE  utf8_unicode_ci AFTER `google_product_cat`";
    $db->Execute($sql);
    $sql = "UPDATE ".TABLE_CATEGORIES. " SET `google_product_cat_BAK`=`google_product_cat`";
    $db->Execute($sql);
}


$sql = "
CREATE TABLE IF NOT EXISTS `".TABLE_GOOGLE_CATEGORIES."` (
  `google_category_id` INT NOT NULL,
  `language` VARCHAR(2) NOT NULL,
  `country` VARCHAR(2) NOT NULL,
  `sort_order` INT NOT NULL,
  `category_path` VARCHAR(2048) NOT NULL,
  PRIMARY KEY (`google_category_id`, `language`, `country`)) CHARACTER SET utf8 COLLATE  utf8_unicode_ci;
";
$db->Execute($sql);


// alter columns
$sql = "
ALTER TABLE ".TABLE_CATEGORIES. "
CHANGE COLUMN `google_product_cat` `google_product_cat` INT NULL DEFAULT NULL";
$db->Execute($sql);


// load taxonomy
if (!defined('XT_WIZARD_STARTED'))
{
    $hint = "Loading files from Google Server. This can take while, please wait...";
    $output.=  "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.=  $hint;
    $output.=  "</div>";
}
$gpc = new google_product_categories();
$gpc->_run_cron(array());

// C R O N
$cron_data = array(
    "cron_id" => "",
    "cron_note" => "Google Product Categories",
    "cron_value" => "1",
    "cron_type" => "m",
    "hour" => "",
    "minute" => "",
    "cron_action" => "file:cron.google_categories.php",
    "cron_parameter" => ""
);
$cron = new xt_cron();
$cron->setPosition('admin');
$cron->_set($cron_data, 'add');

$f_source = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/cronjob/cron.google_categories.php';
$f_target = _SRV_WEBROOT._SRV_WEB_CRONJOBS.'cron.google_categories.php';
copy($f_source, $f_target);
if (!file_exists($f_target))
{
    $hint = "Installer tried to copy cron file but failed.<br />You have to copy manually plugins/xt_google_product_categories/cronjob/cron.google_categories.php to <br/> cronjobs/cron.google_categories.php";
    $output.= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.= $hint;
    $output.= "</div>";
}


// try to update existing product entries
if (!defined('XT_WIZARD_STARTED'))
{
    $hint = "Updating products. This can take while, please wait...";
    $output.= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.= $hint;
    $output.= "</div>";
}
$sql = "update ".TABLE_PRODUCTS." p
left join ".TABLE_GOOGLE_CATEGORIES." g on g.category_path like p.google_product_cat_BAK
set p.google_product_cat = g.google_category_id
where p.google_product_cat_BAK!='' AND p.google_product_cat_BAK IS NOT NULL
";
$db->Execute($sql);

$sql = "update ".TABLE_PRODUCTS." p
set p.google_product_cat = NULL
where p.google_product_cat=0
";
$db->Execute($sql);
if (!defined('XT_WIZARD_STARTED'))
{
// try to update existing category entries
    $hint = "Updating categories. This can take while, please wait...";
    $output.=  "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.=  $hint;
    $output.=  "</div>";
}
$sql = "update ".TABLE_CATEGORIES." p
left join ".TABLE_GOOGLE_CATEGORIES." g on g.category_path like p.google_product_cat_BAK
set p.google_product_cat = g.google_category_id
where p.google_product_cat_BAK!='' AND p.google_product_cat_BAK IS NOT NULL
";
$db->Execute($sql);
$sql = "update ".TABLE_CATEGORIES." p
set p.google_product_cat = NULL
where p.google_product_cat=0
";
$db->Execute($sql);