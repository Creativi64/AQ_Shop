<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1) {
    switch ($data_array['action']) {
        case 'paypalExpressCheckout' :
            require_once _SRV_WEBROOT . 'plugins/xt_paypal/classes/class.paypal.php';
            if(array_key_exists('product', $data_array) && !empty($data_array['product']))
            {
                if(empty($data_array['qty'])){
                    $data_array['qty'] = 1;
                }

                if(function_exists('sessionCart'))
                {
                    $local_cart = sessionCart();
                }
                else
                    $local_cart = $_SESSION['cart'];

                $local_cart->_addCart($data_array);
                $local_cart->_refresh();
            }

            $paypalExpress = new paypal();
            $paypal_url = $paypalExpress->paypalAuthCall('express');
            if($paypal_url != false)
            {
                $_SESSION['expressButtonCheckout'] = true;
                $xtLink->_redirect($paypalExpress->payPalURL);
            }
            break;
    }
}
