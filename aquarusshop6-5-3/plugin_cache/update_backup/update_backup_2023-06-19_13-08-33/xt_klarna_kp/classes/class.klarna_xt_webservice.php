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

use GuzzleHttp\Exception\ClientException;
class klarna_xt_webservice {
	

	const XT_WEBSERVICE = 'https://webservices.xt-commerce.com/';
	const XT_SET_ACCOUNT_TO_LIVE=false; 
	const XT_WEBSERVICE_USER = 'klarna';
	const XT_WEBSERVICE_PW = 'klarna';
	const XT_KP_TEST_WEBSERVICE = 'https://api.klarna.com/';
	
	function __construct() {
		
		global $store_handler;
		$lic_info = $store_handler->getLicenseFileInfo(array('key','mailaddress'));
		
		// set lic values for auth 
		$this->lic_key = trim($lic_info['key']);
		$this->lic_email = trim($lic_info['mailaddress']);

	}
	
	public function _initiate() {
				
		$this->client = new GuzzleHttp\Client ( [
				'base_uri' => self::XT_WEBSERVICE,
				'auth' => [
						$this->lic_key,
						$this->lic_email
				],
				'http_errors' => false
		] );
		
	}
	
	
	/**
	 * signup via webservice
	 * @param unknown $data
	 */
	public function signup($data,$type = 'registered') {
		
		// add license key do query
		$data['license_key'] = $this->lic_key;
		
		if ($type == 'registered') {
			$webservice = 'new_merchant_registered';
		} else {
			$webservice = 'new_merchant_unregistered';
		}
		
		try {
		
		$response = $this->client->request ( 'POST', 'signup/v1/klarna/' . $webservice, [
				'json' => $data
		] );

		} catch ( GuzzleHttp\Exception\ClientException $e ) {
				
			if ($e->hasResponse ()) {

				if ($e->hasResponse ()) {
					print_r  ( $e->getResponse () );
				}
		
			}
		}
		
		$tpl_data = array();
		
		// wrong form data
		if ($response->getStatusCode()==400) {
			$response = json_decode ( $response->getBody() );

				if ($response->error == 'INVALID_PARAMETER') {
					$_field_errors = array ();
			
					foreach ( $response as $e_field => $e_msg ) {
						$_field_errors [$e_field] = $e_msg [0];
					}
				}
			
			
			if (is_array ( $_field_errors ))
				$tpl_data ['_errors'] = $_field_errors;
				
			if (is_array ( $_POST ) && count ( $_POST ) > 1)
				$tpl_data = array_merge ( $tpl_data, $_POST );
			
			return $tpl_data;
		}
		
		return $response->getBody();

		
		
	}
	
	/**
	 * try to validate klarna api credentials by calling klarna API
	 * @param unknown $username
	 * @param unknown $password
	 * @return number
	 */
	public function TestCredentials($username,$password) {
		
		$client = new GuzzleHttp\Client ( [
				'base_uri' => self::XT_KP_TEST_WEBSERVICE,
				'auth' => [
						$username,
						$password
				]
		] );
		try {
			$response = $client->request ( 'POST', 'payments/v1/sessions',[
                'json' => array('test'=>'test')
            ]);
		} catch ( GuzzleHttp\Exception\ClientException $e ) {
			if ($e->hasResponse ()) {

				$status =  $e->getResponse ()->getStatusCode();
				return $status;					
			}
		}
		
	}
	
	/**
	 * message on lic/email missmatch of license file to license server
	 */
	private function _unauthorized() {
		
		echo 'Lizenznummer oder e-Mail stimmt nicht überein - bitte kontaktieren Sie xt:Commerce Support (helpdesk@xt-commerce.com)';
		die();
		
	}
	
	/**
	 * load organization categories
	 * @return mixed
	 */
	public function _getOrganizationCategories() {
		

		try {
			$response = $this->client->request ( 'POST', 'signup/v1/klarna/organization_categories', [
					'json' => array('language_code'=>'de')
			] );

		} catch ( GuzzleHttp\Exception\ClientException $e ) {
				
			if ($e->hasResponse ()) {

				if ($e->hasResponse ()) {
					print_r  ( $e->getResponse () );
				}
		
			}
		}
		
		
		if ($response->getStatusCode()==401) {
			$this->_unauthorized();
			return;
		}
		
		$categories = json_decode($response->getBody());
		return $categories;
	}
	
