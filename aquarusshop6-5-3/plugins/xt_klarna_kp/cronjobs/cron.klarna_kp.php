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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/class.klarna_xt_webservice.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

if(!class_exists('cron_klarna_kp'))
{

    class cron_klarna_kp
    {
    	
        public function _run($params)
        {
            global $xtPlugin, $db, $logHandler;

            $xtWebService = new klarna_xt_webservice();
            $xtWebService->_initiate();
            
            $record = $db->Execute("SELECT config_key, config_value FROM " .TABLE_CONFIGURATION_MULTI . '1');
            $config = array();
            while (!$record->EOF) {
            	$config[strtoupper($record->fields['config_key'])] = $record->fields['config_value'];
            	$record->MoveNext();
            }
            $record->Close();
            
            
            $r = array('msg' => 'Plugin xt_klarna_kp not active');

            if (isset($xtPlugin->active_modules['xt_klarna_kp']))
            {

                if ($config['_KLARNA_CONFIG_ACCOUNT_STATUS']!='DEMO') {
                    $merchant_id = explode('_',$config['_KLARNA_CONFIG_KP_MID']);
                    $shedule = $xtWebService->_syncMerchant($merchant_id[0],1);
                }

				
            } else {
            	$r = array('msg' => 'Plugin xt_klarna_kp not active');
            	$logHandler->_addLog('error', 'klarna_kp:cron', 0, json_encode($r));
            }

            return true;
        }

    }
}
