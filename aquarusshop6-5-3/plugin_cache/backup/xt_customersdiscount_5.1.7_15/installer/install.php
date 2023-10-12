<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

if (!$this->_FieldExists('customers_status_discount_flag',TABLE_CUSTOMERS_STATUS))
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." ADD `customers_status_discount_flag` INT( 1 ) NOT NULL DEFAULT '0';");

if (!$this->_FieldExists('customers_discount',TABLE_CUSTOMERS_STATUS))
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." ADD `customers_discount` VARCHAR( 64 ) NOT NULL DEFAULT '';");

if (!$this->_FieldExists('group_discount_allowed',TABLE_PRODUCTS))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." ADD `group_discount_allowed` INT( 1 ) NOT NULL DEFAULT '1';");