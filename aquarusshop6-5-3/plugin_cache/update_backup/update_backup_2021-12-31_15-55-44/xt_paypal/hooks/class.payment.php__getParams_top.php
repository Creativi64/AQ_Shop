<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

//require_once _SRV_WEBROOT. _SRV_WEB_PLUGINS. 'xt_klarna/classes/class.xt_klarna.php';

/**
 * Add the test connection buttons.
 */
$pObj = $this->_get($this->payment_id);
$is_ppp = false;
if ($pObj) {
    if ($pObj->data[0]['payment_code'] == 'xt_paypal') {
        $is_ppp = true;
    }
}
