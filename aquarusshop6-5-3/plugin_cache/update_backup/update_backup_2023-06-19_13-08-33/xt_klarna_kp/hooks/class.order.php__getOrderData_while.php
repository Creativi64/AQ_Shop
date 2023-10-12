<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($record->fields['payment_code'] == 'xt_klarna_kp')
{
    $record->fields['payment_name'] = 'Klarna';
}
