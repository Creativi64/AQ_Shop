<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */


defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/functions.php';

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/class.paypal_checkout.php';

global $db, $store_handler;


/** @var $this plugin */
/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */
ppcInstallPaymentTypes($product_id, $_plugin_code, 'insert', ['googlepay']);


if(!$this->_FieldExists('ppcp_customer_id', TABLE_CUSTOMERS))
{
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS." ADD COLUMN `ppcp_customer_id` VARCHAR(10) NULL DEFAULT NULL;");
    $db->Execute("ALTER TABLE ".TABLE_CUSTOMERS." ADD INDEX `idx_ppcp_customer_id` (`ppcp_customer_id` DESC);");
}

ppcInstallPaymentIcon();

foreach ($store_handler->getStores() as $store)
{
    $sid = $store['id'];

    $activated = (int) $db->GetOne("SELECT config_value FROM ".TABLE_PLUGIN_CONFIGURATION. " WHERE config_key = 'XT_PAYPAL_CHECKOUT_ACTIVATED' and shop_id = ?", [$sid]);
    if($activated)
    {
        try
        {
            $mode = (int) $db->GetOne("SELECT config_value from ".TABLE_PLUGIN_CONFIGURATION. " WHERE config_key = 'XT_PAYPAL_CHECKOUT_TESTMODE' and shop_id = ?", [$sid]);
            if($mode) $environment = 'sandbox';
            else $environment = 'production';
            $ppcp = paypal_checkout::getPaypalCheckout($environment, $sid, true);

            if($environment=='production' )
            {
                $key_client   = 'XT_PAYPAL_CHECKOUT_CLIENT_ID_LIVE';
                $key_secret   = 'XT_PAYPAL_CHECKOUT_CLIENT_SECRET_LIVE';
            }
            else {
                $key_client   = 'XT_PAYPAL_CHECKOUT_CLIENT_ID_TEST';
                $key_secret   = 'XT_PAYPAL_CHECKOUT_CLIENT_SECRET_TEST';
            }

            $client_id =     $db->GetOne("SELECT config_value from ".TABLE_PLUGIN_CONFIGURATION. " WHERE config_key = ? and shop_id = ?", [$key_client, $sid]); //$data['conf_'.$key_client.'_shop_'.$sid];
            $client_secret = $db->GetOne("SELECT config_value from ".TABLE_PLUGIN_CONFIGURATION. " WHERE config_key = ? and shop_id = ?", [$key_secret, $sid]); //$data['conf_'.$key_secret.'_shop_'.$sid];

            $ppcp->setClientId($client_id);
            $ppcp->setClientSecret($client_secret);
            $token = $ppcp->getSetAccessToken($sid, true);
            $ppcp->helper->setAccessToken($token);

            $res = $ppcp->addAllWebhooks($sid);

            $key = 'XT_PAYPAL_CHECKOUT_WEBHOOK_ID_';
            if($ppcp->getEnvironment() == 'production') $key .= 'LIVE';
            else $key .= 'TEST';

            $db->Execute('UPDATE '.TABLE_PLUGIN_CONFIGURATION." SET config_value = ? WHERE config_key = ? and shop_id = ?", [$res['data']['response']['id'], $key, $sid]);
        }
        catch(PPCPException $e)
        {
            $msg = $e->getLogData()["response"]["details"][0]["issue"];
            if(empty($msg))
                $msg = $e->getLogData()["response"]["error_description"]. ': '. $e->getLogData()["response"]["error"];
            $msgs[] = 'shop '.$sid.': ' .$msg;
        }
        catch(Exception $e)
        {
            $msgs[] = 'shop '.$e->getMessage();
        }
    }
}

ppcClearLanguageCache();
