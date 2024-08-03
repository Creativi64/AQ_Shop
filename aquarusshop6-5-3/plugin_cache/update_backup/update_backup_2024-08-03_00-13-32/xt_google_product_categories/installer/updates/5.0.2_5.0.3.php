<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if (!$this->_FieldExists('products_mpn',TABLE_PRODUCTS))
{
    $sql = "ALTER TABLE ".TABLE_PRODUCTS. " ADD COLUMN `products_mpn` VARCHAR(512) NULL AFTER manufacturers_id";
    $db->Execute($sql);
}

// LZB-429-62909  evtl fehlt bei upd xt 4.1.0 auf 5.1 die spalte da irgendwie die plg version 5 installiert wurde
$sql = "
ALTER TABLE ".TABLE_PRODUCTS. "
CHANGE COLUMN `google_product_cat` `google_product_cat` INT NULL DEFAULT NULL";
$db->Execute($sql);
