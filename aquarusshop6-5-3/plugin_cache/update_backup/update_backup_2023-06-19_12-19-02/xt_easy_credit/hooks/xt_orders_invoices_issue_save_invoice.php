<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($orderData['order_data']['payment_code'] == 'xt_easy_credit')
{
    unset($item['invoice_due_date']);
}
