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

require_once _SRV_WEBROOT.'plugins/xt_paypal_plus/functions/lang_mapping.php';

class paypal_plus
{

	var $Client_ID,
		$Secret_key,
		$API_Endpoint,
		$authCall,
		$customer,
		$address_override,
		$logsuccess_ipn, $orders_id, $sale_id;

	var $api_version = 'v1';

	function __construct($override_mode = false)
	{
		global $xtLink, $xtPlugin;

		$mode = $override_mode ? $override_mode :XT_PAYPAL_PLUS_MODE;

		if ($mode == 'sandbox')
		{
			$this->Client_ID = trim(XT_PAYPAL_PLUS_SANDBOX_CLIENT_ID);
			$this->Secret_key = trim(XT_PAYPAL_PLUS_SANDBOX_SECRET_KEY);
			$this->API_Endpoint = 'https://api.sandbox.paypal.com/';
		}
		else
		{
			$this->Client_ID = trim(XT_PAYPAL_PLUS_CLIENT_ID);
			$this->Secret_key = trim(XT_PAYPAL_PLUS_SECRET_KEY);
			$this->API_Endpoint = 'https://api.paypal.com/';

		}
		$this->PPP_partnerID = "xtc4_Cart_ppplus";
		$this->address_override = 1;
		$this->customer = $_SESSION['customer']->customers_id;
		$this->logsuccess_ipn = PAYPAL_PLUS_VERBOSE_IPN_LOGGING == 1 ? true : false;
		$this->authCall = $this->API_Endpoint . $this->api_version . '/oauth2/token';
		$this->webProfile = $this->API_Endpoint . $this->api_version . '/payment-experience/web-profiles';
		$this->createPayment = $this->API_Endpoint . $this->api_version . '/payments/payment';
		$this->lookupPayment = $this->API_Endpoint . $this->api_version . '/payments/payment/';
		$this->refundPayment = $this->API_Endpoint . $this->api_version . '/payments/sale/sale_id/refund';
		$this->lookupSale = $this->API_Endpoint . $this->api_version . '/payments/sale/';
		$this->webhooks = $this->API_Endpoint . $this->api_version . '/notifications/webhooks';
		$this->position = 'user';
		($plugin_code = $xtPlugin->PluginCode('class.paypal_plus.php:__construct_bottom')) ? eval($plugin_code) : false;
	}

	function setPosition($position)
	{
		$this->position = $position;
	}

	function setPaymentID($payment_id)
	{
		$this->payment_id = $payment_id;
	}

	function dataLog($data_string)
	{
		$f = fopen(_SRV_WEBROOT . 'xtLogs/paypal_plus.log', 'a+');
		if ($f)
		{
			$dt = date("[Y-m-d H:i:s] ".PHP_EOL);
			fwrite($f, $dt . $data_string . PHP_EOL);
			fclose($f);
		}
	}

	function _addCallbackLog($log_data)
	{
		global $db;
		//if (is_null($log_data['transaction_id'])) return false;
		$log_data['module'] = 'xt_paypal_plus';

		$log_data['orders_id'] = isset($this->orders_id) ? $this->orders_id : $this->customer;
		if (is_array($log_data['callback_data']))
		{
			$log_data['callback_data'] = serialize($log_data['callback_data']);
		}
		if (is_array($log_data['error_data']))
		{
			$log_data['error_data'] = serialize($log_data['error_data']);
		}
		$db->AutoExecute(TABLE_CALLBACK_LOG, $log_data, 'INSERT');

		return $db->Insert_ID();
	}

	/*Generates the security tocken
     * @return access data object*/
	function generateSecurityToken()
	{
		$data = array('grant_type' => 'client_credentials');
		$result = json_decode($this->ApiAUTHCall($data));

		if ($result->access_token)
		{
			$_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN'] = $result->access_token;
			$_SESSION['plus_' . $this->customer]['PPPLUS_TOCKEN_TYPE'] = $result->token_type;
			$_SESSION['plus_' . $this->customer]['PPPLUS_EXPIRES_IN'] = $result->expires_in;
			$_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN_TIME'] = time();
		}
		else if($result->error)
		{
			$msg = 'xt_paypal_plus - could not get security token in generateSecurityToken';
			if(!empty($result->error_description))
			{
				$msg .= ' - msg: '.$result->error_description;
			}
            $msg .= PHP_EOL. '  result => '. json_encode($result);
            $this->dataLog($msg);
		}
		else {
            $msg = 'xt_paypal_plus - could not get security token in generateSecurityToken';
            $msg .= '  result => '. json_encode($result);
            $this->dataLog($msg);
        }
		return $result;
	}

	/* generateUserProfile
	 * Generates user web-profile ID 
	 * @return user web-profile ID*/
	function generateUserProfile()
	{
		global $store_handler, $db, $language;
		$call_name = 'Generate User Profile ID: ';
		$plus_customer = $_SESSION['customer']->_buildAddressData($this->customer, 'xt_paypal_plus');
		$customer_name = substr(urlencode($plus_customer['customers_firstname'] . ' ' . $plus_customer['customers_lastname']), 0, 20) . time();

        $tableExists = $db->GetOne("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME='" . TABLE_CONFIGURATION_LANG_MULTI . "' ");
        if($tableExists)
        {
            $brandName = $db->GetOne('SELECT language_value FROM '.TABLE_CONFIGURATION_LANG_MULTI." WHERE config_key='_STORE_NAME' AND language_code=? AND store_id=?", array($language->code, $store_handler->shop_id));
        }
        else $brandName = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_MULTI.$store_handler->shop_id." WHERE config_key='_STORE_NAME'");
        if(empty($brandName))
        {
            $brandName = $db->GetOne('SELECT shop_ssl_domain FROM '.TABLE_MANDANT_CONFIG." WHERE shop_id=?", array($store_handler->shop_id));
        }
		if(strlen($brandName)>127)
		{
			$brandName = preg_replace('/\s+?(\S+)?$/', '', substr($brandName, 0, 123)).'...';
		}

		$data = array(
			"name" => $customer_name,
			"presentation" => array(
				"brand_name" => $brandName,
				"locale_code" => _STORE_COUNTRY,

			),
			"input_fields" => array(
				"allow_note" => false,
				"no_shipping" => 1, //(($_SESSION['cart']->type=='virtual') || ($_SESSION['cart']->type=='mixed'))?1:0,
				"address_override" => $this->address_override
			)
		);

