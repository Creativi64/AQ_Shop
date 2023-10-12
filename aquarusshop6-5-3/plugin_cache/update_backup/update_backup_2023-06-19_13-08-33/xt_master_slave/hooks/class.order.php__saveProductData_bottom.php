<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $add_type  */
/** @var $reduce_stock  */
/** @var $data  */
/** @var $db  */

if($add_type=='insert'){

    $master_model = $db->GetOne('SELECT products_master_model FROM '.TABLE_PRODUCTS. ' WHERE products_id = ?',[$data['products_id']]);
    if(!empty($master_model))
    {
        $master_products_id =  $db->GetOne('SELECT products_id FROM '.TABLE_PRODUCTS. ' WHERE products_model = ? and products_master_flag = 1', [$master_model]);
        if($master_products_id)
        {
            if ($reduce_stock) {
                $stock = new stock();
                $stock->removeStock($master_products_id, $data['products_quantity']);
            }
            // update products_ordered
            $db->Execute(
                "UPDATE ".TABLE_PRODUCTS." SET products_ordered=products_ordered+? WHERE products_id=?",
                [$data['products_quantity'], $master_products_id]
            );
            $db->Execute(
                "UPDATE ".TABLE_PRODUCTS." SET products_transactions=products_transactions+1 WHERE products_id=?",
                [$master_products_id]
            );
        }
    }
}
