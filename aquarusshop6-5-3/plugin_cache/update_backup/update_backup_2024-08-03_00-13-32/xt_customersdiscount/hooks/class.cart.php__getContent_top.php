<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once 'ignored_pages.inc.php';

global $price, $customers_status;

if (!isset($_POST['action']) || ($_POST['action']!='update_product' && $customers_status->customers_status_discount_flag=='1')
    && !empty($customers_status->customers_discount)
) {
    $customer_group_discount = 0;

    $total_cart = $this->cart_total_full_for_customers_discount; //$this->content_total['plain'];
    $cart_discount_already = 0;
    if(is_array($_SESSION['cart']->discount))
    {
        $cart_discount_already = $this->discount['plain'];
    }
    $total_cart = $total_cart + $cart_discount_already;

    if (strstr($customers_status->customers_discount,'#')) {
        $discounts = explode(';',$customers_status->customers_discount);

        $_SESSION['customer_group_discount'] = 0;
        $discounts = array_reverse(array_filter($discounts));
        for ($i=0;$i<sizeof($discounts);$i++)
        {
            $dsc = explode('#', $discounts[$i]);
            if (round($total_cart, 2) >= round($price->_calcCurrency($dsc[0]), 2))
            {
                $customer_group_discount = $dsc[1];
                $_SESSION['customer_group_discount'] = $dsc[1];
                break;
            }
        }
    } else { // only single discount
        if (is_numeric($customers_status->customers_discount) && $customers_status->customers_discount > 0)
        {
            $customer_group_discount = $_SESSION['customer_group_discount'] = $customers_status->customers_discount;
        }
        else $customer_group_discount = $_SESSION['customer_group_discount'] = 0;
    }
}
