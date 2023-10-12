<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (!empty($_SESSION['coupon_info'])) {
    $info->_addInfo($_SESSION['coupon_info'], $_SESSION['coupon_info_type']);
    unset($_SESSION['coupon_info']);
    unset($_SESSION['coupon_info_type']);
}
///
if (round($_SESSION['cart']->total['plain'],2) < 0 && $_SESSION['negtotalreload'] != 'true') {
    $_SESSION['negtotalreload'] = 'true';
//	$_SESSION['selected_payment_discount'][$_SESSION['selected_payment']] = 0;
//	global $xtLink;
    $info->_addInfoSession(TEXT_COUPON_INVALID_TOTAL, 'warning');
    $xtLink->_redirect($xtLink->_link(array('page' => 'checkout', 'paction' => 'shipping', 'conn' => 'SSL')));
} else {
    $_SESSION['negtotalreload'] = 'false';
}
