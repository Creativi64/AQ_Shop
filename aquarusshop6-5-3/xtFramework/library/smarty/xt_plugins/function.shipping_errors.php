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

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/smarty/xt_plugins/function.shipping_error.php';

function smarty_function_shipping_errors($params, & $smarty)
{
    $errors = $params['errors'] ?: [];

    $msgs = '';

    $found_error_price = $found_error_weight = false;
    foreach ($errors as $k => $v)
    {
        if(
            ($params['unique_only'] && $v["error"] == 'price' && $found_error_price)
            ||
            ($params['unique_only'] && $v["error"] == 'weight' && $found_error_weight)
        )
        {
            continue;
        }


        $msgs  .= smarty_function_shipping_error(['error' => $v], $smarty, true);

        if($v["error"] == 'price') $found_error_price = true;
        if($v["error"] == 'weight') $found_error_weight = true;
    }

    echo $msgs;
}

