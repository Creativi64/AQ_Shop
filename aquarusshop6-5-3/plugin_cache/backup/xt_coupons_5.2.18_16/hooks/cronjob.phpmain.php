<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (isset($_GET['api'])) {
    if ($_GET['api'] == 'coupon_import') {
        include 'plugins/xt_coupons/classes/class.csvapi_coupons.php';
        include 'plugins/xt_coupons/classes/class.xt_coupons_token_im_export.php';
        $csv_export = new xt_coupons_token_im_export();
        $csv_export->run_import($_GET);
    }
    if ($_GET['api'] == 'coupon_export') {
        include 'plugins/xt_coupons/classes/class.csvapi_coupons.php';
        include 'plugins/xt_coupons/classes/class.xt_coupons_token_im_export.php';
        $csv_export = new xt_coupons_token_im_export();
        $csv_export->run_export($_GET);
    }
    if ($_GET['api'] == 'coupon_generator') {
        include 'plugins/xt_coupons/classes/class.xt_coupons_token_generator.php';
        $csv_export = new xt_coupons_token_generator();
        $csv_export->run_generator($_GET);
    }
}
