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

$sql = "SHOW INDEX FROM ".TABLE_PRODUCTS." WHERE Key_name = 'products_master_model'";
$c = $db->GetArray($sql);
if(count($c) == 0)
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD INDEX `products_master_model` (`products_master_model` ASC)");
}

