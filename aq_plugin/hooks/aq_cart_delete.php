<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

$packagingCostId = (int)AQ_PACKAGE_COST_ARTICEL_ID;
$packagingCostDbId;
global $db;

$sql = "SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model = ?";
$record = $db->Execute($sql, array((int) $packagingCostId));
if ($record->RecordCount() > 0) {
    $packagingCostDbId = (int) $record->fields['products_id'];
}

$_packagingCostDbId = $_SESSION['cart']->_genProductsKey(["product" =>$packagingCostDbId]);

if ($data_array["cart_delete"] == array($_packagingCostDbId)) {
   
    if($data_array["products_key"] !== array($_packagingCostDbId)){
        $data_array['cart_delete'] = array();
    }
}
?>