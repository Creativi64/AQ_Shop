<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
# require _SRV_WEBROOT.'plugins/aq_plugin/classes/class.cart.php';

$packagingCostId = (int)AQ_PACKAGE_COST_ARTICEL_ID;
$packagingCostDbId;
global $db;

$sql = "SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model = ?";
$record = $db->Execute($sql, array((int) $packagingCostId));
if ($record->RecordCount() > 0) {
    $packagingCostDbId = (int) $record->fields['products_id'];
}

if ($data['product'] == $packagingCostDbId) {
    
    return false;
} 

$packingCost = [
    "product" => $packagingCostDbId,
    "qty" => 1
];

$checkForPackagingCost = $this->_checkAddType($packingCost);

if ($checkForPackagingCost['type'] == 'insert') {
    $this->_addToCart($packingCost);
}
function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

?>