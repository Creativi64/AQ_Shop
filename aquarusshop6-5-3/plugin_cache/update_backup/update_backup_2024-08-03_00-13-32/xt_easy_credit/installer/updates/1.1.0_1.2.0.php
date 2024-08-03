<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

define('TABLE_EASY_CREDIT_FINANCING', DB_PREFIX.'_easy_credit_financing');

global $db;

$payment_id = $db->GetOne("SELECT payment_id FROM ".TABLE_PAYMENT." WHERE payment_code = 'xt_easy_credit'");

if(!empty($payment_id))
{
    $db->Execute("DELETE FROM ".TABLE_PAYMENT_COST." WHERE payment_id=?", [$payment_id]);

    $db->Execute("INSERT INTO " . TABLE_PAYMENT_COST . " (`payment_id`, `payment_geo_zone`, `payment_country_code`, `payment_type_value_from`, `payment_type_value_to`, `payment_price`,`payment_allowed`) VALUES(" . $payment_id . ", 0, 'DE', 200, 10000.00, 0, 1);");
}
