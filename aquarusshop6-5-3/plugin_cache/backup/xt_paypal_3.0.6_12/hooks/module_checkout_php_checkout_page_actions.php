<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true && $page->page_action != 'success' && $page->page_action != 'process') {

    include _SRV_WEBROOT . 'xtCore/pages/page_action/checkout.payment.php';

    if ($_SESSION['conditions_accepted_paypal'] == 'true')
        $_SESSION['conditions_accepted'] = 'true';

    if ($_SESSION['rescission_accepted_paypal'] == 'true')
        $_SESSION['rescission_accepted'] = 'true';

    include _SRV_WEBROOT . 'xtCore/pages/page_action/checkout.confirmation.php';

}
?>