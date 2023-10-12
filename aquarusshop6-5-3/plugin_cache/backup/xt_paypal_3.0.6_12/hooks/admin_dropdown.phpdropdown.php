<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get'] == 'refunds_type') {
    $result = array();
    $result[] = array('id' => 'Full', 'name' => TEXT_REFUNDS_TYPE_FULL);
    $result[] = array('id' => 'Partial', 'name' => TEXT_REFUNDS_TYPE_PARTIAL);
}

if ($request['get'] == 'xt_paypal_ssl_version') {
    $result = array();
    $result[] = array('id' => 'autodetect', 'name' => TEXT_PAYPAL_AUTODETECT);
    $result[] = array('id' => '1', 'name' => 'NSS');
    $result[] = array('id' => CURL_SSLVERSION_TLSv1_2, 'name' => 'TLS');
}
if ($request['get'] == 'xt_paypal_cipher_list') {
    $result = array();
    $result[] = array('id' => 'autodetect', 'name' => TEXT_PAYPAL_AUTODETECT);
    $result[] = array('id' => 'TLSv1', 'name' => 'TLSv1');
}
?>