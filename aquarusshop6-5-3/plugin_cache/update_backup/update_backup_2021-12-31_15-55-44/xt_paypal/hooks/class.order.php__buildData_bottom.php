<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($order_data["order_data"]["payment_code"] == 'xt_paypal')
{
    $sql = "SELECT transaction_id FROM ".TABLE_CALLBACK_LOG. " WHERE module='xt_paypal' AND orders_id=?";
    $order_data["order_data"]['transaction_id'] = $db->GetOne($sql, array($oID));
}