		// check/add logo image
		$a = parse_url(XT_PAYPAL_PLUS_LOGO);
		if(empty($a['scheme'])) $a['scheme'] = 'https';
		if($a['scheme'] == 'https')
		{
			$url = 'https://';
			if(!empty($a['host']))
			{
				$url .= $a['host'].$a['path'];
			}
			else if(!empty($a['path']))
			{
				$url .= $a['path'];
			}
			if($url!='https://')
			{
				$data['presentation']['logo_image'] = $url;
			}
		}

		$res = $this->ApiJSONCall($data, $this->webProfile, $call_name);
		$result = json_decode($res);

		if ($result->id)
		{
			$_SESSION['plus_' . $this->customer]['PPPLUS_USER_PROFILE_ID'] = $result->id;
			if ($this->logsuccess_ipn)
			{
				$log_data['class'] = 'success_generate_profile';
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_generate_profile';
			if ($result = '')
			{
				$result = $res;
			}
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_msg'] = $call_name . serialize($result) . ' - Customer: ' . $customer_name;
			$log_data['error_data'] = $data;
			$this->_addCallbackLog($log_data);
		}
		return $result;
	}

	/* getCartProducts
	* get current cart product 
	* @return an array of cart products
	*/
	function getCartProducts()
	{
		global $customers_status, $currency, $price, $db;
		$items = array();
		// products
		$i = $itmamt = $itmtax = 0;
		foreach ($_SESSION['cart']->show_content as $key => $arr)
		{
			$_price = $arr['products_price']['plain'];
			// processing  xt_products_options
			if (is_array($arr['products_info_data'])
				&& is_array($arr['products_info_data']['options'])
				&& count($arr['products_info_data']['options']))
			{
				// $arr['products_price']['plain'] became something we cant handle anymore
				// it misses the single options / $products_count
				$_price = $arr['products_final_price']['plain'] / $arr['products_quantity'];
			}

			if ($customers_status->customers_status_show_price_tax != '1')
			{
				$_price += $arr['products_tax']['plain'];
			}
            $itemPrice = round($_price, 3, PHP_ROUND_HALF_DOWN);
            $itemPrice = round($itemPrice, 2, PHP_ROUND_HALF_DOWN);

			// change 1.00 to 1
			// but dont touch 1.5
			// 1.5 will throw error in paypal, thats what we want
			$qnt = floatval($arr['products_quantity']);
			$qnt = $qnt === round($qnt) ? intval($qnt) : $arr['products_quantity'];

			$e_item = array(
				"name" => $arr['products_name'],
				//"sku"=>$arr['products_model'],
				"price" => number_format($itemPrice, 2, '.', ''),
				"currency" => $currency->code,
				"quantity" => $qnt
			);
			if (!empty($arr['products_model']))
			{
				$e_item['sku'] = $arr['products_model'];
			}
			$itmamt += $itemPrice * $qnt;

			array_push($items, $e_item);
			$i++;
		}
		$data['products_total'] = $itmamt + $itmtax;

		// calculate correction
		$xt_total = $_SESSION['cart']->content_total['plain'];
		if ($customers_status->customers_status_show_price_tax != '1')
		{
			foreach ($_SESSION['cart']->content_tax as $_tax)
			{
				$xt_total += $_tax['tax_value']['plain'];
			}
		}
        //$xt_total = round($xt_total, 3, PHP_ROUND_HALF_DOWN);
        $xt_total = round($xt_total, 2);
        //echo $xt_total.' - '.$data['products_total'].' - ';
		if($xt_total != $data['products_total'])
		{
			$correction_amount = round($xt_total - $data['products_total'], 3, PHP_ROUND_HALF_UP);
			if($correction_amount!=0)
			{
                $correction_amount = round($correction_amount, 2, PHP_ROUND_HALF_UP); // if -0.005
				$e_item = array(
					"name" => TEXT_PPP_CORRECTION_ROUNDING,
					"price" => $correction_amount,
					"currency" => $currency->code,
					"quantity" => 1
				);

                //echo $correction_amount;

				$data['products_total'] += $correction_amount;

				array_push($items, $e_item);
			}
		}

		$data['items'] = $items;

		// Other like shipping etc
		$handlingamt = 0;
		$data['freeshipping'] = false;
		if (is_array($_SESSION['cart']->show_sub_content))
		{
			foreach ($_SESSION['cart']->show_sub_content as $key => $arr)
			{
				if ($key == 'shipping')
				{
					$_price = $arr['products_final_price']['plain'];
					if ($customers_status->customers_status_show_price_tax != '1')
					{
						$_price += $arr['products_final_tax']['plain'];
					}
					$_price = round($_price, 2);

					$data['shipping'] = $_price;
					if ($_price < 0)
					{ // some kind of discount
						$data['shipping_discount'] += $_price;
					}
					else
					{
						$itmamt += $_price;
					}
				}
				else
				{
					if ($key == 'payment')
					{  // there is no payment fee when entering payment page
						/*
                                            if($customers_status->customers_status_show_price_tax != '1')
                                            {
                                                $pprice = $price->_BuildPrice($arr['products_price']['plain_otax'], $arr['products_tax_class']);
                                                $itemtax = $pprice - $arr['products_price']['plain'];
                                            }
                                            else {
                                                $itemtax = round($arr['products_tax']['plain'],2);
                                            }*/
						//$handlingamt += $arr['products_final_price']['plain'];
					}
					else
					{
						if ($key == 'xt_coupon') /* ?? checkout option for example */
						{
							$_price = $arr['products_final_price']['plain'];
							if ($customers_status->customers_status_show_price_tax != '1')
							{
								$_price += $arr['products_final_tax']['plain'];
							}
							$itemPrice = round($_price, 2);

							$data['shipping_discount'] += $itemPrice;
						}
						else /* ?? checkout option for example */
						{
							// for now anything other than shipping is treated as handling fee
							// read 4.5 in https://www.paypal.com/us/webapps/mpp/ua/useragreement-full
							$_price = $arr['products_final_price']['plain'];
							if ($customers_status->customers_status_show_price_tax != '1')
							{
								$_price += $arr['products_final_tax']['plain'];
							}
							$itemPrice = round($_price, 2);

							$handlingamt += $itemPrice;
						}
					}
				}
			}
		}

		$ha = $this->PPPaditionalPrice();
		if ($ha < 0)
		{
			$data['shipping_discount'] += $ha;
		}
		else
		{
			$handlingamt += $ha;
		}

		if (round($handlingamt, 2) != 0 && defined('XT_PAYPAL_PLUS_CORRECTION_PLUS') &&  XT_PAYPAL_PLUS_CORRECTION_PLUS == true)
		{
			$data['handling'] = number_format($handlingamt, 2, '.', ''); //abs(number_format($handlingamt, 2, '.', ''));
		}
		return $data;
	}

