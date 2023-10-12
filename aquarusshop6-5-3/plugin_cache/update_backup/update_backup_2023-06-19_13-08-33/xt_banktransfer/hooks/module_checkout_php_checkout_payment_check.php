<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_POST['selected_payment']=='xt_banktransfer'){
    if($_POST['acID'] != '0' && isset($_POST['acID'])){
        $xt_banktransfer_pre_data = $payment_module_data->getAccountData($_POST['acID'], $_SESSION['customer']->customers_id);
    }else{
        $xt_banktransfer_pre_data = $_POST;
    }

    $xt_banktransfer_pre_data['banktransfer_iban'] = trim(str_replace(' ','',$xt_banktransfer_pre_data['banktransfer_iban']));
    $xt_banktransfer_pre_data['banktransfer_bic'] = trim(str_replace(' ','',$xt_banktransfer_pre_data['banktransfer_bic']));

    $_SESSION['xt_banktransfer_data'] = array('customer_id'       => $_SESSION['customer']->customers_id,
        'banktransfer_owner'      => $filter->_filter($xt_banktransfer_pre_data['banktransfer_owner']),
        'banktransfer_bank_name'  => $filter->_filter($xt_banktransfer_pre_data['banktransfer_bank_name']),
        'banktransfer_iban'       => $filter->_filter($xt_banktransfer_pre_data['banktransfer_iban']),
        'banktransfer_bic'        => $filter->_filter($xt_banktransfer_pre_data['banktransfer_bic']),
        'banktransfer_save'       => $filter->_filter($xt_banktransfer_pre_data['banktransfer_save']));
} else {

    if (isset($_SESSION['xt_banktransfer_data'])) unset($_SESSION['xt_banktransfer_data']);
}

// bugfix 0000624
if($_POST['selected_payment']!='xt_banktransfer'){
    if (isset($_SESSION['xt_banktransfer_data'])) unset($_SESSION['xt_banktransfer_data']);
}
