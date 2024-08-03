<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('REVIEWS_UPDATER', true);

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/hooks/page_registry_php_bottom.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/installer/install.php';

global $db;

$idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_status'");
if(empty($idx))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_status` (`review_status` ASC)");

$idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_customers_id'");
if(empty($idx))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_customers_id` (`customers_id` ASC)");

$idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_products_id'");
if(empty($idx))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_products_id` (`products_id` ASC)");

$idx = $db->GetOne("SHOW INDEXES FROM ".TABLE_PRODUCTS_REVIEWS. " WHERE Key_name = 'idx_orders_id'");
if(empty($idx))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_orders_id` (`orders_id` ASC)");

/** @var $this plugin_installed */
if(!$this->_FieldExists('shop_id', TABLE_PRODUCTS_REVIEWS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS. " ADD COLUMN `shop_id` INT DEFAULT '0' AFTER orders_id");
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_REVIEWS." ADD INDEX `idx_shop_id` (`shop_id` ASC)");
}

$langs = array('de', 'en');
$content_dir = _SRV_WEBROOT . 'plugins/xt_reviews/installer/content/';
xt_reviews_install_content($langs, $content_dir);

xt_reviews_update_order_ids();
