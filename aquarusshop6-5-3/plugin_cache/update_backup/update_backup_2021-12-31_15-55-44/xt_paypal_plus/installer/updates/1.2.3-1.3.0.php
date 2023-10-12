<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("UPDATE ".TABLE_PLUGIN_CONFIGURATION."  SET config_value=0 WHERE config_key='PAYPAL_PLUS_VERBOSE_IPN_LOGGING'");

global $store_handler;
$stores = $store_handler->getStores();
$errors = array();
foreach ($stores as $store)
{
    $storeId = $store['id'];

    $live_mode = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT.' WHERE config_key=\'XT_PAYPAL_PLUS_MODE\' AND shop_id=?', array($storeId));
    if($live_mode == 'live')
    {
        $secretKey = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT.' WHERE config_key=\'XT_PAYPAL_PLUS_SECRET_KEY\' AND shop_id=?', array($storeId));
        $clientId = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT.' WHERE config_key=\'XT_PAYPAL_PLUS_CLIENT_ID\' AND shop_id=?', array($storeId));
        $dbKey = 'XT_PAYPAL_PLUS_PPP_WEBHOOKID';
    }
    else {
        $live_mode = 'sandbox'; // just in case its not set in db
        $secretKey = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT.' WHERE config_key=\'XT_PAYPAL_PLUS_SANDBOX_SECRET_KEY\' AND shop_id=?', array($storeId));
        $clientId = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT.' WHERE config_key=\'XT_PAYPAL_PLUS_SANDBOX_CLIENT_ID\' AND shop_id=?', array($storeId));
        $dbKey = 'XT_PAYPAL_PLUS_PPP_WEBHOOKID_SANDBOX';
    }

    if (!empty($secretKey) && !empty($clientId))
    {
        include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
        $ppp = new paypal_plus($live_mode);

        $ppp->Secret_key = $secretKey;
        $ppp->Client_ID = $clientId;

        $result = $ppp->setUpWebhooks($storeId, $dbKey);

        if ($result->id)
        {
            // all done in $ppp
        }
        else
        {
            $errors[] = $result->error;
        }
    }
}
if(count($errors))
{
    $error_message = implode('<br/>', $errors);
    $output.= '<div class="error">'.$error_message.'</div>';
}