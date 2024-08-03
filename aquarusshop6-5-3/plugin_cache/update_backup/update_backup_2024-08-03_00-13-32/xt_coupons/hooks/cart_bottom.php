<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (XT_COUPONS_CART_PAGE == 1) {
    $arr_coupon = $_SESSION['sess_coupon'];

    if (!is_array($arr_coupon)) {

        $show_field = false;

        if (XT_COUPONS_LOGIN != 1) {
            $show_field = true;
        } elseif ($_SESSION['registered_customer']) {
            $show_field = true;
        }

        if ($show_field == true) {
            $tpl = 'coupons_cart.html';

            $plugin_template = new Template();
            $plugin_template->getTemplatePath($tpl, 'xt_coupons', '', 'plugin');
            echo ($plugin_template->getTemplate('', $tpl, $tpl_data));
        }
    }
}
