<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$is_ppp = false;
if ($pObj) {
    if ($pObj->data[0]['payment_code'] == 'xt_paypal_plus') {
        $is_ppp = true;
    }
}

if ($is_ppp == true && $this->url_data['edit_id'] && !$this->url_data['save'])
{
    global $store_handler;
    $stores = $store_handler->getStores();
    foreach ($stores as $store)
    {
        $header['conf_XT_PAYPAL_PLUS_SANDBOX_CLIENT_ID_shop_' . $store['id']]['required'] = false;
        $header['conf_XT_PAYPAL_PLUS_SANDBOX_SECRET_KEY_shop_' . $store['id']]['required'] = false;
        $header['conf_XT_PAYPAL_PLUS_CLIENT_ID_shop_' . $store['id']]['required'] = false;
        $header['conf_XT_PAYPAL_PLUS_SECRET_KEY_shop_' . $store['id']]['required'] = false;
    }
}
