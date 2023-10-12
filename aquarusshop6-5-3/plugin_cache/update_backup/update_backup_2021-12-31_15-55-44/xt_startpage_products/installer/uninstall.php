<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $store_handler;
$plugin = new plugin();
$stores = $store_handler->getStores();

$db->Execute("DROP TABLE " . DB_PREFIX . "_startpage_products;");
$db->Execute("DELETE FROM " . TABLE_ADMIN_NAVIGATION . " WHERE text='xt_startpage_products'");

foreach ($stores as $store) {
    if ($plugin->_FieldExists('products_startpage_' . $store['id'], DB_PREFIX . '_products'))
        $db->Execute("ALTER TABLE " . DB_PREFIX . "_products DROP `products_startpage_" . $store['id'] . "`");

    if ($plugin->_FieldExists('products_startpage_sort_' . $store['id'], DB_PREFIX . '_products'))
        $db->Execute("ALTER TABLE " . DB_PREFIX . "_products DROP `products_startpage_sort_" . $store['id'] . "`");
}