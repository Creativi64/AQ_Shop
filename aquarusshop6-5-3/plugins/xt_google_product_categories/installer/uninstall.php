<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_google_product_categories/classes/class.xt_google_product_categories.php';

if ($this->_FieldExists('google_product_cat',TABLE_PRODUCTS)){
    $db->Execute("ALTER TABLE `".DB_PREFIX."_products` DROP `google_product_cat`;");
}
if ($this->_FieldExists('google_product_cat_BAK',TABLE_PRODUCTS)){
    $db->Execute("ALTER TABLE `".DB_PREFIX."_products` DROP `google_product_cat_BAK`;");
}
if ($this->_FieldExists('google_product_cat',TABLE_CATEGORIES)){
    $db->Execute("ALTER TABLE `".DB_PREFIX."_categories` DROP `google_product_cat`;");
}
if ($this->_FieldExists('google_product_cat_BAK',TABLE_CATEGORIES)){
    $db->Execute("ALTER TABLE `".DB_PREFIX."_categories` DROP `google_product_cat_BAK`;");
}

$db->Execute("
    DROP TABLE IF EXISTS ".TABLE_GOOGLE_CATEGORIES."
    ");

$db->Execute("DELETE FROM ".TABLE_CRON." WHERE cron_action='file:cron.google_categories.php'");
unlink(_SRV_WEBROOT._SRV_WEB_CRONJOBS.'cron.google_categories.php');