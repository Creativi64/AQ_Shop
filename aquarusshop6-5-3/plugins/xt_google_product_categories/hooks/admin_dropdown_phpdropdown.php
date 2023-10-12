<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($request['get']=='google_categories') {
    require_once _SRV_WEBROOT.'plugins/xt_google_product_categories/classes/class.xt_google_product_categories.php';

    $_g_pc = new google_product_categories();
    $result = $_g_pc->getCategories();

    unset($_g_pc);
}
