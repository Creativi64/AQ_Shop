<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($this->_FieldExists('customers_status_discount_flag',TABLE_CUSTOMERS_STATUS))
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." DROP `customers_status_discount_flag`");

if ($this->_FieldExists('customers_discount',TABLE_CUSTOMERS_STATUS))
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS_STATUS." DROP `customers_discount`");

if ($this->_FieldExists('group_discount_allowed',TABLE_PRODUCTS))
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS." DROP `group_discount_allowed`");