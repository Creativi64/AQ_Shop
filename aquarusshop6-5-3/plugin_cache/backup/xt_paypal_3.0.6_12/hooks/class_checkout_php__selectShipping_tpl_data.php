<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true) {
    global $template;

        $tpl = 'shipping_paypal.html';

    $template->getTemplatePath($tpl, 'xt_paypal', '', 'plugin');
}
?>