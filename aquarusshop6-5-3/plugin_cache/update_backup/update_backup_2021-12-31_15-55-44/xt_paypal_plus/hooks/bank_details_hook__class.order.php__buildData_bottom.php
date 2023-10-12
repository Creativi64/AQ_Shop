<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$instruction = unserialize($bank_details[COL_BAD_ADD_DATA]);

// we are in a foreach, however we set the due date every time. no problem, i guess
$order_data['order_data']['invoice_due_date'] = $instruction->payment_due_date;