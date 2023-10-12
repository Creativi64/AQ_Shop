<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $customers_status;

if (!empty($customers_status->customers_discount))
{
    $_SESSION['cart']->_refresh();
    $total_cart = $_SESSION['cart']->cart_total_full_for_customers_discount; //$_SESSION['cart']->content_total['plain']; //$_SESSION['cart']->cart_total_full;

    if (strstr($customers_status->customers_discount, '#'))
    {
        $discounts = explode(';', $customers_status->customers_discount);

        $discounts = array_reverse(array_filter($discounts));
        $discount_found = false;
        for ($i = 0; $i < sizeof($discounts); $i++)
        {
            $dsc = explode('#', $discounts[$i]);
            if (round($total_cart, 2) >= round($price->_calcCurrency($dsc[0]), 2))
            {
                $customer_group_discount = $dsc[1];
                /*if($_SESSION['customer_group_discount'] < $dsc[1]) */
                $_SESSION['customer_group_discount'] = $dsc[1];
                $_SESSION['cart']->_refresh();
                $discount_found = true;
                break;
            }
        }
        if (!$discount_found && $_SESSION['customer_group_discount'] > 0)
        {
            $_SESSION['customer_group_discount'] = 0;
            $_SESSION['cart']->_refresh();
        }
        if (!$discount_found || $customer_group_discount == 0)
        {
            $_SESSION['customer_group_discount'] = 0;
        }
    }
    else
    { // only single discount
        if ($customers_status->customers_discount > 0)
        {
            $customer_group_discount = $_SESSION['customer_group_discount'] = $customers_status->customers_discount;
        }
    }
}