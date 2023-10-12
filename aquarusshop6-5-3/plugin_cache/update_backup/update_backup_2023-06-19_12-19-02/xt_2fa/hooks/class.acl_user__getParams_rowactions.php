<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'plugins/xt_2fa/classes/class.xt_2fa.php';


if (!isset($this->url_data['edit_id'])) {
    $ga_2fa = new xt_2fa();
    $extF = new ExtFunctions();
    $rowActions[] = array('iconCls' => 'twofa_login', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_2FA_LOGIN);
    $rowActionsFunctions['twofa_login'] = $ga_2fa->get2FaHandler();
} else {
    $header['plugin_xt_twofa_status'] = array('type' => 'hidden');
}
