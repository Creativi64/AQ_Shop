<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$price_no_discount = $product_data['products_price']['plain'];
if($price_no_discount < $product_data['products_price']['old_plain'])
{
    $price_no_discount = $product_data['products_price']['old_plain'];
}
$this->cart_total_full_coupons += $price_no_discount*$value['products_quantity'];
