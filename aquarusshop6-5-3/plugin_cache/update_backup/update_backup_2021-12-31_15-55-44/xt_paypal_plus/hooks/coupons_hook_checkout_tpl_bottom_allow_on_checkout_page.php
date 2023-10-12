<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($_SESSION['selected_payment'] == 'xt_paypal_plus')
{
    $allow_on_checkout_page[] = array('plugin' => 'xt_paypal_plus');
}