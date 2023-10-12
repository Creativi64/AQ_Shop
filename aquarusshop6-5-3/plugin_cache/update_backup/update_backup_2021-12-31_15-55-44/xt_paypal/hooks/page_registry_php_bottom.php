<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (USER_POSITION == 'store' && constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true) {
    define('PAGE_XT_PAYPAL_CHECKOUT', _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_paypal/pages/xt_paypal_checkout.php');
}

if (isset($xtPlugin->active_modules['xt_paypal'])) {
    define('TABLE_PAYPAL_REFUNDS', DB_PREFIX . '_plg_paypal_refunds');
}
