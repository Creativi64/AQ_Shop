<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
// order_info_option data in order.html
if (!empty($order_data) && $order_data['order_data']['dsgvo_shipping_optin']=='1') {
    $value = '<span style="color: green"><i class="far fa-check-circle"></i></span>';
} else {
    $value = '<span style="color: red"><i class="far fa-times-circle"></i></span>';
}
$order_data['order_data']['order_info_options'][] = array('text'=>'<span class="bold" style="font-weight:bold">'.TEXT_DSGVO_SHIPPING_CONSENT.'</span>', 'value'=>$value);
