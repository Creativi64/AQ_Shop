<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(array_key_exists('conf_XT_EASY_CREDIT_RESTBETRAG_shop_1', $header))
{
    require_once _SRV_WEBROOT . 'plugins/xt_easy_credit/classes/class.xt_easy_credit.php';
    global $store_handler, $price;

    $ec = new easy_credit();
    $restbetrag = $ec->getRestbetrag();
    if($restbetrag)
        $restbetrag = $price->_StyleFormat($restbetrag);
    else
        $restbetrag = 'n/a';

    foreach ($store_handler->getStores() as $store)
    {
        $header['conf_XT_EASY_CREDIT_RESTBETRAG_shop_'.$store['id']]['required'] = false;
        $header['conf_XT_EASY_CREDIT_RESTBETRAG_shop_'.$store['id']]['readonly'] = true;
        $header['conf_XT_EASY_CREDIT_RESTBETRAG_shop_'.$store['id']]['value'] = $restbetrag;
    }
}