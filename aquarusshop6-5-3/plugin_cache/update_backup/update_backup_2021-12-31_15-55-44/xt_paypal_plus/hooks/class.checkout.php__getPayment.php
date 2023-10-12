<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin;

if (!function_exists ( 'ppp_sort_payments' ))
{
    function ppp_sort_payments($p1, $p2)
    {
        if(strpos($p1['payment_code'],'xt_klarna')===0)
        {
            $p1['sort_order'] = PHP_INT_MAX;
        }
        if(strpos($p2['payment_code'],'xt_klarna')===0)
        {
            $p2['sort_order'] = PHP_INT_MAX;
        }
        $so1 = (int)$p1['sort_order'];
        $so2 = (int)$p2['sort_order'];
        return $so1 >= $so2;
    }
}

if (USER_POSITION=='store' && isset($xtPlugin->active_modules['xt_paypal_plus']))
{
    include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
    $ppp = new paypal_plus;
    if ($ppp->isPPPavailable() && isset($_SESSION['plus_' . $ppp->customer]['approval_url']) && $_SESSION['disablePPPdue2Browser'] != true)
    {
        unset ($payment_data['xt_paypal']);
        uasort($payment_data, 'ppp_sort_payments');
    }
}

