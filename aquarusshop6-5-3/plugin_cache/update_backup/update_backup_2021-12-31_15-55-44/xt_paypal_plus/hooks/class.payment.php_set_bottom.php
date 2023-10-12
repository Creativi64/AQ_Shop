<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($data['payment_code'] == 'xt_paypal_plus')
{
	global $db, $store_handler;

	$msgs = [];
	$processed_client_ids = [];

    $stores = $db->GetArray('SELECT * FROM '.TABLE_MANDANT_CONFIG.' order by shop_status desc, shop_id;');
	foreach ($stores as $store)
	{
		$storeId = $store['shop_id'];
		if(isset($data['conf_XT_PAYPAL_PLUS_ENABLED_shop_'. $storeId]))
		{
		    if ((!empty($data['conf_XT_PAYPAL_PLUS_SANDBOX_CLIENT_ID_shop_'.$storeId]) && !empty($data['conf_XT_PAYPAL_PLUS_SANDBOX_SECRET_KEY_shop_'.$storeId])) ||
				(!empty($data['conf_XT_PAYPAL_PLUS_CLIENT_ID_shop_' . $storeId]) && !empty($data['conf_XT_PAYPAL_PLUS_SECRET_KEY_shop_' . $storeId]))
			)
            {
                $live_mode = $data['conf_XT_PAYPAL_PLUS_MODE_shop_'.$storeId] == 'live';
                if($live_mode)
                {
                    $secretKey = $data['conf_XT_PAYPAL_PLUS_SECRET_KEY_shop_'.$storeId];
                    $clientId = $data['conf_XT_PAYPAL_PLUS_CLIENT_ID_shop_'.$storeId];
                    $dbKey = 'XT_PAYPAL_PLUS_PPP_WEBHOOKID';
                }
                else
                {
                    $secretKey = $data['conf_XT_PAYPAL_PLUS_SANDBOX_SECRET_KEY_shop_'.$storeId];
                    $clientId = $data['conf_XT_PAYPAL_PLUS_SANDBOX_CLIENT_ID_shop_'.$storeId];
                    $dbKey = 'XT_PAYPAL_PLUS_PPP_WEBHOOKID_SANDBOX';
                }

                if(!array_key_exists($clientId, $processed_client_ids))
                {

                    include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
                    $ppp = new paypal_plus($data['conf_XT_PAYPAL_PLUS_MODE_shop_'.$storeId]);

                    $isSecureUrl = $db->GetOne('SELECT shop_ssl FROM '.TABLE_MANDANT_CONFIG. " WHERE shop_id=?", array($storeId));
                    if($isSecureUrl == 'no_ssl' || $isSecureUrl == '0')
                    {
                        $ppp->dataLog("Shop $storeId: " .'IPN-Url wurde nicht eingerichtet, da für diesen Shop SSL nicht aktiviert ist. Client-ID: '.$clientId);
                        $msgs[] = "Shop $storeId: " .'IPN-Url wurde nicht eingerichtet, da für diesen Shop SSL nicht aktiviert ist.';
                        continue;
                    }

                    $ppp->Secret_key = $secretKey;
                    $ppp->Client_ID = $clientId;

                    $result = $ppp->setUpWebhooks($storeId);

                    if($result->id)
                    {
                        $db->Execute('UPDATE '. TABLE_CONFIGURATION_PAYMENT." SET config_value='' WHERE config_key LIKE 'XT_PAYPAL_PLUS_PPP_WEBHOOKID%' and shop_id=?",array($storeId));
                        $db->Execute('UPDATE '. TABLE_CONFIGURATION_PAYMENT." SET config_value=? WHERE config_key=? and shop_id=?",array($result->id, $dbKey, $storeId));
                        $msgs[] = "Shop $storeId: " .'IPN-Url eingerichtet:<br>'.$result->url;
                        $processed_client_ids[$clientId]['result'] = $result;
                    }
                    else if ($result->name){
                        $ppp->dataLog("Shop $storeId: IPN-Url NICHT eingerichtet. Paypal Fehler: " .$result->name. ' Client-ID: '.$clientId);
                        $msgs[] = "Shop $storeId: IPN-Url NICHT eingerichtet. Paypal Fehler: " .$result->name;
                    }
                    else {
                        $ppp->dataLog("Shop $storeId: IPN-Url NICHT eingerichtet, unbekannter Fehler. Client-ID: '.$clientId");
                        $msgs[] = "Shop $storeId: IPN-Url NICHT eingerichtet, unbekannter Fehler";
                    }
                }
                else {
                    $db->Execute('UPDATE '. TABLE_CONFIGURATION_PAYMENT." SET config_value='' WHERE config_key LIKE 'XT_PAYPAL_PLUS_PPP_WEBHOOKID%' and shop_id=?",array($storeId));
                    $db->Execute('UPDATE '. TABLE_CONFIGURATION_PAYMENT." SET config_value=? WHERE config_key=? and shop_id=?",array($processed_client_ids[$clientId]['result']->id, $dbKey, $storeId));
                    $msgs[] = "Shop $storeId: " .'IPN-Url verwendet:<br>'.$processed_client_ids[$clientId]['result']->url;
                }
			}
		}
	}

    if(isset($objP) && is_object($objP)) $objP->success = true; // payment::_set
    $obj = new stdClass();
    $obj->failed = false;
    $obj->error_message = implode('<br/>&nbsp;<br/>', $msgs);
    return $obj;
}

