<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

/** @var $id string|int */


$products_master_model= $db->GetOne('SELECT products_master_model FROM '.TABLE_PRODUCTS. ' WHERE products_id = ?', [$id]);

if($products_master_model)
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_master_slave/classes/class.xt_master_slave_functions.php';

    xt_master_slave_functions::updateMainQuantityFromVariant($products_master_model);
}