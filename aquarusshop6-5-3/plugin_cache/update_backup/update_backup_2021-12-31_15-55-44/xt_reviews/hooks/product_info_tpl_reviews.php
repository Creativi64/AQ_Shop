<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (XT_REVIEWS_ENABLED)
{
	global $current_product_id, $slave_product_id;

	$xt_reviews = new xt_reviews();
    echo $xt_reviews->displayProduct(isset($slave_product_id) ? $slave_product_id : $current_product_id);
}