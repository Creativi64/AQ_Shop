<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_SESSION['selected_payment']=='xt_banktransfer'){
    if(is_data($_SESSION['xt_banktransfer_data'])){
        $payment_module_data->write_order_data($processed_data['orders_id'], $_SESSION['xt_banktransfer_data']);
    }
}
