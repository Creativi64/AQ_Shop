<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $this plugin */

if ($this->_TableExists(DB_PREFIX.'_tmp_products') && !$this->_FieldExists('products_master_slave_order', DB_PREFIX.'_tmp_products'))
{
    $db->Execute("ALTER TABLE ".DB_PREFIX.'_tmp_products'." ADD products_master_slave_order INT NOT NULL AFTER products_master_model");
}
