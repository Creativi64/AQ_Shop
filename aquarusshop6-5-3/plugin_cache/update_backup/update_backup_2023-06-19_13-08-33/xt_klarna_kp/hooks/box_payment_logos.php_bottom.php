<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

foreach($payment_logos as $k => $pl)
{
    if($pl["payment_code"] == "xt_klarna_kp")
    {
        $payment_logos[$k] = null;
        define('KLARNA_LOGO_PRO', true);
    }
}
$tpl_data = array('_payment_logos'=> array_filter($payment_logos));
