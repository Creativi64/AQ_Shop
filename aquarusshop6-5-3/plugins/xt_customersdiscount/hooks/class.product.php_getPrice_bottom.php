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

if(!isset($_POST['action']) || ($_POST['action']!='update_product' && $_POST['action']!='add_product' ))
{
    include_once 'ignored_pages.inc.php';

    global $customers_status;

    $customer_group_discount = 0;

    $total_cart = 0;
    if ($_SESSION['cart'])
    {
        $total_cart = $_SESSION['cart']->cart_total_full_for_customers_discount; //$_SESSION['cart']->cart_total_full;
        //$total_cart = $_SESSION['cart']->content_total['plain'];
        $cart_discount_already = 0;
        if (is_array($_SESSION['cart']->discount))
        {
            $cart_discount_already = $_SESSION['cart']->discount['plain'];
        }
        $total_cart = $total_cart + $cart_discount_already;
    }

    if (false && XtCustomersDiscount_orderEditActive())
    {
        if ($cheapest_price)
        {
            $old_price_plain_otax = $price->_removeTax($price_plain, $products_tax);
        }
        else
        {
            if ($special_price)
            {
                $old_price_plain_otax = $price->_removeTax($special_price, $products_tax);
                $price_plain = $special_price;
            }
            else
            {
                $old_price_plain_otax = $this->data['products_price'];
                $price_plain = $price->_AddTax($old_price_plain_otax, $products_tax);
            }
        }

        $format_array = array(
            'price' => $price_data['plain'],
            'price_otax' => $price_data['plain_otax'],
            'old_price' => $products_price,
            'old_price_otax' => $products_price_otax,
            'format' => true,
            'format_type' => $customer_group_discount > 0 ? 'special' : 'special',
            'date_available' => $date_available,
            'date_expired' => $date_expired
        );

        //error_log(print_r($format_array, true));
        $price_data = $price->_Format($format_array);
    }
}
