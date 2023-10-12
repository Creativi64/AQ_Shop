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

function smarty_function_shipping_error($params, & $smarty, $return = false)
{
    global $currency;

    $error = $params['error'] ?: [];

    if(!empty($error))
    {
        $available_html = ['+br', 'br+', 'li', 'p', 'div'];
        if(empty($params['html']) || !in_array($params['html'], $available_html))
        {
            $html = '+br';
        }
        else $html = $params['html'];
        $class = $params['class'];

        $msg = __text('TEXT_CART').' ';
        switch($error['error'])
        {
            case 'weight':
                $msg .= __text('TEXT_PRODUCTS_WEIGHT'). ' ';
                break;
            case 'price':
                $msg .= __text('TEXT_TOTAL'). ' ';
                break;
        }
        $cart = str_replace('.',',',(string)((float)((string)$error['cart'])));
        switch($error['error'])
        {
            case 'weight':
                $msg .= $cart .' kg. ';
                break;
            case 'price':
                $msg .= $cart .' '. $currency->title.'. ';
                break;
        }


        $msg .= ucfirst($error['type']).' ';
        switch($error['error'])
        {
            case 'weight':
                $msg .= __text('TEXT_PRODUCTS_WEIGHT'). ' ';
                break;
            case 'price':
                $msg .= __text('TEXT_TOTAL'). ' ';
                break;
        }

        $limit = str_replace('.',',',(string)((float)((string)$error['limit'])));
        switch($error['error'])
        {
            case 'weight':
                $msg .= $limit .' kg. ';
                break;
            case 'price':
                $msg .= $limit .' '. $currency->title.'.';
                break;
        }

        switch ($html)
        {
            case '+br':
                $msg .= '<br />';
                break;
            case 'br+':
                $msg = '<br />' . $msg;
                break;
            case 'li':
            case 'p':
            case 'div':
                $msg = '<'.$html.'>'. $msg .'</'.$html.'>';
                break;
        }

        if($return) return $msg;

        echo $msg;
    }
}

