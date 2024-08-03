<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $header array */
/** @var $this payment */

if($this->url_data['edit_id'] && $this->url_data['new'] != true)
{
    include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_skrill/classes/class.xt_skrill.php';

    global $db;
    $skrillId = xt_skrill::getSkrillPaymentId();
    if($skrillId == $this->url_data['edit_id'])
    {
        foreach($header as $k => $v)
        {
            if(strpos($k, 'conf_XT_SKRILL_ACTIVATED_PAYMENTS_shop_',0) === 0)
            {
                preg_match("/[0-9]+$/", $k, $out);
                $header[$k]['valueUrl'] = 'adminHandler.php?plugin=xt_skrill&load_section=xt_skrill&pg=saved_ACTIVATED_PAYMENTS&shop_id='.$out[0];
            }
        }
    }
}
