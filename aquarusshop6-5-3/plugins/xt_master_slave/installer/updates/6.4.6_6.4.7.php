<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_master_slave/classes/class.xt_master_slave_functions.php';

$master_models = $db->GetArray('SELECT products_model FROM '.TABLE_PRODUCTS.' WHERE products_master_flag = 1');
foreach($master_models as $mm)
{
    $master_products_quantity = $db->GetOne('SELECT SUM(products_quantity) FROM '.TABLE_PRODUCTS.' WHERE products_master_model = ? and products_quantity > 0', [$mm['products_model']]);
    $db->Execute('UPDATE '.TABLE_PRODUCTS.' SET products_quantity = ? WHERE products_model = ?', [$master_products_quantity, $mm['products_model']]);

    $master_products_ordered = $db->GetOne('SELECT SUM(products_ordered) FROM '.TABLE_PRODUCTS.' WHERE products_master_model = ?', [$mm['products_model']]);
    $db->Execute('UPDATE '.TABLE_PRODUCTS.' SET products_ordered = ? WHERE products_model = ?', [$master_products_ordered, $mm['products_model']]);
}