	/* _getCartData
	 * Get cart informations for payment call
	 * @return unknown
	 */
	function _getCartData()
	{
		global $currency, $db;

		$buyer_data = array();
		$pr_data = $this->getCartProducts();

		$amount = $pr_data['products_total'] + $pr_data['shipping'] + $pr_data['handling'] + $pr_data['shipping_discount'];


		//echo 'ppp amount=>'.$amount.'    rounded=>'.number_format($amount, 2, '.', '');

		$buyer_data['amount'] = array(
			"total" => number_format($amount, 2, '.', ''),
			"currency" => $currency->code,
			"details" => array(
				'subtotal' => number_format($pr_data['products_total'], 2, '.', ''),
				'shipping' => number_format((isset($pr_data['shipping'])) ? $pr_data['shipping'] : '0', 2, '.', ''),
				'shipping_discount' => number_format((/*$_SESSION['cart']->total_discount +*/
				$pr_data['shipping_discount']), 2, '.', ''),
				'handling_fee' => number_format((isset($pr_data['handling'])) ? $pr_data['handling'] : '0', 2, '.', '')
			)
		);
		$basket_id = $db->GetOne("Select basket_id FROM " . TABLE_CUSTOMERS_BASKET . " WHERE customers_id=?", array($this->customer));
		$buyer_data['invoice_number'] = $this->customer . '_' . time();
		$buyer_data['custom'] = 'Basket ID ' . $basket_id;

		/*
		$plus_customer_payment_address = $_SESSION['customer']->customer_payment_address;
		$plus_customer_shipping_address = $_SESSION['customer']->customer_shipping_address;

		$payment_address =array(
				'recipient_name'=> substr(($plus_customer_payment_address['customers_firstname'].' '.$plus_customer_payment_address['customers_lastname']),0,40),
				'line1'=>  $plus_customer_payment_address['customers_street_address'],
				'city' => $plus_customer_payment_address["customers_city"],
				'postal_code' => $plus_customer_payment_address["customers_postcode"],
				'country_code' => $plus_customer_payment_address["customers_country_code"]
		);
		$shipping_address =array(
				'recipient_name'=> substr(($plus_customer_shipping_address['customers_firstname'].' '.$plus_customer_shipping_address['customers_lastname']),0,40),
				'line1'=>  $plus_customer_shipping_address['customers_street_address'],
				'city' => $plus_customer_shipping_address["customers_city"],
				'postal_code' => $plus_customer_shipping_address["customers_postcode"],
				'country_code' => $plus_customer_shipping_address["customers_country_code"]
		);
		*/

		$buyer_data['item_list'] = array(
			"items" => $pr_data['items'],
			//"billing_address"=>$payment_address,
			//"shipping_address"=>$shipping_address,
		);

		return $buyer_data;
	}

