<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'plugins/xt_2fa/classes/class.xt_2fa.php';

$ga = new xt_2fa();

$result = $ga->validation($rs->fields['user_id'],$filter->_filter($data['plg_twofa_code']));
if (!$result) {
    unset($_SESSION['admin_user']);
    $this->_errorMsg = sprintf(ERROR_2FA_LOGIN_FAILED);
    $plugin_return_value = false;
    return false;
}