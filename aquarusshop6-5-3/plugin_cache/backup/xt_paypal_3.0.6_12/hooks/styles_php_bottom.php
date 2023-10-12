<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (constant('XT_PAYPAL_EXPRESS') == 1) {
    echo '<link rel="stylesheet" type="text/css" href="' . _SYSTEM_BASE_URL . _SRV_WEB . _SRV_WEB_PLUGINS . 'xt_paypal/css/xt_paypal.css' . '" />';
}
