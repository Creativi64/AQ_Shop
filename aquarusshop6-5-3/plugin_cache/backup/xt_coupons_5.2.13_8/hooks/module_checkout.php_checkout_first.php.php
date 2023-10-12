<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$coupons = new xt_coupons();
$coupons->coupon_recalc();
