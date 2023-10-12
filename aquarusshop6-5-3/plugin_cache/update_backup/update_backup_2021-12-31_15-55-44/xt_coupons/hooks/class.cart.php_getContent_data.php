<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$skip_coupon_product_discount_calc = false;

($plugin_code = $xtPlugin->PluginCode('coupons_hook_cart_getContent_data:top')) ? eval($plugin_code) : false;

if (isset($_SESSION['sess_coupon']['coupon_code']) && $skip_coupon_product_discount_calc === false){
	$coupons = new xt_coupons();

	$regular_price = !empty($value) ? $value['products_price']['plain_otax'] : 0;
	$coupon_product_discount = $coupons->coupon_product_percent($value, 0, '', !empty($cart_products) ? $cart_products :false);

	$discount += $coupon_product_discount;
    if($discount>100){
        $discount = 100;
    }

	$this->coupon_product_discount[$value['products_id']] = $coupon_product_discount /* $value['products_quantity'] */;
}
