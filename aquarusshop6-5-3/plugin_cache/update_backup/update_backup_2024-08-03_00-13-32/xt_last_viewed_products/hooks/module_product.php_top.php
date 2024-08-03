<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_last_viewed_products/classes/class.xt_last_viewed_products.php';

global $current_product_id, $last_viewed_products;

$last_viewed_products = new last_viewed_products();
$last_viewed_products->_addLastViewedProduct($current_product_id);
