<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtLink, $db;

if ($_SESSION['easy_creditInstallmentsCheckout'] == true && !$_check_error )
{
    require_once _SRV_WEBROOT.'plugins/xt_easy_credit/classes/class.easy_credit.php';
    $ec = new easy_credit();
    $res = $ec->confirm();
    if($res['confirmed'] != true)
    {
        global $info;
        $info->_addInfoSession($res['msg']);
        $xtLink->_redirect($res['url']);
    }
    else {
        $rehash = serialize($_SESSION['easy_credit']);
        $db->Execute("INSERT INTO ".TABLE_EASY_CREDIT_FINANCING." VALUES(?,?) ON DUPLICATE KEY UPDATE rehash=?", array($_SESSION['last_order_id'], $rehash, $rehash));
        unset($_SESSION['easy_credit']);
    }
}
