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

class xt_sofortueberweisung{

	var $data=array();
	var $external = true;
	var $version = '1.0';

	function __construct(){

		// einstellungen bei SOU
		// Success: http://www.xt-connector.com/svn_enterprise/xtc_enterprise/index.php?page=checkout&page_action=payment_process&-KUNDEN_VAR_2-=-KUNDEN_VAR_1-&sovar3=-KUNDEN_VAR_3-&sovar4=-KUNDEN_VAR_3_MD5_PASS-&betrag=-BETRAG-
	}

	function build_payment_info($data){

	}

	function pspRedirect($order_data) {
		global $xtLink,$filter,$order, $info;

		$target_url = 'https://www.sofortueberweisung.de/payment/start';

		$data = array();
		$orders_id = (int)$order_data['orders_id'];
		if (is_int($orders_id)) {
			$data['user_id'] = constant('XT_SOFORTUEBERWEISUNG_USER_ID');
			$data['project_id'] = constant('XT_SOFORTUEBERWEISUNG_PROJECT_ID');
			$data['amount'] = $order->order_total['total']['plain'];
			$data['currency_id'] = $order->order_data['currency_code'];
			
			
			// verwendungszweck
			$data['reason_1'] = $orders_id;
			$data['reason_2'] = $order->order_data['billing_firstname'].' '.$order->order_data['billing_lastname'];
				
				
			$data['user_variable_0'] = $orders_id.':'.$order->order_data['customers_id'];
			$data['user_variable_1'] = session_id();
			$data['user_variable_2'] = session_name();
			$data['sender_country_id'] = $order->order_data['billing_country_code'];
			$data['interface_version'] = 'mz_xtc4_v4.0';

				

			// Input ?berpr?fung
			$hash_array = array(
			$data['user_id'],  // user_id
			$data['project_id'],  // project_id
 			$data['sender_holder'],   // sender_holder 
 			'',   // sender_account_number 
 			'',   // sender_bank_code 
 			$data['sender_country_id'],   // sender_country_id 
			$data['amount'],  // amount
			$data['currency_id'],   // currency_id
			$data['reason_1'], // reason_1
			$data['reason_2'],   // reason_2
			$data['user_variable_0'],   // user_variable_0
			$data['user_variable_1'],   // user_variable_1
			$data['user_variable_2'],   // user_variable_2
			'',   // user_variable_3 
 			'',   // user_variable_4 
 			'',   // user_variable_5 
			constant('XT_SOFORTUEBERWEISUNG_PROJECT_PASSWORD')  // project_password
			);

			$data_implode = implode('|', $hash_array);
			$hash = md5($data_implode);
			$data['hash']=$hash;

            //error_log('------------  redirecting');
            //error_log('created hash sofort => '.$hash);
				
			$params = '';
			$cnt = 0;
			foreach ($data as $key => $value) {
				($cnt == 0 ) ? $params .= '?'.$key.'='.urlencode(stripslashes($value)) : $params .= '&'.$key.'='.urlencode(stripslashes($value));
				$cnt++;
			}

			// redirect
			//echo $xtLink->_redirect($target_url.$params);
			//break;
			//$xtLink->_redirect($target_url.$params);
            //error_log($target_url.$params);
			return $target_url.$params;
		}

        $info->_addInfoSession('error', constant('TEXT_ORDER_NUMBER').': '.constant('TEXT_NOT_SPECIFIED'));
        return $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment', 'params' => 'error=ERROR_PAYMENT'));
	}
	
	function pspSuccess() {
		return true;
	}
}