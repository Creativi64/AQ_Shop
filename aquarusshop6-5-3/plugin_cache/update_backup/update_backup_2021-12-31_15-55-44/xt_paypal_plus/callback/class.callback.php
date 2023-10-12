<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';

class callback_xt_paypal_plus extends callback {
	
	function process() {

		if ($_GET['page_action']=='xt_paypal_plus')
		{
			$this->data = array();
			$raw_post_data = file_get_contents('php://input');
			
			$result = json_decode($raw_post_data);
			if ($result->resource->parent_payment){
				$ppp = new paypal_plus;
				$o_id = $ppp->checkSaleID($result->resource->parent_payment, $result->resource->invoice_number);
				$ppp->orders_id = $o_id;
				$ppp->transaction_id = $result->resource->id;

				if (defined('PAYPAL_PLUS_API_LOGGING') && PAYPAL_PLUS_API_LOGGING == 1)
				{
					$log_string = '-------  IPN ' . $result->id . PHP_EOL . print_r($result, true);
					$ppp->dataLog($log_string);
				}

				if ($o_id>0)
				{
					$ppp->customer = -1;
					$ppp->status_comment=$result->summary;
					$ppp->_setStatus($result->event_type,1,'callback', $result, $o_id);
				}else
				{
					$log_data['class'] = 'callback';
					$log_data['transaction_id'] = $result->resource->parent_payment;
					$log_data['callback_data'] = serialize((array)$result);
					$ppp->_addCallbackLog($log_data);
				}
				
				
			}
			header("HTTP/1.0 200 OK"); 
			die('200 OK');			
		}
    }
    
}
?>