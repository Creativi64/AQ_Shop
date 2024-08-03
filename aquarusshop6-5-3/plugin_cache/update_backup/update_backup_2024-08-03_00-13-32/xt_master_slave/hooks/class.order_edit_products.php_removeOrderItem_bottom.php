<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var  $delete_pId int */

$master_model = $db->GetOne('SELECT products_master_model FROM ' . TABLE_PRODUCTS . ' WHERE products_id = ?', [$delete_pId]);
if (!empty($master_model))
{
    xt_master_slave_functions::updateMainQuantityFromVariant($master_model);
}