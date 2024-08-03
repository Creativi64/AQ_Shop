<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if(!$this->_FieldExists('sort_order', TABLE_PRODUCTS_CROSS_SELL))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_CROSS_SELL." ADD COLUMN `sort_order` INT(4) NULL DEFAULT NULL");
}