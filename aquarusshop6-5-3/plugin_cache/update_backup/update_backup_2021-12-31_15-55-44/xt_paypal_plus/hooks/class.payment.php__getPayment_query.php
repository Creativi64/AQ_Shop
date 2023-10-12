<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $testingIsPPPAvailable;
if($testingIsPPPAvailable===true)
{
    if(empty($this->sql_payment->a_sql_where))
    $this->sql_payment->setSQL_WHERE(" p.payment_code='xt_paypal_plus' AND ");
    else
        $this->sql_payment->setSQL_WHERE(" AND p.payment_code='xt_paypal_plus' ");
}