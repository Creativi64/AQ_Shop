<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($data['payment_code'] == 'xt_paypal_plus')
{
    global $store_handler;
    $stores = $store_handler->getStores();
    foreach ($stores as $store)
    {
        $hook_id = $db->GetOne('SELECT config_value FROM ' . TABLE_CONFIGURATION_PAYMENT . " WHERE config_key='XT_PAYPAL_PLUS_PPP_WEBHOOKID' and shop_id=?", array($store['id']));
        $data['conf_XT_PAYPAL_PLUS_PPP_WEBHOOKID_shop_' . $store['id']] = $hook_id;
        $hook_id = $db->GetOne('SELECT config_value FROM ' . TABLE_CONFIGURATION_PAYMENT . " WHERE config_key='XT_PAYPAL_PLUS_PPP_WEBHOOKID_SANDBOX' and shop_id=?", array($store['id']));
        $data['conf_XT_PAYPAL_PLUS_PPP_WEBHOOKID_SANDBOX_shop_' . $store['id']] = $hook_id;
    }

    if( isset($xtPlugin->active_modules['xt_cleancache']))
    {
        require_once _SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php';
        $cc = new cleancache();

        $r = $cc->cleanTemplateCached('cache_css');
    }
}