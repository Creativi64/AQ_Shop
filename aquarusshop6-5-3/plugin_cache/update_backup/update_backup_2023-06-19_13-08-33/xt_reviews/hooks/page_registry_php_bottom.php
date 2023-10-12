<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_PRODUCTS_REVIEWS', DB_PREFIX.'_products_reviews');
define('TABLE_PRODUCTS_REVIEWS_PERMISSION', DB_PREFIX.'_products_reviews_permission');
define('PAGE_REVIEWS', _SRV_WEB_PLUGINS.'xt_reviews/pages/reviews.php');
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS.'xt_reviews/classes/class.xt_reviews.php';
