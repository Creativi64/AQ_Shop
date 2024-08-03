<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . 'plugins/xt_easy_credit/classes/class.xt_easy_credit.php';

define('TABLE_EASY_CREDIT_FINANCING', DB_PREFIX.'_easy_credit_financing');

if (USER_POSITION=='store')
{
    define('PAGE_XT_EASY_CREDIT', _SRV_WEB_PLUGINS.'xt_easy_credit/pages/xt_easy_credit.php');

    global $db;
    $sql = "SELECT payment_id FROM " . TABLE_PAYMENT . " WHERE payment_code='xt_easy_credit'";
    $payId = $db->GetOne($sql);
    define('EASY_CREDIT_PAYMENT_ID', $payId ? $payId : false);
}