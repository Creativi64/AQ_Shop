<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

function XtCustomersDiscount_orderEditActive()
{
    $oePages = array('addOrderItem','removeOrderItem','applyExistingAddress','apply','updateOrderItem', 'editCoupon', 'applyNewAddress', 'applyEditedAddress', 'applyExistingAddress');

    if(
        ( isset($_GET['plugin']) && isset($_GET['pg']) && $_GET['plugin']=='order_edit' && in_array($_GET['pg'], $oePages))
        ||
        ( isset($_POST['plugin']) && isset($_POST['pg']) && $_POST['plugin']=='order_edit' && in_array($_POST['pg'], $oePages))
    )
    {
        return true;
    }
    return false;
}
