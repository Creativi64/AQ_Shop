<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.xt_google_product_categories.php';

global $db;

if(!defined('DB_STORAGE_ENGINE'))
{
    $sel_engine = 'innodb';
    $sql_version = $db->GetOne("SELECT VERSION() AS Version");
    if(version_compare($sql_version, '5.6') == -1)
    {
        $sel_engine = 'myisam';
    }
    define('DB_STORAGE_ENGINE', $sel_engine);
}

if (!$this->_FieldExists('google_product_cat',TABLE_PRODUCTS))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_products` ADD `google_product_cat` INT;");
else {
    $sql = "
        ALTER TABLE ".TABLE_PRODUCTS. "
        CHANGE COLUMN `google_product_cat` `google_product_cat` INT NULL DEFAULT NULL";
    $db->Execute($sql);
}

if (!$this->_FieldExists('google_product_cat',TABLE_CATEGORIES))
    $db->Execute("ALTER TABLE `".DB_PREFIX."_categories` ADD `google_product_cat` INT;");
else {
    $sql = "
ALTER TABLE ".TABLE_CATEGORIES. "
CHANGE COLUMN `google_product_cat` `google_product_cat` INT NULL DEFAULT NULL";
    $db->Execute($sql);
}

if (!$this->_FieldExists('products_mpn',TABLE_PRODUCTS))
{
    $sql = "ALTER TABLE ".TABLE_PRODUCTS. " ADD COLUMN `products_mpn` VARCHAR(512) NULL AFTER manufacturers_id";
    $db->Execute($sql);
}

$sql = "
CREATE TABLE IF NOT EXISTS `".TABLE_GOOGLE_CATEGORIES."` (
  `google_category_id` INT NOT NULL,
  `language` VARCHAR(2) NOT NULL,
  `country` VARCHAR(2) NOT NULL,
  `sort_order` INT NOT NULL,
  `category_path` VARCHAR(2048) NOT NULL,
  PRIMARY KEY (`google_category_id`, `language`, `country`)
  ) ENGINE=".DB_STORAGE_ENGINE."  DEFAULT CHARSET=utf8;
";
$db->Execute($sql);


// load taxonomy
if (!defined('XT_WIZARD_STARTED'))
{
    $hint = "Loading files from Google Server. This can take a while, please wait...";
    $output.= "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.=  $hint;
    $output.=  "</div>";
}
$gpc = new google_product_categories();
$gpc->_run_cron(array());

// C R O N
$data = array(
    "cron_id" => "",
    "cron_note" => "Google Product Categories",
    "cron_value" => "1",
    "cron_type" => "m",
    "hour" => "",
    "minute" => "",
    "cron_action" => "file:cron.google_categories.php",
    "cron_parameter" => "",
    "active_status" => 1
);
$cron = new xt_cron();
$cron->setPosition('admin');
$cron->_set($data, 'add');

$f_source = _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/cronjob/cron.google_categories.php';
$f_target = _SRV_WEBROOT._SRV_WEB_CRONJOBS.'cron.google_categories.php';
copy($f_source, $f_target);
if (!file_exists($f_target) && XT_WIZARD_STARTED !== true)
{
    $hint = "Installer tried to copy cron file but failed.<br />You have to copy manually plugins/xt_google_product_categories/cronjob/cron.google_categories.php to <br/> cronjobs/cron.google_categories.php";
    $output.=  "<br /><div style='border:solid 1px #fecf43;background: #ffe086; padding:10px;'>";
    $output.=  $hint;
    $output.=  "</div>";
}