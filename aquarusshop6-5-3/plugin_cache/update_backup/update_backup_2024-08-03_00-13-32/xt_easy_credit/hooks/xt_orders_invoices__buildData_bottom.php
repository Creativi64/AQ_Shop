<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($orderData['order_data']['payment_code'] == 'xt_easy_credit')
{
    unset($data['invoice']['invoice_due_date']);
    $data['invoice']['invoice_due_date_info'] = TEXT_EASY_CREDIT_SEE_PAYMENTPLAN;
}
