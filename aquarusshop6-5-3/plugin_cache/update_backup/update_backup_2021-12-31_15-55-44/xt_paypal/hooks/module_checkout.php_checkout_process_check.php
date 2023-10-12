<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1 && $_SESSION['paypalExpressCheckout'] == true)
{
    global $filter;
    if(is_data($_POST['comments']))
    {
        $_SESSION['order_comments']=$_POST['comments'];
    }

    if ($_POST['paypal_create_account'] == 'on')
        $_SESSION['paypal_create_account'] = true;
    else unset($_SESSION['paypal_create_account']);

// only for checked attribute in conditions checkbox
    if ($_POST['conditions_accepted'] == 'on') {
        $_SESSION['XT_PPEXPRESS__conditions_accepted'] = 1;
    }
// redirect on any error. that's why this hook-code hang in last order 99.
    if ($_check_error === true) {
        $tmp_link = $xtLink->_link(array('page' => 'checkout', 'params' => $xtLink->_getParams() . '&' . session_name() . '=' . session_id(), 'conn' => 'SSL'));
        $xtLink->_redirect($tmp_link);
    }
}

