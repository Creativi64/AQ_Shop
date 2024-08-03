<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if (!$this->_FieldExists('customers_discount',TABLE_CUSTOMERS_STATUS))
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." ADD `customers_discount` VARCHAR( 64 ) NOT NULL DEFAULT '';");
else
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." CHANGE COLUMN `customers_discount` `customers_discount` VARCHAR(64) NOT NULL DEFAULT '';");



