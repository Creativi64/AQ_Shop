<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if ($this->_FieldExists('products_serials',TABLE_PRODUCTS))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." DROP `products_serials`");
}