<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(preg_match('/orders_[\d]+/',$key ))
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.tracking.php';

    $shipping_data = tracking::getTrackingForOrder($array[0]["oID"],array(), true, false);
    $value['xt_ship_and_track'] = $shipping_data;
    $a = 0;
}
else if($key === 'xt_ship_and_track')
{
    $subnode->addAttribute('name','Sendungsverfolgung / Tracking');
}
