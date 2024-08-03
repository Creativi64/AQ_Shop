<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Provide drop-down lists for configuration.
 */
if ($request['get'] == 'klarna_kp_logo_design') {
    $result = array();
    $result =
        array(
            array('id' => 'logo-white', 'name' => 'logo-white'),
            array('id' => 'logo-black', 'name' => 'logo-black'),
            array('id' => 'badge-short-white', 'name' => 'badge-short-white'),
            array('id' => 'badge-short-blue', 'name' => 'badge-short-blue'),
            array('id' => 'badge-long-white', 'name' => 'badge-long-white'),
            array('id' => 'badge-long-blue', 'name' => 'badge-long-blue'),
    );
} else if ($request['get'] == 'klarna_widget_design') {
    $result = array();
    $result[] = array(
        'id' => 'not implemented',
        'name' => 'None'
    );
}
else if ($request['get'] == 'klarna_kp_b2b_groups') {
    $result = array();
    global $customers_status;

    $list = $customers_status->_getStatusList('admin');
    foreach ($list as $ldata) {
        $result[] =  array('id' => $ldata['id'],
            'name' => $ldata['text']);
    }
}

else if ($request['get'] == 'klarna_kp_payment_methods') {
    $result = array();

    $list = ['pay_now', 'pay_later', 'pay_over_time'];
    foreach ($list as $pm) {
        $result[] =  array('id' => $pm, 'name' => __define('TEXT_KLARNA_KP_'.$pm), 'desc' => '', __define('TEXT_KLARNA_KP_'.$pm.'_DESC'));
    }
}
else if ($request['get'] == 'klarna_kp_order_status') {
    $result = array();

    require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
    $s_status = new system_status();

    $_data = $s_status->values['order_status'];

    foreach ($_data as $zdata) {
        $result[] =  array('id' => $zdata['id'],
            'name' => $zdata['name']);
    }
}