<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $store_handler;

$update_dropdowns = [
    'MODE', // alt MODE wird bei update durch MODE_SANDBOX ersetzt
    'EXPRESS',
    'PAYMENT_TYPE_ORDER'
];

foreach ($xml_data["xtcommerceplugin"]["configuration_payment"]["config"] as $config)
{
    $config_key = strtoupper($modul.'_'.$config['key']);

    foreach ($store_handler->getStores() as $store)
    {
        $db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET sort_order=? WHERE config_key=?",[$config['sort_order'], $config_key]);

        if(in_array($config['key'], $update_dropdowns))
        {
            $current_val = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_PAYMENT." WHERE  config_key=? AND shop_id=?",[$config_key, $store['id']]);
            $current_url = $db->GetOne("SELECT url          FROM ".TABLE_CONFIGURATION_PAYMENT." WHERE  config_key=? AND shop_id=?",[$config_key, $store['id']]);
            $new_val = null;
            switch($current_url)
            {
                case 'conf_truefalse':
                    $new_val = $current_val == 'true' || $current_val == 1 ? 1:0;
                    break;
                case 'status_sandbox':
                    $new_val = $current_val == 'sandbox' || $current_val == 1 ? 1:0;
                    break;
            }
            if(!is_null($new_val) && $current_val !== false)
            {
                $db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET `type`='status', url='', config_value=? WHERE config_key=? AND shop_id=?",[$new_val, $config_key, $store['id']]);
            }
        }
    }

}

$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT." SET sort_order=59 WHERE config_key='XT_PAYPAL_ORDER_STATUS_NEW'");
