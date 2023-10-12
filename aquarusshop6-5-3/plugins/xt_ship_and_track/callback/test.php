<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(!empty($_REQUEST['test-shipcloud-callback']))
{

    $endpoint = $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_ship_and_track', 'params'=>'target=shipcloud', 'conn' => 'SSL'), 'xtAdmin/');


    $tracking_code = $_REQUEST['test-shipcloud-callback']; //"b8486a29e9f502952a3db0612409483028165180";


    $data = array(
        "id" => "ef9df623-6974-4a4b-9a99-c0ec5b58b136",
        "occured_at" => "2015-02-17T14:20:42+01:00",
        "type" => "example.event",
        "data" => array(
            "id" => $tracking_code,
            "url" => "/v1/shipments/es40a6e7a83ea8253f54eb414606626172b523d8",
            "object_type" => "shipment"
        )
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);


// value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
// CURL_SSLVERSION_TLSv1_2 (6)
//curl_setopt($ch, CURLOPT_SSLVERSION, 6);

//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);

    $result = curl_exec($ch);

    curl_close($ch);
}

