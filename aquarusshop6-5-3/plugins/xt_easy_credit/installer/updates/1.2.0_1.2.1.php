<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_EASY_CREDIT_FINANCING', DB_PREFIX.'_easy_credit_financing');

global $db;

$payment_id = $db->GetOne("SELECT payment_id FROM ".TABLE_PAYMENT." WHERE payment_code = 'xt_easy_credit'");

if(!empty($payment_id))
{
    $db->Execute("DELETE FROM ".TABLE_PAYMENT_COST." WHERE payment_id=?", [$payment_id]);

    $db->Execute("UPDATE " . TABLE_PAYMENT_DESCRIPTION . " SET payment_name = 'easyCredit-Ratenkauf' WHERE payment_id = ?", [$payment_id]);
}
