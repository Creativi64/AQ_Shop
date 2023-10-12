<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$allowed_functions =         ["getBox", ];
$login_required_functions =  [];

$ret = new stdClass();
$ret->success = false;
$ret->message = null;
$response_code = 200;

$fnc = isset($_REQUEST['fnc']) ? $_REQUEST['fnc'] : false;
if($fnc && !empty($fnc) && in_array($fnc, $allowed_functions))
{
    global $db, $xtPlugin;
    try
    {
        switch ($fnc)
        {
            case "getBox":
                include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK."/library/smarty/xt_plugins/function.box.php";
                define('_CUST_STATUS_SHOW_PRICE', $customers_status->customers_status_show_price);
                $ret->html = smarty_function_box(['name' => 'cart', 'return' => true], new Smarty());;
                break;
            default:
                $response_code = 400;
                $ret->message = "function [" . $fnc . "] not implemented";
        }
    }
    catch (Exception $e)
    {
        $response_code = 500;
        $ret->message = "Exception occurred";
        error_log(__FILE__ . ': ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
    }

    $ret->success = true;
}
else {

    $response_code = 403;
    $ret->message = "function [".$fnc."] not allowed";
}

http_response_code($response_code);
header('Content-Type: application/json');
echo json_encode($ret);
die();
