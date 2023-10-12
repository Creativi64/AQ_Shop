<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(/*$_POST['action']!='update_product' &&*/ $format_type=='graduated_discount')
{
    // some of these value are set in hook product _getPrice_afterProductsPrice
    $format_array = array(
        'price' => $special_price,
        'price_otax' => $special_price_otax,
        'old_price' => $products_price > $original_products_price ? $products_price : $original_products_price,
        'old_price_otax' => $original_products_price_otax > $original_products_price_otax ? $original_products_price_otax : $original_products_price_otax,
        'format' => true,
        'format_type' => 'graduated_discount',
        'date_available' => $date_available,
        'date_expired' => $date_expired,
        'cheapest_price_otax' => $cheapest_price_otax,
        'cheapest_price' => $cheapest_price,
    );
}