	/*createPayment
	* Genertaes payment ID
	*/
	function createPayment()
	{
		global $xtLink;
		$d = $this->_getCartData();
		$call_name = 'Create Payment ID: ';
		$data = array("intent" => "sale",
			"experience_profile_id" => $_SESSION['plus_' . $this->customer]['PPPLUS_USER_PROFILE_ID'],
			"payer" => array("payment_method" => "paypal"),
			"transactions" => array($d),
			"redirect_urls" => array(
				'return_url' => $xtLink->_link(array('page' => 'xt_paypal_plus', 'params' => 'success=true', 'conn' => 'SSL')),
				'cancel_url' => $xtLink->_link(array('page' => 'xt_paypal_plus', 'params' => 'error_payments=ERROR_PAYMENT', 'conn' => 'SSL'))
			)
		);

		$result = json_decode($this->ApiJSONCall($data, $this->createPayment, $call_name));

		if (isset($result->links))
		{
			foreach ($result->links as $link)
			{
				$_SESSION['plus_' . $this->customer][$link->rel] = $link->href;
			}
			$_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'] = $result->id;
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'success_create_payment';
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_create_payment';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_data'] = $data;
			$log_data['error_msg'] = $call_name . serialize($result);
			$this->_addCallbackLog($log_data);
		}
	}


	/* set order id in paypal
	*  done before execute payment
	 * utilizes patch payment call
	 * also field custom is set to shop url
	*/
	function setOrderId($orderId)
	{
		global $xtLink;

		$call_name = 'Set order ID - Payment ID: ';
		$data = array(
			array(
				"op" => "add",
				"path" => "/transactions/0/invoice_number",
				"value" => '' . $orderId
			),
			array(
				"op" => "add",
				"path" => "/transactions/0/custom",
				"value" => $xtLink->_link(array())
			),
		);

		$result = json_decode($this->ApiJSONCall($data, $this->createPayment . '/' . $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'], $call_name, 'PATCH'));
		if (isset($result->id))
		{
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'success_set_order_id';
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_set_order_id';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_msg'] = $call_name . serialize($result);
			$log_data['error_data'] = $data;
			$this->_addCallbackLog($log_data);

			throw new Exception('Could not set Order Id in PayPal');
		}

	}

	function patchAddresses()
	{
		$plus_customer_payment_address = $_SESSION['customer']->customer_payment_address;
		$plus_customer_shipping_address = $_SESSION['customer']->customer_shipping_address;

		$payment_address = array(
			//'recipient_name'=> substr(($plus_customer_payment_address['customers_firstname'].' '.$plus_customer_payment_address['customers_lastname']),0,40),
			'line1' => $plus_customer_payment_address['customers_street_address'],
			'line2' => $plus_customer_payment_address['customers_address_addition'],
			'city' => $plus_customer_payment_address["customers_city"],
			'state' => !empty($plus_customer_payment_address['customers_federal_state_code']) ? $plus_customer_payment_address['customers_federal_state_code'] : '',
			'postal_code' => $plus_customer_payment_address["customers_postcode"],
			'country_code' => $plus_customer_payment_address["customers_country_code"]
		);
		$shipping_address = array(
			'recipient_name' => substr(($plus_customer_shipping_address['customers_firstname'] . ' ' . $plus_customer_shipping_address['customers_lastname']), 0, 40),
			'line1' => $plus_customer_shipping_address['customers_street_address'],
			'line2' => $plus_customer_shipping_address['customers_address_addition'],
			'city' => $plus_customer_shipping_address["customers_city"],
			'state' => '',
			'postal_code' => $plus_customer_shipping_address["customers_postcode"],
			'country_code' => $plus_customer_shipping_address["customers_country_code"]
		);


		$call_name = 'Patch Addresses: ';
		$data = array(
			array(
				"op" => "add",
				"path" => "/potential_payer_info/billing_address",
				"value" => $payment_address
			),
			array(
				"op" => "add",
				"path" => "/transactions/0/item_list/shipping_address",
				"value" => $shipping_address
			)
		);

		$result = json_decode($this->ApiJSONCall($data, $this->createPayment . '/' . $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'], $call_name, 'PATCH'));
		if (isset($result->id))
		{
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'success_patch_addresses';
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_patch_addresses';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_msg'] = $call_name . serialize($result);
			$log_data['error_data'] = $data;
			$this->_addCallbackLog($log_data);
		}
	}


	/*patchPayment
	* Updates payment resource
	*/
	function patchPayment()
	{
		global $xtLink, $currency;
		$pr_data = $this->getCartProducts();;
		$call_name = 'Patch Payment ID: ';
		$data = array("op" => "replace",
			"path" => "/transactions/0/amount",
			"value" => array(
				"total" => $_SESSION['cart']->total['plain'],
				"currency" => $currency->code,
				"details" => array(
					'subtotal' => number_format($pr_data['products_total'], 2, '.', ''),
					'shipping' => number_format((isset($pr_data['shipping'])) ? $pr_data['shipping'] : '0', 2, '.', ''),
					'shipping_discount' => number_format(($_SESSION['cart']->total_discount + $pr_data['shipping_discount']), 2, '.', ''),
					'handling_fee' => number_format((isset($pr_data['handling'])) ? $pr_data['handling'] : '0', 2, '.', '')
				)
			)
		);

		$result = json_decode($this->ApiJSONCall($data, $this->createPayment . '/' . $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'], $call_name));
		if (isset($result->id))
		{
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'success_patch_payment';
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_patch_payment';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_msg'] = $call_name . serialize($result);
			$log_data['error_data'] = $data;
			$this->_addCallbackLog($log_data);
		}

	}

	/*getPaymentDetails
	* get details for authorized payment 
	* @params 
	*/
	function getPaymentDetails($payerID = '', $paymentID = '')
	{
		$data = array();
		$call_name = 'Payment Details: ';
		if ($paymentID == '')
		{
			$lookup_url = $_SESSION['plus_' . $this->customer]["self"];
		}
		else
		{
			$lookup_url = $this->lookupPayment . $paymentID;
		}
		$res = $this->ApiJSONCall($data, $lookup_url, $call_name, "GET");
		$result = json_decode($res);

		if ($result->id)
		{
			if ($payerID != '')
			{
				$_SESSION['plus_' . $this->customer]['PPPLUS_PAYER_ID'] = $payerID;
			}

			if (($this->address_override == 1) && ($this->position == 'user'))
			{
				//$this->setAddress($this->customer,$result);
			}

			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['transaction_id'] = $paymentID;
				$log_data['class'] = 'success_payment_details';
				$log_data['callback_data'] = $call_name . serialize((array)$result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['transaction_id'] = $paymentID;
			$log_data['class'] = 'error_payment_details';
			if (!isset($result))
			{
				$result = $res;
			}
			$log_data['error_msg'] = $call_name . $result;
			$log_data['error_data'] = $lookup_url;
			$this->_addCallbackLog($log_data);
		}

		return $result;
	}

	/*getSaleDetails
	* get details for authorized sale 
	* @params 
	*/
	function getSaleDetails($saleID, $paymentID)
	{
		$data = array();
		$call_name = 'Sale Details: ';
		$lookup_url = $this->lookupSale . $saleID;
		$res = $this->ApiJSONCall($data, $lookup_url, $call_name, "GET");
		$result = json_decode($res);
		if ($result->id)
		{
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['transaction_id'] = $paymentID;
				$log_data['class'] = 'success_payment_details';
				$log_data['callback_data'] = $call_name . serialize((array)$result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['transaction_id'] = empty($paymentID) ? 0 : $paymentID;
			$log_data['class'] = 'error_payment_details';
			if (!isset($result))
			{
				$result = $res;
			}
			$log_data['error_msg'] = $call_name . ' ' . $res;
			$log_data['error_data'] = $lookup_url;
			$this->_addCallbackLog($log_data);

			$msg = Plus_buildErrorMessage($result);
			throw new Exception($msg);
		}

		return $result;
	}

	/*PaymentInfo
	* Returns payment info
	* @params $result REST data 
	* @params $return_sale_id - retunr only sale_id or not
	*/
	function PaymentInfo($result, $return_sale_id = 0, $label = '', &$str = '')
	{
		//$str='';
		$data = json_decode(json_encode($result), true);

		if ($return_sale_id == 1)
		{
			foreach ($data as $k => $d)
			{
				if (is_array($d))
				{
					if ((string)$k == 'sale')
					{
						return $d['id'];
					}
					$str .= $this->PaymentInfo($d, $return_sale_id, $str);
				}
			}
		}
		else
		{
		    if (is_string($str)) $str = [];

			foreach ($data as $k => $d)
			{
				if ((string)$k != 'links')
				{
					if (is_array($d))
					{
						$this->PaymentInfo($d, $return_sale_id, $k, $str);
					}
					else
					{
						$l = 'TEXT_PPP_' . $label . (($label != '') ? '_' : '') . $k;
						$str[$k] = $d;
					}
				}
			}
		}

		return $str;
	}


	/*executePayment
	* get details for authorized payment 
	* @params 
	*/
	function executePayment($order)
	{
		global $db;
		$this->_setOrderId($order->oID);

		$data = array("payer_id" => $_SESSION['plus_' . $this->customer]['PPPLUS_PAYER_ID']);
		$call_name = 'Execute Payment: ';
		$res = $this->ApiJSONCall($data, $_SESSION['plus_' . $this->customer]["execute"], $call_name);

		// test declined error
		//$res = '{"name":"INSTRUMENT_DECLINED","details":[],"message":"The instrument presented  was either declined by the processor or bank, or it can\'t be used for this payment.","information_link":"https://developer.paypal.com/docs/api/#INSTRUMENT_DECLINED","debug_id":"e9f1412bf0aa9"}';

		$result = json_decode($res);

		if ($result->id)
		{
			$sale_id = $this->PaymentInfo($result, 1);
			$tr_details = $this->getSaleDetails($sale_id, $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID']);
			$this->data = $tr_details;
			$this->transaction_id = $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'] . '_' . $sale_id;
			//$this->_setStatus((string)$tr_details->state, 0, 'success');

			$db->Execute("UPDATE " . TABLE_ORDERS . " SET
									PPP_PAYMENTID = ?,
									PPP_SALEID = ?
								WHERE orders_id = ?", array($_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'], $sale_id, $order->oID));

			// check for PAY_UPON_INVOICE
			if (property_exists($result, 'payment_instruction')
				&& property_exists($result->payment_instruction, 'instruction_type')
				&& $result->payment_instruction->instruction_type == 'PAY_UPON_INVOICE'
			)
			{
				require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_bank_details/classes/class.xt_bank_details.php';
				$bad = new xt_bank_details();

				$data = array(
					COL_BAD_BANK_IDENTIFIER_CODE => $result->payment_instruction->recipient_banking_instruction->bank_identifier_code,
					COL_BAD_BANK_NAME => $result->payment_instruction->recipient_banking_instruction->bank_name,
					COL_BAD_INTERNATIONAL_BANK_ACCOUNT_NUMBER => $result->payment_instruction->recipient_banking_instruction->international_bank_account_number,
					COL_BAD_ACCOUNT_NAME => $result->payment_instruction->recipient_banking_instruction->account_holder_name,
					COL_BAD_REFERENCE_NUMBER => $result->payment_instruction->reference_number,
					COL_BAD_DUE_DATE => $result->payment_instruction->payment_due_date,
					COL_BAD_TECH_ISSUER => 'xt_paypal_plus',
					COL_BAD_ADD_DATA => serialize($result->payment_instruction)
				);
				$res_insert = $bad->_set($data);
				if ($res_insert->success == true)
				{
					$sql = "INSERT INTO " . TABLE_BAD_ORDER_BANK_DETAILS . " (" . COL_BAD_ORDERS_ID . "," . COL_BAD_BANK_DETAILS_ID . ") VALUES(?,?)";
					$db->Execute($sql, array($order->oID, $res_insert->new_id));
				}
				else
				{
					$log_data = array();
					$log_data['class'] = 'error_save_payment_instruction';
					$log_data['transaction_id'] = $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'];
					if (!isset($result))
					{
						$result = $res;
					}
					$log_data['error_msg'] = $call_name . serialize((array)$result);
					$this->_addCallbackLog($log_data);
				}
			}

			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'success_execute_payment';
				$log_data['transaction_id'] = $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'];
				$log_data['callback_data'] = $call_name . serialize((array)$result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$msg = Plus_buildErrorMessage($result);

			$log_data = array();
			$log_data['class'] = 'error_execute_payment';
			$log_data['transaction_id'] = $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'];
			if (!isset($result))
			{
				$result = $res;
			}
			$log_data['callback_data'] = serialize((array)$result);
			$log_data['error_msg'] = $call_name . ': '.$msg;
			$logId = $this->_addCallbackLog($log_data);

			//($status, $comments = '', $send_email = 'true', $send_comments = 'true', $trigger = 'user', $callback_id = 0, $callback_message = '')
			$order->_updateOrderStatus(XT_PAYPAL_PLUS_ORDER_STATUS_FAILED, $msg, 'true', 'true', 'user', $logId, $log_data['error_msg']);

			throw new Exception($msg);
		}
	}


	/*refundPayment
	* get details for authorized payment 
	* @params 
	*/
	function refundPayment($data, $url, $payment_id)
	{
		$call_name = 'Refund Payment: ';
		$res = $this->ApiJSONCall($data, $url, $call_name);
		$result = json_decode($res);

		if ($result->id)
		{
			$this->data = $result;
			if ($this->logsuccess_ipn)
			{
				$log_data = array();
				$log_data['class'] = 'callback_data_ppp_refund';
				$log_data['transaction_id'] = $payment_id;
				$log_data['callback_data'] = $call_name . serialize($result);
				$this->_addCallbackLog($log_data);
			}
		}
		else
		{
			$log_data = array();
			$log_data['class'] = 'error_data_ppp_refund';
			$log_data['transaction_id'] = $payment_id;
			if (!isset($result))
			{
				$result = $res;
			}
			$log_data['error_msg'] = $call_name . serialize($result);
			$log_data['error_data'] = $data;
			$this->_addCallbackLog($log_data);
		}
		return $result;
	}

	function setUpWebhooks($store_id)
	{
		global $xtLink, $db;

		$this->customer = 'admin_' . time();
		$this->position = 'admin';
		$this->orders_id = time();
		$this->generateSecurityToken();

		if(empty($_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN']))
		{
			$res = new stdClass();
			$res->error = "Shop $store_id: " .'Security token nicht vorhanden. Kombination Client-ID/Passwort falsch?';
			return $res;
		}

        $domain = $db->GetOne('SELECT shop_ssl_domain FROM '.TABLE_MANDANT_CONFIG. " WHERE shop_id=?", array($store_id));

        $secureUrl = 'https://'.$domain;

		$xtLink->setSecureLinkURL($secureUrl);
		$xtLink->setLinkURL($secureUrl);
        $url = $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_paypal_plus', 'conn' => 'SSL'), 'xtAdmin/');
		if (strpos($url, "https://") === false)
		{
			$res = new stdClass();
			$res->error = "Shop $store_id: " .TEXT_PPP_NO_SSL_FOR_WEBHOOK;
			return $res;
		}

		$exp = explode("xt_paypal_plus", $url);
        $url = $exp[0] . "xt_paypal_plus";
		$url = str_replace('&amp;', '&', $url);
		$ret = $this->createWebhooks($url);
		$this->unsetPayPalPlusSessions($this->customer);
		return $ret;
	}

	function createWebhooks($url)
	{
		global $db;

        $call_name = 'List Webhooks: ';
        $res = $this->ApiJSONCall(array(), $this->webhooks, $call_name, 'GET');

        $webhooks = json_decode($res)->webhooks;

        if(is_array($webhooks))
        {
            foreach($webhooks as $hook)
            {
                if(strpos('page_action=xt_paypal_plus',$hook->url) >=0)
                {
                    $call_name = 'Delete Webhook: ';
                    $this->ApiJSONCall(array(), $this->webhooks.'/'.$hook->id, $call_name, 'DELETE');
                }
            }
        }

		$call_name = 'Create Webhook: ';
		$data = array("url" => $url,
			"event_types" => array(
				array("name" => "PAYMENT.AUTHORIZATION.CREATED"),
				array("name" => "PAYMENT.AUTHORIZATION.VOIDED"),
				array("name" => "PAYMENT.CAPTURE.REFUNDED"),
				array("name" => "PAYMENT.CAPTURE.REVERSED"),
				array("name" => "PAYMENT.SALE.COMPLETED"),
				array("name" => "PAYMENT.SALE.REFUNDED"),
				array("name" => "PAYMENT.SALE.REVERSED"),
				array("name" => "PAYMENT.SALE.PENDING")
			),
		);
		$res = $this->ApiJSONCall($data, $this->webhooks, $call_name);
		$ret = json_decode($res);

		return $ret;
	}

	/**
	 * Set status of order based on returned transaction status
	 *
	 */
	function _setStatus($status, $send_mail = 1, $class = "success", $callback_data = array(), $orders_id = 0)
	{
		global $store_handler;
		$log_data = array();
		$transaction_id = isset($this->transaction_id) ? $this->transaction_id : $_SESSION['plus_' . $this->customer]['PPPLUS_PAYMENT_ID'];
		$log_data['transaction_id'] = $transaction_id;
		$log_data['error_data'] = $status;

		/**
		 * 1.3.0 registered events
		 *
		 * 	PAYMENT.AUTHORIZATION.CREATED
		    PAYMENT.AUTHORIZATION.VOIDED
		    PAYMENT.CAPTURE.REFUNDED
		    PAYMENT.CAPTURE.REVERSED
		    PAYMENT.SALE.COMPLETED
		    PAYMENT.SALE.REFUNDED
		    PAYMENT.SALE.REVERSED
		    PAYMENT.SALE.PENDING
		 */

		$log_data['callback_data'] = array(
			'message' => 'OK',
			'error' => '200',
			'transaction_id' => $transaction_id,
			'shop_id' => $store_handler->shop_id,
			'callback_data' => $callback_data);
		$log_data['class'] = $class;
		$log_data['orders_id'] = $orders_id;
		switch ($status)
		{
			case 'PAYMENT.SALE.COMPLETED' :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_APPROVED;
				break;

			case 'PAYMENT.SALE.PENDING' :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_PENDING;
				break;

			case 'PAYMENT.AUTHORIZATION.CREATED' :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_CREATED;
				break;

			case 'PAYMENT.SALE.REVERSED' :
			case 'PAYMENT.CAPTURE.REVERSED' :
			case 'PAYMENT.AUTHORIZATION.VOIDED' :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_CANCELED;
				break;

			case 'PAYMENT.SALE.REFUNDED' :
			case 'PAYMENT.CAPTURE.REFUNDED' :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_REFUNDED;
				break;

			default :
				$new_status = XT_PAYPAL_PLUS_ORDER_STATUS_FAILED;
				$log_data = array();
				$log_data['class'] = 'error';
				$log_data['error_data'] = serialize($this->data);
				$log_data['callback_data'] = array('message' => 'UNKNOWN STATUS ['.$status.']', 'error' => $status, 'transaction_id' => $transaction_id, 'shop_id' => $store_handler->shop_id);
				break;

		}
		$this->_addCallbackLog($log_data);

		$order = new order($this->orders_id, $this->customer);

		// XT_PAYPAL_PLUS_SEND_STATUS_MAIL_ON_IPN Kunden bei Bestellstatusänderungen im Shop informieren. Unabhängig davon sind Status-E-Mails von Paypal an den Kunden
		// executepayment sendet keine emails, nur der callback sendet
		if ($send_mail == 1 && XT_PAYPAL_PLUS_SEND_STATUS_MAIL_ON_IPN == 1
			&& $new_status != $order->order_data['orders_status_id'] && $new_status != XT_PAYPAL_PLUS_ORDER_STATUS_FAILED)
		{
			$send_mail = 'true'; // email nur senden, wenn am payment konfiguriert
		}
		else
		{
			$send_mail = 'false';
		}
		$order->_updateOrderStatus($new_status, (isset($this->status_comment)) ? $this->status_comment : '', $send_mail, '', '', $log_data['transaction_id'], $log_data['error_data']);
	}

	/*CheckAccessTokenLivetime
	* Checks if access token is still alive
	* @params $generate_new_session - whether to generate new session is expired
	* @return boolean
	*/
	function CheckAccessTokenLivetime($generate_new_session = true)
	{
	    $t = time();
        //error_log('CheckAccessTokenLivetime   '.$t.' - '.$_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN_TIME'] .' = '. ($t - $_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN_TIME']).'    PPPLUS_EXPIRES_IN => '.$_SESSION['plus_' . $this->customer]['PPPLUS_EXPIRES_IN']  );

        if ($t - $_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN_TIME'] >= $_SESSION['plus_' . $this->customer]['PPPLUS_EXPIRES_IN'])
		{
			$this->unsetPayPalPlusSessions($this->customer);
			if ($generate_new_session)
			{
				$this->generateSecurityToken();
			}
			return false;
		}
		return true;
	}

	/*unsetPayPalPlusSessions
	* unset all sessions generated by paypal plus
	* @params $id - customer ID
	*/
	function unsetPayPalPlusSessions($id)
	{
		unset($_SESSION['plus_' . $id]);
	}

	/*	ApiAUTHCall
    * Do the AUTH call posting JSON encoded data to the endpoint
    * @param $data - array
    * @return cUrl response
    * */
	function ApiAUTHCall($data)
	{
		global $language;

		global $store_handler;
		$uid = 0;
		if (defined('PAYPAL_PLUS_API_LOGGING') && PAYPAL_PLUS_API_LOGGING)
		{
			$uid = uniqid();
			$log_data = array(
				'time' => date("Y-m-d H:i:s"),
				'store_id' => $store_handler->shop_id,
				'data' => $data
			);
			$log_string = 'ApiAUTHCall Request ' . $uid . PHP_EOL . print_r($log_data, true);
			$this->dataLog($log_string);
		}

		$ch = curl_init($this->authCall);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->Client_ID . ':' . $this->Secret_key);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept: application/json',
				'Accept-Language: ' . Plus_lang_mapping($language->code))
		);

		// value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
		// CURL_SSLVERSION_TLSv1_2 (6)
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		// to ensure curls verify functions working in dev:
		// download http://curl.haxx.se/ca/cacert.pem
		// set _CURL_CURLOPT_CAINFO in config.php
		if (defined('_CURL_CURLOPT_CAINFO') && _CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
		{
			curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
		}

		$result = curl_exec($ch);

		if (defined('PAYPAL_PLUS_API_LOGGING') && PAYPAL_PLUS_API_LOGGING == 1)
		{
			$log_data = array(
				'time' => date("Y-m-d H:i:s"),
				'store_id' => $store_handler->shop_id,
				'curl_errno' => curl_errno($ch),
				'result' => $result
			);
			$log_string = 'API Response ' . $uid . PHP_EOL . print_r($log_data, true);
			$this->dataLog($log_string);
		}

		if (curl_errno($ch))
		{
			$log_data = array();
			$log_data['class'] = 'error_ApiAUTHCall';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_msg'] = "Authentification error - " . curl_error($ch) . '-' . curl_errno($ch);
			$this->_addCallbackLog($log_data);
		}
		curl_close($ch);
		return $result;
	}

	/* Do the API call posting JSON encoded data to the endpoint
     * @param $data - array
     * @return cUrl response 
     * */
	function ApiJSONCall($data, $endpoint, $error_message = '', $method = "POST")
	{

		global $store_handler;
		$uid = 0;
		if (defined('PAYPAL_PLUS_API_LOGGING') && PAYPAL_PLUS_API_LOGGING == 1)
		{
			$uid = uniqid();
			$log_data = array(
				'time' => date("Y-m-d H:i:s"),
				'store_id' => $store_handler->shop_id,
				'data' => $data,
				'endpoint' => $endpoint,
				'method' => $method
			);
			$log_string = '-------  API Request ['.trim($error_message).'] ' . $uid . PHP_EOL . print_r($log_data, true);
			$this->dataLog($log_string);
		}

		$ch = curl_init();
		switch ($method)
		{
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
				break;
			case 'PATCH':

				//curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
				break;
			case 'GET':
				$endpoint .= "?" . http_build_query($data);
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Authorization:Bearer ' . $_SESSION['plus_' . $this->customer]['PPPLUS_ACCESS_TOCKEN'],
				'PayPal-Partner-Attribution-Id: ' . $this->PPP_partnerID)
		);

		// value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
		// CURL_SSLVERSION_TLSv1_2 (6)
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		// to ensure curls verify functions working in dev:
		// download http://curl.haxx.se/ca/cacert.pem
		// point _CURL_CURLOPT_CAINFO to cacert.pem, eg in config.php
		if (defined('_CURL_CURLOPT_CAINFO') && _CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
		{
			curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
		}

		$result = curl_exec($ch);
		if (defined('PAYPAL_PLUS_API_LOGGING') && PAYPAL_PLUS_API_LOGGING == 1)
		{
			$log_data = array(
				'time' => date("Y-m-d H:i:s"),
				'store_id' => $store_handler->shop_id,
				'curl_errno' => curl_errno($ch),
				'result' => $result
			);
			$log_string = '-------  API Response ['.trim($error_message).'] ' . $uid . PHP_EOL . print_r($log_data, true);
			$this->dataLog($log_string);
		}

		if (curl_errno($ch))
		{
			$log_data = array();
			$log_data['class'] = 'error_ApiJSONCall';
			$log_data['transaction_id'] = 'time_' . time();
			$log_data['error_data'] = $data;
			$log_data['error_msg'] = $error_message . curl_error($ch) . '-' . curl_errno($ch);
			$this->_addCallbackLog($log_data);
		}
		curl_close($ch);
		return $result;
	}


	function _setOrderId($orders_id)
	{
		$this->orders_id = (int)$orders_id;
	}


	/*setAddress
		Set paypal_plus address 
		@params $cID - customer ID
	*/
	function setAddress($cID = '', $ppp_info)
	{
		if (!$cID)
		{
			return false;
		}
		$payer = $ppp_info->payer;
		$cart_data = $this->_getCartData();
		$p = $cart_data["item_list"]["shipping_address"];

		//if (isset($payer->payer_info->shipping_address->country_code)){
		$data['customers_id'] = $cID;
		$data['customers_firstname'] = $_SESSION['customer']->customer_shipping_address['customers_firstname'];
		$data['customers_lastname'] = $_SESSION['customer']->customer_shipping_address['customers_lastname'];
		$data['customers_street_address'] = (isset($payer->payer_info->shipping_address->line1) ? $payer->payer_info->shipping_address->line1 : $p["line1"]);
        $data['customers_address_addition'] = (isset($payer->payer_info->shipping_address->line2) ? $payer->payer_info->shipping_address->line2 : $p["line2"]);
		$data['customers_postcode'] = (isset($payer->payer_info->shipping_address->postal_code) ? $payer->payer_info->shipping_address->postal_code : $p["postal_code"]);
		$data['customers_city'] = (isset($payer->payer_info->shipping_address->city) ? $payer->payer_info->shipping_address->city : $p["city"]);
		$data['customers_country_code'] = (isset($payer->payer_info->shipping_address->country_code) ? $payer->payer_info->shipping_address->country_code : $p["country_code"]);
		$data['address_class'] = 'xt_paypal_plus';
		$data['PPPLUS_EMAIL'] = $payer->payer_info->email;
		$add_type = $this->checkPayPalAddress($cID);
		$data['address_book_id'] = $add_type['address_book_id'];
		$_SESSION['customer']->_buildCustomerAddressData($data, $add_type['type'], false);
		$this->setPayPalShippingAddress();
		//}
	}

	function checkPayPalAddress($cID)
	{
		global $db;

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=? and address_class='xt_paypal_plus'", array($cID));
		if ($record->RecordCount() == 0)
		{
			$data = array('type' => 'insert', 'address_book_id' => 0);
			return $data;
		}
		else
		{
			$data = array('type' => 'update', 'address_book_id' => $record->fields['address_book_id']);
			return $data;
		}
	}


	function setPayPalShippingAddress()
	{
		$_SESSION['customer']->customer_shipping_address = $_SESSION['customer']->_buildAddressData($_SESSION['customer']->customers_id, 'xt_paypal_plus');
		$_SESSION['customer']->customer_shipping_address['allow_change'] = true;
		$_SESSION['customer']->customer_payment_address['allow_change'] = true;
		$_SESSION['customer']->customer_default_address['allow_change'] = true;
	}

	/*
	function get3rdLevelPayments(){
		global $db, $language, $store_handler,$xtLink,$xtPlugin;
		$payments = array();
		$pay = new payment();
		
		$a_payments = array();
		$a_price = array();
		$pay->_payment();
		$avalibale_price =$pay->payment_data;
		
		foreach($avalibale_price as $k=>$v){
			array_push($a_payments,$k);
			$a_price[$k] = $v['payment_price']['formated'];
		}
		
		$rc  = $db->Execute("SELECT * FROM ". TABLE_PAYMENT ." p 
								INNER JOIN ".TABLE_PAYMENT_DESCRIPTION." pd on p.payment_id = pd.payment_id 
							WHERE p.in_paypal_plus=1 and  p.status=1 and pd.language_code = ? GROUP BY p.payment_id",array($language->code));
		if ($rc->RecordCount()>0){
			while (!$rc->EOF){
				if ($rc->fields['payment_icon']!=''){
					$t = _SYSTEM_BASE_URL . _SRV_WEB._SRV_WEB_PLUGINS.$rc->fields['payment_dir'].'/images/'.$rc->fields['payment_icon'];
					if (!file_exists($t)){
						$rc->fields['payment_icon'] = _SYSTEM_BASE_URL.'/media/payment/'.$rc->fields['payment_icon'];
					}else{
						$rc->fields['payment_icon'] =$t;
					}
				}
				$rc->fields['payment_name'] = str_replace('"',"'",$rc->fields['payment_name']);
				if (isset($a_price[$rc->fields['payment_code']]))
					$rc->fields['payment_name'] = $rc->fields['payment_name'].' ('. $a_price[$rc->fields['payment_code']].')';
				$rc->fields['payment_url'] = $xtLink->_link(array('page'=>'xt_paypal_plus','params'=>'ppp_payment='.$rc->fields['payment_code'].'&ppp_action=confirmation', 'conn'=>'SSL'));
				if (in_array($rc->fields['payment_code'],$a_payments))
					array_push($payments,$rc->fields);
				$rc->MoveNext();
			}
		}

		($plugin_code = $xtPlugin->PluginCode('class.paypal_plus.php:get3rdLevelPayments')) ? eval($plugin_code) : false; 
		return $payments; 
	}
	*/

	function checkSaleID($payment_id, $orders_id)
	{
		global $db;
		$rc = $db->GetOne("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE PPP_PAYMENTID=? AND orders_id=?", array($payment_id, $orders_id));
		return $rc;
	}

	function isPPPavailable()
	{
		if (!array_key_exists('pppIsAvailable', $_REQUEST))
		{
			// set flag for hook class.payment.php:_getPayment_query in class payment
			global $testingIsPPPAvailable, $store_handler, $db;

			$testingIsPPPAvailable = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_PAYMENT." WHERE config_key='XT_PAYPAL_PLUS_ENABLED' and shop_id=?", array($store_handler->shop_id));
			$testingIsPPPAvailable = $testingIsPPPAvailable == 1 ? true : false;

			if ($testingIsPPPAvailable)
			{
			$payment = new payment();
			$payment->_payment();

			// unset flag
			$testingIsPPPAvailable = false;
			unset($GLOBALS['testingIsPPPAvailable']);

				$testingIsPPPAvailable = is_array($payment->payment_data['xt_paypal_plus']) ? true : false;
			}

			$_REQUEST['pppIsAvailable'] = $testingIsPPPAvailable;
		}
		return $_REQUEST['pppIsAvailable'];
	}

	function PPPaditionalPrice()
	{
		global $customers_status, $tax;
		global $testingIsPPPAvailable;
		$testingIsPPPAvailable = true;
		$payment = new payment();
		$payment->_payment();
		$testingIsPPPAvailable = false;

		if (isset($payment->payment_data['xt_paypal_plus']["payment_price"]["plain"])
            && ((float) $payment->payment_data['xt_paypal_plus']["payment_price"]["plain"]) > 0)
		{
		    $already_discounted = 0;

		    if(is_array($_SESSION["cart"]->discount) && $_SESSION["cart"]->discount > 0)
            {
                $already_discounted =  $_SESSION["cart"]->discount;
            }

			$old_selected_payment = $_SESSION['selected_payment'];
			$_SESSION['selected_payment'] = 'xt_paypal_plus';
			$_SESSION['cart']->_refresh();

			if ($payment->payment_data['xt_paypal_plus']['payment_price']['discount'] == 1)
			{
                if($already_discounted > 0)
                {
                    $price = -1 * ($_SESSION['cart']->discount['plain'] - $already_discounted["plain"]);
                }
				else {
                    $price = -1 * $_SESSION['cart']->discount['plain'];
                };
			}
			else
			{
				$price = $payment->payment_data['xt_paypal_plus']["payment_price"]["plain_otax"];
			}

			if ($customers_status->customers_status_add_tax_ot == '1' || $customers_status->customers_status_show_price_tax == '1' )
			{
				$price += $payment->payment_data['xt_paypal_plus']["payment_price"]["plain_otax"] * ($tax->data[$payment->payment_data['xt_paypal_plus']['payment_tax_class']] / 100);
			}

			$_SESSION['selected_payment'] = $old_selected_payment;
			$_SESSION['cart']->_refresh();

			return $price;
		}

		else
		{
			return 0;
		}
	}

	function testConnection()
	{
		$log_file = _SRV_WEBROOT ."xtLogs/ppp_test_connection.txt";
		$curl_log = fopen($log_file, 'w+');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://tlstest.paypal.com/");
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_STDERR, $curl_log);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_SSLVERSION, 6);
		// to ensure curls verify functions working in dev:
		// download http://curl.haxx.se/ca/cacert.pem
		// point _CURL_CURLOPT_CAINFO to cacert.pem, eg in config.php
		if (defined('_CURL_CURLOPT_CAINFO') && _CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
		{
			curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
		}
		$result = curl_exec($ch);

		fclose($curl_log);

		$result_class = 'success';
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code!=200)
		{
			$result_class = 'warn';
		}

		// howmyssl_out
		$ch = curl_init('https://www.howsmyssl.com/a/check');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);

		$json = json_decode($data);
		$howmyssl_out = $json->tls_version;


		// ssl version
		$curl_info = curl_version();
		$ssl_version =  $curl_info['ssl_version'];

		$tpl_data = array(
			'curl_result' => $result,
			'curl_error_no' => curl_errno($ch),
			'curl_http_code' => $http_code,
			'curl_error_msg' => curl_error($ch),
			'result_class' => $result_class,
			'curl_out' => file_get_contents($log_file),
			'howmyssl_out' => $howmyssl_out,
			'sslversion_out' => $ssl_version
		);
        curl_close($ch);

		$tplFile = 'ppp_test_connection.html';
		$template = new Template();
		$template->getTemplatePath($tplFile, 'xt_paypal_plus', 'admin', 'plugin');
		$html = $template->getTemplate('', $tplFile, $tpl_data);

		echo $html;
	}
}
