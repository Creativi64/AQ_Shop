<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin;

if(USER_POSITION == 'store' &&
    $key == 'xt_skrill' &&
    isset($xtPlugin->active_modules['xt_skrill']))
{
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/class.xt_skrill.php';

    if (xt_skrill::skrillActivated())
    {
        $arr_all = xt_skrill::$payments;
        $arr_selected = array_unique(array_filter(explode(',', XT_SKRILL_ACTIVATED_PAYMENTS)));
        $arr_unselected = array_diff(array_keys($arr_all), $arr_selected);
        $available_sub_payments = array();
        $payment_country_code = strtoupper($_SESSION['customer']->customer_payment_address['customers_country_code']);

        foreach ($arr_selected as $v)
        {
            define('XT_SKRILL_ACTIVATED_PAYMENTS_' . strtoupper($v), true);
            if (key_exists('allowed_countries',$arr_all[$v])) {
                if (!in_array($payment_country_code,$arr_all[$v]['allowed_countries'])) {
                    continue;
                }
            }
            $available_sub_payments[$v] = $arr_all[$v];
        }
        foreach ($arr_unselected as $v)
        {
            define('XT_SKRILL_ACTIVATED_PAYMENTS_' . strtoupper($v), false);
        }

        $tpl_data['sub_payments'] = $available_sub_payments;
    }
}