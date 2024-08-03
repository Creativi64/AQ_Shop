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

if(array_key_exists($_packagingCostDbId, $_SESSION['cart']->content)){
    
    $_SESSION['cart']->content[$_packagingCostDbId]["products_quantity"] = 1;
    $_SESSION['cart']->_refresh();

    if(count($_SESSION['cart']->content) == 1){
        $xtLink->_redirect($xtLink->_link(array('page'=>'cart')));
    }
}
?>