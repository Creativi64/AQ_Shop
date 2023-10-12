<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

try
{
    $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($orders_id);
    if(!empty($kp_order_arr['order_id']) && $kp_order_arr['status'] != 'CANCELLED')
    {
        $kp_order_id = $kp_order_arr['order_id'];
        $kp = klarna_kp::getInstance();
        $kp->cancelOrder($kp_order_id);
        $kp_order = $kp->getOrder($kp_order_id);
        xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
    }
}
catch (Exception $e)
{
    $r = new stdClass();
    $r->success = false;
    $r->refresh = false;
    $msg = explode('|', $e->getMessage());
    $msg[0] = $msg[0] . ( !empty($msg[1]) ? '::' . $msg[1] : '');
    unset($msg[1]);
    $r->msg = implode("<br/>", $msg);

    $plugin_return_value = TEXT_KLARNA_CANT_DELETE.'<br />'.$r->msg;
}

