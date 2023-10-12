<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true) {

    $tmp_link = $xtLink->_link(array('page' => 'checkout', '', 'conn' => 'SSL'));

}
?>