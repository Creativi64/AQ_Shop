<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $add_type  */
/** @var $reduce_stock  */
/** @var $data  */
/** @var $db  */



$master_model = $db->GetOne('SELECT products_master_model FROM '.TABLE_PRODUCTS. ' WHERE products_id = ?',[$data['products_id']]);
if(!empty($master_model))
{
    xt_master_slave_functions::updateMainQuantityFromVariant($master_model);
}

