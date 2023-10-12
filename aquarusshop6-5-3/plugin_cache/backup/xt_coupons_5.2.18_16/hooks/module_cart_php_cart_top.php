<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ((isset($_POST['special'])) && ($_POST['special'] == 'coupon')) {
    if ($_POST['coupon_code'] == '') {
        $_SESSION['coupon_info'] = TEXT_COUPON_MISSING;
        $_SESSION['coupon_info_type'] = 'error';
        $info->_addInfo(TEXT_COUPON_MISSING, 'error');
    } else {
        global $filter;
        $coupon_code = $filter->_filter($_POST['coupon_code']);
        if ($coupon_code != '') {
            $coupon = new xt_coupons();
            $coupon_erg = $coupon->_addToCart($coupon_code);
            if ($coupon_erg == true) {
                $_SESSION['coupon_info'] = TEXT_COUPON_ADDED;
                $_SESSION['coupon_info_type'] = 'success';
                $info->_addInfo(TEXT_COUPON_ADDED, 'success');

            } else {
                $_SESSION['coupon_info'] = $coupon->error_info;
                $_SESSION['coupon_info_type'] = 'error';
                $info->_addInfo($coupon->error_info, 'error');
            }
        }
    }
}
