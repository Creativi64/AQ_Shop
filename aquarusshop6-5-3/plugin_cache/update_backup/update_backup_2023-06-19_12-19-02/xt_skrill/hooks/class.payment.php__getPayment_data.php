<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin;

if($data[count($data)-1]['payment_code'] == 'xt_skrill' && USER_POSITION == 'store' &&
    isset($xtPlugin->active_modules['xt_skrill']))
{
    include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/class.xt_skrill.php';

    if (xt_skrill::skrillActivated())
    {
        $arr_selected = array_unique(array_filter(explode(',', XT_SKRILL_ACTIVATED_PAYMENTS)));
        if(count($arr_selected)==0)
        {
            unset( $data[count($data)-1] );
        }
    }
}