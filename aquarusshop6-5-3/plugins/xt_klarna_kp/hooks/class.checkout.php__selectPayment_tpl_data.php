<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $page, $db, $language, $xtLink, $currency, $tax, $countries, $customers_status, $info;

if($page->page_name == 'checkout' && $page->page_action == 'payment')
{
    if ($key == 'xt_klarna_kp')
    {
        if(array_key_exists('kp_session', $_SESSION))
        {
            $tpl_extra_data = [
                'client_token' => $kp_session["client_token"],
                'payment_method_categories' => $kp_session["payment_method_categories"]
            ];
            $tpl_data = array_merge($tpl_data, $tpl_extra_data);
        }
    }
}