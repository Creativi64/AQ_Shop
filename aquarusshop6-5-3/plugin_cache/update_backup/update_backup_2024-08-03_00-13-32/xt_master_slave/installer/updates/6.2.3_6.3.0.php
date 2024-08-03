<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $this plugin */

if (!$this->_FieldExists('load_mains_imgs', TABLE_PRODUCTS))
{
    $db->Execute('ALTER TABLE ' . TABLE_PRODUCTS . ' ADD COLUMN `load_mains_imgs` TINYINT UNSIGNED DEFAULT 2 AFTER ms_load_masters_main_img;');
}
if ($this->_FieldExists('load_masters_img', TABLE_PRODUCTS))
{
    $db->Execute('ALTER TABLE ' . TABLE_PRODUCTS . ' DROP COLUMN `load_masters_img`');
}

if (!$this->_FieldExists('sum_quantity_for_graduated_price', TABLE_PRODUCTS))
{
    $db->Execute('ALTER TABLE ' . TABLE_PRODUCTS . ' ADD COLUMN `sum_quantity_for_graduated_price` TINYINT UNSIGNED DEFAULT 2 AFTER load_mains_imgs;');
}
