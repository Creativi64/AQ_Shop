<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


$pObj = $this->_get($this->payment_id);
$is_ppp = false;
if ($pObj) {
    if ($pObj->data[0]['payment_code'] == 'xt_paypal_plus') {
        $is_ppp = true;
    }
}
