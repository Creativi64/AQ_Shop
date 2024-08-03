<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($_SESSION['easy_creditInstallmentsCheckout'] == true && $type=='complete' && $add_type=='insert')
{
    if ($_SESSION['easy_credit'])
    {
        global $db;

        $easy_credit_insert_data = array($this->oID, serialize($_SESSION['reshash']));

        $db->Execute('INSERT INTO '.TABLE_EASY_CREDIT_FINANCING. " (orders_id, rehash) VALUES (?,?)", $easy_credit_insert_data);
    }
    else {
        error_log('xt_easy_credit set_order warning: Expected to find financing plan information but not found in session');
    }
}