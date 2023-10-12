<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $kp_reauthorize_required;
$kp_reauthorize_required = false;

if($_REQUEST['page_action'] == 'confirmation' &&
    $_REQUEST['special'] == 'coupon' &&
    !empty($_REQUEST['coupon_code']) &&
    !empty($_SESSION["sess_coupon"]) &&
    !empty($_SESSION["kp_session"]["session_id"]) &&
    $_SESSION['kp_session_coupon_code'] != $_REQUEST['coupon_code']
)
{
    $errors = [];
    $kp = klarna_kp::updatePaymentsSession($_SESSION["kp_session"]["session_id"], $_SESSION['cart'], $_SESSION['customer'], $customers_status, $errors);
    $_SESSION['kp_session_coupon_code'] = $_REQUEST['coupon_code'];
    $kp_reauthorize_required = $_SESSION["kp_selected_payment_method_category"];
}