	/**
	 * load widget URL
	 * @param unknown $merchant_id
	 */
	public function _getWidgetUrl($merchant_id) {
		

		try {

            $response = $this->client->request ( 'POST', 'signup/v1/klarna/merchant_widget', [
                'json' => array('merchant_id'=>$merchant_id)
            ] );
		
		} catch ( GuzzleHttp\Exception\ClientException $e ) {
		
			if ($e->hasResponse ()) {
					
				$response = json_decode ( $e->getResponse ()->getBody () );
				echo json_encode($response);
				return;
			}
		}
			
		return json_decode($response->getBody());
		
	}
	
	/**
	 * check webservice for merchant changes
	 * @param unknown $merchant_id
	 * @param unknown $store_id
	 */
	public function _syncMerchant($merchant_id,$store_id) {
		global $logHandler,$db;
		

		try
		{
		
			$response = $this->client->request ( 'POST', 'signup/v1/klarna/sync_merchant', [
					'json' => array('merchant_id'=>$merchant_id),
					'http_erros'=>false
			] );
			 
			 
		} catch ( GuzzleHttp\Exception\ClientException $e ) {
		
			if ($e->hasResponse ()) {
				$response = json_decode ( $e->getResponse ()->getBody () );
				$logHandler->_addLog('error', 'klarna_kp:cron', 0, json_encode($response));
				return false;
			}
		}
		
		$sheduler = json_decode($response->getBody());
		
		
		if (property_exists($sheduler, 'type')) {
			switch ($sheduler->type) {
				
				case 'STATUS_UPDATE':
					
					$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store_id . " SET config_value='".$sheduler->current."' WHERE config_key='_KLARNA_CONFIG_ACCOUNT_STATUS'");
					$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store_id . " SET config_value='".$sheduler->reason."' WHERE config_key='_KLARNA_CONFIG_ACCOUNT_REASON'");
					if (self::XT_SET_ACCOUNT_TO_LIVE==true) {
						$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store_id . " SET config_value='0' WHERE config_key='_KLARNA_CONFIG_KP_TESTMODE'");
					}
					
					$logHandler->_addLog('success', 'klarna_kp:cron', 0, json_encode(array('msg' => 'Klarna Account set to: '.$sheduler->current)));

					// add popup log trigger
                    $logHandler->_addPopupNotification('success', 'klarna_kp:cron', 0, json_encode(array('msg' => array('de'=>'Ihr Klarna Checkout Status hat sich aktualisiert - bitte laden Sie die Seite neu (F5)','en'=>'Your Klarna Checkout Status has been changed - please reload the Page (F5)'))));

					// set cronjob to every 10 minutes ?
                    $db->Execute("UPDATE " . TABLE_CRON . " SET cron_value='10' WHERE cron_action='file:cron.klarna_kp.php'");

					
					break;
				
			}
			
			// confirm job
			$confirm_hash = $sheduler->hash;
			$this->confirmSheduleJob($confirm_hash);
			return true;
		}
		
		
	}
	
	/**
	 * confirm sheduled job
	 * @param unknown $hash
	 * @return boolean
	 */
	private function confirmSheduleJob($hash) {
		global $logHandler;
		
		$client = new GuzzleHttp\Client ( [
				'base_uri' => self::XT_WEBSERVICE,
				'auth' => [
						self::XT_WEBSERVICE_USER,
						self::XT_WEBSERVICE_PW
				]
		] );
		
		try
		{
		
				
			$response = $client->request ( 'POST', 'license/confirm_shedulingjob', [
					'json' => array('confirmation_hash'=>$hash),
					'http_erros'=>false
			] );
		
		
		} catch ( GuzzleHttp\Exception\ClientException $e ) {
		
			if ($e->hasResponse ()) {
				$response = json_decode ( $e->getResponse ()->getBody () );
				$logHandler->_addLog('error', 'klarna_kp:cron', 0, json_encode($response));
				return false;
			}
		}
		return true;
		
		
	}
	
}