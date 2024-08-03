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

include '../../xtFramework/admin/main.php';

if (! $xtc_acl->isLoggedIn ()) {
	die ( 'login required' );
}

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kco/classes/class.klarna_xt_webservice.php';

$xtWebService = new klarna_xt_webservice();
$xtWebService->_initiate();

$stores = $store_handler->getStores();

$tpl_data = array ();
$tpl_data['stores']=$stores;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_decode;

if (isset($_GET['page'])) {
	$page = $_GET['page'];
}
if (!isset($_GET['store'])) {
	$store=1;
} else {
	$store=(int)$_GET['store'];
}


// saving forms
if (is_array ( $_POST ) && count ( $_POST ) > 1) {
		
	switch ($page) {
		
		/**
		 * registration form
		 */
		case 'form' :
			
			$categories = $xtWebService->_getOrganizationCategories();
			
			$tpl_data['klarna_categories'] = $categories;
			
			$tpl = 'klarna_signup.html';
						
			$data = array ();
			$data ['email'] = $_POST ['account_email'];
			$data ['confirmation_email'] = $_POST ['confirmation_email'];
			$data ['country'] = $_POST ['country'];
			
			// registered merchant
			if ($_POST ['registercheck'] == '1') {
				$type = 'registered';
				$data ['organization_registration_id'] = $_POST ['organization_registration_id'];
				$data ['organization_registration_authority'] = $_POST ['organization_registration_authority'];
				if ($_POST ['novatrequired'] == '1') {
					$data ['subject_to_vat'] = false;
				} else {
					$data ['vat_id'] = $_POST ['vat_id'];
					$data ['subject_to_vat'] = true;
				}
				// unregistered
			} else {
				$type = 'unregistered';
				if ($_POST ['novatrequired'] == '1') {
					$data ['subject_to_vat'] = false;
				} else {
					$data ['vat_id'] = $_POST ['vat_id'];
					$data ['subject_to_vat'] = true;
				}
				$data ['given_name'] = $_POST ['given_name'];
				$data ['family_name'] = $_POST ['family_name'];				
				$data ['date_of_birth'] = $_POST ['date_of_birth'];
				
				$data ['street_address'] = $_POST ['street_address'];
				$data ['postal_code'] = $_POST ['postal_code'];
				$data ['city'] = $_POST ['city'];

			}
			
			// store information
			$data ['store_name'] = $_POST ['store_name'];
			$data ['organization_category'] = $_POST ['organization_category'];
			$data ['homepage_url'] = $_POST ['homepage_url'];
			$data ['support_email'] = $_POST ['support_email'];
			$data ['support_phone'] = $_POST ['support_phone'];
			
			$data ['store_id'] = '1'; 
			
			$response = $xtWebService->signup($data,$type);

			// error in form fields
			if (is_array($response)) {
	
				$tpl_data = array_merge($tpl_data,$response);
				
				$tpl = 'klarna_signup.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				return;
				break;
				
			} else {
				
				$contents = (string)$response;
				$body = json_decode($contents);
				
			}
			

			/**
			 * store username/password in configuration
			 */
			if (property_exists($body, 'username')) {
				
				$user = $body->username;
				$pw = $body->password;
				
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='" . $user . "' WHERE config_key='_KLARNA_CONFIG_KP_MID'");
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='" . $pw . "' WHERE config_key='_KLARNA_CONFIG_KP_PWD'");
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='PENDING' WHERE config_key='_KLARNA_CONFIG_ACCOUNT_STATUS'");
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='ACCEPTED' WHERE config_key='_KLARNA_CONFIG_ACCOUNT_REASON'");

				
				$tpl = 'klarna_status.html';
				
				$template = new Template ();
				$tpl_data = array();
				$tpl_data['status']='PENDING';
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );				
				echo $page_data;
				return;
				
			} else {
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );				
				echo $page_data;
				return;
			}

			break;
		
		/**
		 * existing klarna account, trying to validate
		 */
		case 'existing_account':

			$data = array();
			$data ['username'] = $_POST ['username'];
			$data ['password'] = $_POST ['password'];

			$result = $xtWebService->TestCredentials($data ['username'],$data ['password']);
			
			// unauthorized
			if ($result=='401') {
		
				$tpl = 'klarna_existing_account.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, array('error'=>'true') );
				echo $page_data;
				return;

				
			}
			// bad request, but authorized
			if ($result=='400') {
				
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='" . $data ['username'] . "' WHERE config_key='_KLARNA_CONFIG_KP_MID'");
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='" . $data ['password'] . "' WHERE config_key='_KLARNA_CONFIG_KP_PWD'");
				$db->Execute("UPDATE " . TABLE_CONFIGURATION_MULTI . $store . " SET config_value='0' WHERE config_key='_KLARNA_CONFIG_ACCOUNT_TYPE'");
				
				$tpl = 'klarna_existing_account.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, array('success'=>'true') );
				echo $page_data;
				return;
			}
			
			break;
			
	}
} else {
	
	switch ($page) {
		case 'status':
			$tpl = 'klarna_status.html';
			break;
		case 'existing_account':
			$tpl = 'klarna_existing_account.html';
			
			$template = new Template ();
			$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
			$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
			echo $page_data;
			break;
			
		case 'widget':
			$config = $store_handler->getStoreConfig(1);

			
			if (($config['_KLARNA_CONFIG_ACCOUNT_STATUS']=='TRANSACTIONS_ENABLED' && $config['_KLARNA_CONFIG_ACCOUNT_REASON']=='APPROVED') || ($config['_KLARNA_CONFIG_ACCOUNT_STATUS']=='TRANSACTIONS_DISABLED' && $config['_KLARNA_CONFIG_ACCOUNT_REASON']=='TIMEOUT')) {
				$merchant_id = explode('_',$config['_KLARNA_CONFIG_KP_MID']);
				$widget=$xtWebService->_getWidgetUrl($merchant_id[0]);

			if (is_object($widget) && property_exists($widget, 'widget_url')) {
				echo '<iframe src="'.$widget->widget_url.'" width="100%" height="1200px" frameBorder="0"></iframe>';
			} elseif (is_object($widget) && property_exists($widget,'merchant_id')) {
                echo $widget->merchant_id[0];

            }
				
			} else {
				echo 'something went wrong';
			}
			
			break;
		
		case 'register':
			

			$tpl = 'klarna_lp.html';
			$template = new Template ();
			$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
			$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
			echo $page_data;

			break;
		
		default:
			
			

			$config = $store_handler->getStoreConfig(1);

			
			// check account status
			if (_KLARNA_CONFIG_ACCOUNT_STATUS=='PENDING') {
				$tpl_data['status']='PENDING';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			} elseif(_KLARNA_CONFIG_ACCOUNT_STATUS=='TRANSACTIONS_ENABLED') {
				$tpl_data['status']='TRANSACTIONS_ENABLED';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			} elseif (_KLARNA_CONFIG_ACCOUNT_STATUS=='ADDITIONAL_REVIEW_1' || _KLARNA_CONFIG_ACCOUNT_STATUS=='ADDITIONAL_REVIEW_2' || _KLARNA_CONFIG_ACCOUNT_STATUS=='ADDITIONAL_REVIEW_3') {
				$tpl_data['status']='ADDITIONAL_REVIEW';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			}elseif (_KLARNA_CONFIG_ACCOUNT_STATUS=='SUSPENDED') {
				$tpl_data['status']='SUSPENDED';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			}elseif (_KLARNA_CONFIG_ACCOUNT_STATUS=='TRANSACTIONS_DISABLED') {
				$tpl_data['status']='TRANSACTIONS_DISABLED';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			}elseif (_KLARNA_CONFIG_ACCOUNT_STATUS=='PAYOUTS_ENABLED') {
				$tpl_data['status']='PAYOUTS_ENABLED';
				$tpl = 'klarna_status.html';
				$template = new Template ();
				$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
				$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
				echo $page_data;
				break;
			}
				
			
			$categories = $xtWebService->_getOrganizationCategories();
			
			$tpl_data['klarna_categories'] = $categories;

			
			$tpl_data['street_address']=$config['_STORE_SHOPOWNER_STREETADDRESS'];
			$tpl_data['city']=$config['_STORE_SHOPOWNER_CITY'];
			$tpl_data['postal_code']=$config['_STORE_SHOPOWNER_ZIP'];
			$tpl_data['support_phone']=$config['_STORE_SHOPOWNER_TELEPHONE'];
			
			$tpl_data['vat_id']=$config['_STORE_SHOPOWNER_VATID'];
			$tpl_data['organization_registration_id']=$config['_STORE_SHOPOWNER_REGISTERID'];
			$tpl_data['organization_registration_authority']=$$config['_STORE_SHOPOWNER_REGISTER_COURT'];
			
			$tpl_data['store_name']='';
			$tpl_data['support_email']=$config['_STORE_CONTACT_EMAIL'];
			$tpl_data['account_email']=$config['_STORE_CONTACT_EMAIL'];
			$tpl_data['confirmation_email']=$config['_STORE_CONTACT_EMAIL'];

            $proto = $config['store']->data[0]['shop_ssl'] == 1 ? 'https' : 'http';
            $shopUrl = $proto.'://'.$conf->fields['shop_ssl_domain'];
			$tpl_data['homepage_url']= $shopUrl;
			
			$tpl = 'klarna_signup.html';
			
			$template = new Template ();
			$template->getTemplatePath($tpl, 'xt_klarna_kco', 'admin', 'plugin');
			$page_data = $template->getTemplate ( 'smarty', '/' . $tpl, $tpl_data );
			
			echo $page_data;
			
			break;
	}
}
