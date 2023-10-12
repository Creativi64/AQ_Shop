<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('PAGE_XT_PAYPAL_PLUS', _SRV_WEB_PLUGINS.'xt_paypal_plus/pages/xt_paypal_plus.php');

if (XT_PAYPAL_PLUS_ENABLED && isset($xtPlugin->active_modules['xt_paypal_plus']))
{
    define('TABLE_PAYPAL_PLUS_REFUNDS', DB_PREFIX . '_plg_paypal_plus_refunds');
}
