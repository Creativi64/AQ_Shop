<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_SESSION['selected_payment']=='xt_banktransfer'){
    if(is_data($_SESSION['xt_banktransfer_data'])){
        $data = $_SESSION['xt_banktransfer_data'];

        $banktransferValidationReturnValue = $payment_module_data->_banktransferValidation($data);
        $data = $banktransferValidationReturnValue['data'];
        $error_data = $banktransferValidationReturnValue['error'];

        if (count($error_data) > 0) {
            $error = true;
            $checkout_data['page_action'] = 'payment';
        } else {
            $_SESSION['xt_banktransfer_data'] = $data;
        }
    }
}
