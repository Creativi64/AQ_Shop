<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get'] == 'coupon') {
    require_once _SRV_WEBROOT . 'plugins/xt_coupons/classes/class.xt_coupons.php';
    $_coupons = new xt_coupons();
    $result = $_coupons->getCoupons();
    unset($_coupons);
}

if ($request['get'] == 'coupon_type') {
    $result = array();
    $result[] = array('id' => 'fix', 'name' => TEXT_COUPON_TYPE_FIX);
    $result[] = array('id' => 'percent', 'name' => TEXT_COUPON_TYPE_PERCENT);
    $result[] = array('id' => 'freeshipping', 'name' => TEXT_COUPON_TYPE_FREESHIPPING);
}

if($request['get'] == 'coupon_customers_status')
{
    global $customers_status;
    $result[] = array('id' => '',
        'name' => TEXT_EMPTY_SELECTION);
    $result = array();

    $list = $customers_status->_getStatusList('admin');

    foreach ($list as $ldata)
    {
        $result[] = array('id' => $ldata['id'],
            'name' => $ldata['text']);
    }
}