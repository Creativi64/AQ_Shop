<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true) {
    require_once _SRV_WEBROOT . 'plugins/xt_paypal/classes/class.paypal.php';
    $filter_paypal = new paypal();
    $value = $filter_paypal->filterPayments($value);
}
?>