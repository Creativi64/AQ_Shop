<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
global $xtPlugin;
if(!array_key_exists('xt_klarna_kco', $xtPlugin->active_modules) && $_POST['dsgvo_shipping_optin'] != 'on'){
    $shipping_method = $_SESSION['selected_shipping'];
    $rs = $db->Execute("SELECT * FROM ".TABLE_SHIPPING." WHERE shipping_code=? LIMIT 0,1",array($shipping_method));
    if ($rs->RecordCount()==1 && $rs->fields['dsgvo_shipping_required_status']=='1') {
        $_check_error=true;
        $info->_addInfoSession(ERROR_XT_DSGVO_SHIPPINGCHECKBOX);
    }
}
