<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (isset($xtPlugin->active_modules['xt_ship_and_track']) && !empty($orderID))
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.tracking.php';

    $tracking = new tracking();
    $shippings = $tracking->getTrackingForOrder($orderID);

    if (is_array($shippings) && count($shippings)>0)
    {
        foreach ($shippings as $s_id => $s_arr)
        {
            $orders[$orderID]['tracking'][]=array('tracking_id'=>$s_arr['tracking_code'],'shipping_date'=>$s_arr['tracking_added'],'tracking_url'=>$s_arr['shipper_tracking_url'],'shipping_provider'=>$s_arr['shipper_name']);
        }
    }
}
