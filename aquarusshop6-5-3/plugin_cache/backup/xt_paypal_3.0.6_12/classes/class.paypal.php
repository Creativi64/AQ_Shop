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

require_once _SRV_WEBROOT.'plugins/xt_paypal/functions/lang_mapping.php';

class paypal {

	var $API_UserName,
	$API_Password,
	$API_Signature,
	$API_Endpoint,
	$location_error,
	$NOTIFY_URL,
	$EXPRESS_CANCEL_URL,
	$EXPRESS_RETURN_URL,
	$CANCEL_URL,
	$RETURN_URL,
	$EXPRESS_URL,
	$IPN_URL,
	$ppAPIec,
	$ppAPIdp,
	$payPalURL,
	$address_override;

	public static function plugin_version()
    {
        global $db;

	    $plugin_version = $db->GetOne("SELECT version FROM ".TABLE_PLUGIN_PRODUCTS." WHERE `code`='xt_paypal'");
	    if($plugin_version==false) $plugin_version = 'n/a';
	    return $plugin_version;
    }

	var $version = '64.0';

	function __construct() {
		global $xtLink,$xtPlugin;

		$this->BorderColor			= XT_PAYPAL_BORDER_COLOR;
		$this->Logo					= XT_PAYPAL_LOGO;

		if(XT_PAYPAL_MODE_SANDBOX==1){
			$this->API_UserName 	= trim(XT_PAYPAL_API_SANDBOX_USER);
			$this->API_Password 	= trim(XT_PAYPAL_API_SANDBOX_PWD);
			$this->API_Signature	= trim(XT_PAYPAL_API_SANDBOX_SIGNATURE);
			$this->API_Endpoint 	= 'https://api-3t.sandbox.paypal.com/nvp';
			$this->EXPRESS_URL		= 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';
			$this->GIROPAY_URL		= 'https://www.sandbox.paypal.com/webscr?cmd=_complete-express-checkout&token=';
			$this->IPN_URL			= 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
		}elseif(XT_PAYPAL_MODE_SANDBOX==0){
			$this->API_UserName 	= trim(XT_PAYPAL_API_USER);
			$this->API_Password 	= trim(XT_PAYPAL_API_PWD);
			$this->API_Signature	= trim(XT_PAYPAL_API_SIGNATURE);
			$this->API_Endpoint 	= 'https://api-3t.paypal.com/nvp';
			$this->EXPRESS_URL		= 'https://www.paypal.com/webscr?cmd=_express-checkout&token=';
			$this->GIROPAY_URL      = 'https://www.paypal.com/webscr?cmd=_complete-express-checkout&token=';
			$this->IPN_URL			= 'https://ipnpb.paypal.com/cgi-bin/webscr';
		}


        $this->PaymentType='order';
        if (constant('XT_PAYPAL_PAYMENT_TYPE_ORDER')==1) {
            $this->PaymentType='Sale';
        } else {
            $this->PaymentType='order';
        }

		$this->address_override     = 'true';
		$this->RETURN_URL			= $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment_process', 'conn'=>'SSL', ));
		$this->RETURN_EXPRESS_URL	= $xtLink->_link(array('page'=>'checkout', '', 'conn'=>'SSL', 'params'=>'ppexpress=true'));
		$this->CANCEL_EXPRESS_URL  	= $xtLink->_link(array('page'=>'cart'));

		$this->CANCEL_URL  			= $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL' , 'params'=>'paypal_msg=CANCEL_PAYMENT'));
		$this->NOTIFY_URL  			= $xtLink->_link(array('page'=>'callback', 'paction'=>'xt_paypal'));

		$this->GIROPAYSUCCESSURL	= $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment_process', 'conn'=>'SSL', ));
		$this->GIROPAYCANCELURL  	= $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL', 'params'=>'paypal_msg=CANCEL_PAYMENT'));
		$this->BANKTXNPENDINGURL	= $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment_process', 'conn'=>'SSL', ));

		// generate BN IDS depending on software Version
		$this->getSoftwareVersion();

        //for xt_facebook_shop
        // start for xt_facebook_shop url rerwriting
        $urlsToRewrite = array();
        $urlKeys = array('CANCEL_URL', 'RETURN_EXPRESS_URL','CANCEL_EXPRESS_URL');
        foreach($urlKeys as $k)
        {
            $urlsToRewrite[$k] = $this->$k;
        }

        // on return from payment -> facebook -> shop -> intern redirect, we need to preserve url paramters, sometimes ...
        if ($_SESSION['paypalExpressCheckout']==true && $_REQUEST['action'] != 'process')
        {
            $urlsToRewrite['RETURN_URL'] = $this->RETURN_URL;
        }
        else if ($_SESSION['paypalExpressCheckout']!=true && $_REQUEST['action'] == 'process')
        {
            $urlsToRewrite['RETURN_URL'] = array($this->RETURN_URL, true);
        }

        // hook will save rewritten values back to $urlsToRewrite
        ($plugin_code = $xtPlugin->PluginCode('framing:rewriteUrl')) ? eval($plugin_code) : false;

        // reassign rewritten values
        foreach($urlsToRewrite as $urlKey => $url)
        {
            if (is_array($url)) $url=$url[0];
            $this->$urlKey = $url;
        }
        // end for xt_facebook_shop url rerwriting


        ($plugin_code = $xtPlugin->PluginCode('class.paypal.php:__construct_bottom')) ? eval($plugin_code) : false;
	}

	function getSoftwareVersion() {

		$_lic = _SRV_WEBROOT.'lic/license.txt';
		if (!file_exists($_lic)) {
			$this->ppAPIec = 'xtCommerce_Cart_16start';
			$this->ppAPIdp = 'xtCommerce_Cart_16start';
		} else {
			$lic_content = file_get_contents($_lic);
			
			// ultimate
			if (stristr($lic_content, 'ultimate') != FALSE) {
				$this->ppAPIec = 'xtCommerce_Cart_16ultimate_ECM';
				$this->ppAPIdp = 'xtCommerce_Cart_16ultimate_ECS';
				
			} elseif (stristr($lic_content, 'merchant') != FALSE) {
				$this->ppAPIec = 'xtCommerce_Cart_16merchant_ECM';
				$this->ppAPIdp = 'xtCommerce_Cart_16merchant_ECS';
				
			} elseif (stristr($lic_content, 'professional') != FALSE) {
				$this->ppAPIec = 'xtCommerce_Cart_16pro_ECM';
				$this->ppAPIdp = 'xtCommerce16_Cart_pro_ECS';
				
			} elseif (stristr($lic_content, 'community') != FALSE) {
				$this->ppAPIec = 'xtCommerce_Cart_16community_ECM';
				$this->ppAPIdp = 'xtCommerce16_Cart_community_ECS';
            } elseif (stristr($lic_content, 'Start') != FALSE) {
                $this->ppAPIec = 'xtCommerce_Cart_16start';
				$this->ppAPIdp = 'xtCommerce_Cart_16start';
			} else {
				$this->ppAPIec = 'xtCommerce_Cart_16start';
				$this->ppAPIdp = 'xtCommerce_Cart_16start';
			}
		}
	}

	function buildButton($lang=''){

		global $language;

		if($lang==''){
			$lang = $language->code;
		}

		if(constant('XT_PAYPAL_EXPRESS') == 1){
		    if($lang == 'de')
            {
                $params = [
                    'type' => 'p',
                    'img' => "checkout-logo-medium-{$lang}.png",
                    'plg' => 'xt_paypal',
                    'subdir' => "buttons/{$lang}",
                    'path_only' => true,
                    'return' => true
                ];
                $source = image::getImgUrlOrTag($params, $smarty);
            }
            else $source = 'https://www.paypal.com/'._lang_mapping($lang).'/i/btn/btn_xpressCheckout.gif';

            // mobile ?
            if ($_SESSION['isMobile']==true) {
                $source = 'media/payment/paypal_express_mobile_'.$language->code.'.png';
                if (!file_exists(_SRV_WEBROOT.$source)) {
                    $source = 'media/payment/paypal_express_mobile_en.png';
                }
            }

			return $source;
		}
	}

	function buildLink(){
		global $xtLink, $page, $xtPlugin;

		if(constant('XT_PAYPAL_EXPRESS') == 1)
		{
			$params = $xtLink->_getParams();
			if ($params=='') {
				$params='action=paypalExpressCheckout';
			} else {
				$params.='&action=paypalExpressCheckout';
			}

			$link_array = array('page'=>$page->page_name, 'params'=>$params);
			return $xtLink->_link($link_array);
		}
	}


	function _setOrderId($orders_id) {
		$this->orders_id = (int)$orders_id;
	}


	function paypalAuthCall($type=''){
		global $order, $language,$xtPlugin, $store_handler, $db;

		unset($_SESSION['reshash']);
		unset($_SESSION['nvpReqArray']);

		/* The returnURL is the location where buyers return when a
		 payment has been succesfully authorized.
		 The cancelURL is the location buyers are sent to when they hit the
		 cancel button during authorization of payment during the PayPal flow
		 */
		/*
		 if(constant('XT_PAYPAL_EXPRESS') == 1){
			$returnURL =urlencode($this->RETURN_EXPRESS_URL);
			}else{
			$returnURL =urlencode($this->RETURN_URL);
			}
			*/

		$add_override = 0;
		if ($type == 'checkout') $add_override = 1;
		$add = '&ADDROVERRIDE='.$add_override;

		/* Construct the parameter string that describes the PayPal payment
		 the varialbes were set in the web form, and the resulting string
		 is stored in $nvpstr
		 */

		$buyer_data = $this->_getBuyerdata();

		if($type=='checkout'){
			$buyer_data = array_merge($buyer_data,$this->_getOrderData());
			$product_data = $this->getOrderProducts();
			$returnURL =urlencode($this->RETURN_URL);
			$cancelURL =urlencode($this->CANCEL_URL);
		}else{
			$buyer_data = array_merge($buyer_data,$this->_getCartData());
			$product_data = $this->getCartProducts();
			$returnURL =urlencode($this->RETURN_EXPRESS_URL);
			$cancelURL =urlencode($this->CANCEL_EXPRESS_URL);
		}
		foreach ($buyer_data as $key => $val) {
			$buyer_data[$key] = urlencode($val);
		}

		$amt = round($buyer_data['amount'],2);
		if($amt == 0)
        {
            return false;
        }

        $address = '&SHIPTONAME='.$buyer_data['buyer_name'].
                  '&SHIPTOSTREET='.$buyer_data['buyer_street'].
                  '&SHIPTOSTREET2='.$buyer_data['buyer_address_addition'].
                  '&SHIPTOCITY='.$buyer_data['buyer_city'].
                  '&SHIPTOCOUNTRY='.$buyer_data['buyer_country'].
                  '&SHIPTOSTATE='.$buyer_data['buyer_state'].
                  '&SHIPTOZIP='.$buyer_data['buyer_zip'].
                  '&SHIPTOPHONENUM='.$buyer_data['buyer_phone'].'';

		require_once _SRV_WEBROOT.'plugins/xt_paypal/conf/localeconf.php';
		global $_pp_locale;
		$locale = $_pp_locale['default'];

		if(is_object($order)){
			if (isset($_pp_locale[$order->order_data['language_code']]))
			$locale = $_pp_locale[$order->order_data['language_code']];
		}else{
			$locale = $_pp_locale[$language->code];
		}
		 
		$nvpstr="&Amt=".$amt.
                "&PAYMENTACTION=".$this->PaymentType.
                "&ReturnUrl=".$returnURL.
                "&CANCELURL=".$cancelURL . $address .$add.
                "&LocaleCode=".$locale.
                "&CURRENCYCODE=".$buyer_data['currency_code'];

		if($_SESSION['reshash']['REDIRECTREQUIRED']==true){
			$nvpstr.="&GIROPAYSUCCESSURL=".$this->GIROPAYSUCCESSURL.
                	 "&GIROPAYCANCELURL=".$this->GIROPAYCANCELURL.
                	 "&BANKTXNPENDINGURL=".$this->BANKTXNPENDINGURL;				
		}
		
		($plugin_code = $xtPlugin->PluginCode('class.paypal.php:hash_call')) ? eval($plugin_code) : false;
		
		if($this->BorderColor!='')
		$nvpstr.="&CartBorderColor=".$this->BorderColor;

		if($this->Logo!='')
		$nvpstr.="&LogoImg=".$this->Logo;

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
		$nvpstr.="&BRANDNAME=".urlencode($brandName);

		$nvpstr.= $product_data;

		/* Make the call to PayPal to set the Express Checkout token
		 If the API call succeded, then redirect the buyer to PayPal
		 to begin to authorize payment.  If an error occured, show the
		 resulting errors
		 */
		$resArray=$this->hash_call("SetExpressCheckout",$nvpstr);

		$_SESSION['reshash']= $resArray;

		$ack = strtoupper($resArray["ACK"]);

		if($ack=="SUCCESS" || $ack=='SUCCESSWITHWARNING'){
			// Redirect to paypal.com here
			$token = urldecode($resArray["TOKEN"]);
			$this->payPalURL = $this->EXPRESS_URL.''.$token;

			if($ack == 'SUCCESSWITHWARNING')
            {
                $log_data = array();
                $log_data['module'] = 'xt_paypal';
                $log_data['class'] = 'warning';
                $oID = $this->orders_id;
                if (!isset($this->orders_id))
                {
                    $oID = 0;
                }
                $log_data['orders_id'] = $oID;
                $log_data['error_msg'] = 'authCall warning: ' . $resArray['L_ERRORCODE0'] . ' ' . $resArray['L_SHORTMESSAGE0'];;
                $log_data['error_data'] = $resArray . $nvpstr;
                $this->_addCallbackLog($log_data);
            }

			return $this->payPalURL;
		} else  {
				
			$log_data = array();
			$log_data['module'] = 'xt_paypal';
			$log_data['class'] = 'error';
			$oID = $this->orders_id;
			if (!isset($this->orders_id)) $oID = 0;
			$log_data['orders_id'] = $oID;
			$log_data['error_msg'] = 'authCall error: '.$resArray['L_ERRORCODE0'].' '.$resArray['L_SHORTMESSAGE0'];;
			$log_data['error_data'] = $resArray.$nvpstr;
			$this->_addCallbackLog($log_data);
			//$this->_addErrorMessage();
			if($type=='checkout'){
				$this->payPalURL = $this->CANCEL_URL;
			}else{
				$this->payPalURL = $this->CANCEL_EXPRESS_URL;
			}

			$this->_addErrorMessage2($resArray);
				
			return $this->payPalURL;
		}

	}

	function paypalGetCustomerData($type=''){

		$nvpstr="&TOKEN=".$_SESSION['reshash']['TOKEN'];

		/* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
			*/

		$resArray=$this->hash_call("GetExpressCheckoutDetails",$nvpstr);
		 
		if(is_array($resArray) && is_array($_SESSION['reshash']))
		$_SESSION['reshash'] = array_merge($_SESSION['reshash'], $resArray);
		 
		$ack = strtoupper($resArray["ACK"]);

		if($ack=="SUCCESS"){
			$_SESSION['paypalExpressCheckout'] = true;
			$this->checkCustomer();
		}else{
			$log_data = array();
			$log_data['module'] = 'xt_paypal';
			$log_data['class'] = 'error';
			$log_data['orders_id'] = 0;
			$log_data['error_msg'] = 'get customer data: '.$resArray['L_ERRORCODE0'].' '.$resArray['L_SHORTMESSAGE0'];
			$log_data['error_data'] = $resArray;
			$this->_addCallbackLog($log_data);
				
			//$this->_addErrorMessage();
			if($type=='checkout'){
				$this->payPalURL = $this->CANCEL_URL;
			}else{
				$this->payPalURL = $this->CANCEL_EXPRESS_URL;
			}

			$this->_addErrorMessage2($resArray);

			return $this->payPalURL;
		}
	}

	function checkCustomer(){
		global $db, $store_handler, $xtPlugin;
		$_SESSION['pp_address_change'] = true;

		if (!isset ($_SESSION['registered_customer']))
		{
			$sql = "SELECT customers_id FROM " . TABLE_CUSTOMERS . " where customers_email_address = ? and account_type = '0'";
			$sql_ar = array($_SESSION['reshash']['EMAIL']);
			$check_shop_id = true;
			($plugin_code = $xtPlugin->PluginCode('class.paypal.php:checkCustomer_check_shop_id')) ? eval($plugin_code) : false;
			if($check_shop_id)
			{
				$sql .= " AND shop_id=? ";
				$sql_ar[] = $store_handler->shop_id;
			}

			$record = $db->Execute($sql, $sql_ar);

			if($record === false || $record->RecordCount() == 0)
			{
				$customer = $this->createAccount();
				$customers_id = $customer->data_customer_id;
			}
			else
                $customers_id = $record->fields['customers_id'];

            $this->loginCustomer($customers_id);
		}

		if($this->address_override == 'true'){
			$this->setAddress($_SESSION['registered_customer']);
			$this->setPayPalShippingAddress();
		}

		unset($_SESSION['pp_address_change']);
	}

	protected function loginCustomer($cID){
		global $xtPlugin;

		$_SESSION['registered_customer'] = $cID;
		$_SESSION['customer']->_customer($cID);
		//$_SESSION['cart']->_restore();

		($plugin_code = $xtPlugin->PluginCode('paypal:login_customer')) ? eval($plugin_code) : false;
	}

	function setAddress($cID=''){

		if(!$cID) return false;

		$pos 			= strrpos($_SESSION['reshash']['SHIPTONAME'], ' ');
		$lenght 		= strlen($_SESSION['reshash']['SHIPTONAME']);

		$data['customers_id']				= $cID;
		$data['customers_gender']			= '';
		$data['customers_phone']			= $_SESSION['reshash']['PHONENUM'];
		$data['customers_company']			= $_SESSION['reshash']['BUSINESS'];
		$data['customers_firstname']		= substr($_SESSION['reshash']['SHIPTONAME'], 0, $pos);
		$data['customers_lastname']			= substr($_SESSION['reshash']['SHIPTONAME'], ($pos+1), $lenght);
		$data['customers_street_address']	= $_SESSION['reshash']['SHIPTOSTREET'];
        $data['customers_address_addition']	= $_SESSION['reshash']['SHIPTOSTREET2'];
		$data['customers_postcode']			= $_SESSION['reshash']['SHIPTOZIP'];
		$data['customers_city']				= $_SESSION['reshash']['SHIPTOCITY'];
		$data['customers_country_code']		= $_SESSION['reshash']['SHIPTOCOUNTRYCODE'];
		$data['address_class']				= 'paypal_express';

		$add_type = $this->checkPayPalAddress($cID);
		$data['address_book_id'] = $add_type['address_book_id'];
		$_SESSION['customer']->_buildCustomerAddressData($data, $add_type['type'], false);
	}


	function createAccount(){

		$pos 			= strrpos($_SESSION['reshash']['SHIPTONAME'], ' ');
		$lenght 		= strlen($_SESSION['reshash']['SHIPTONAME']);

        // createAccount wird in checkCustomer nur bei express für nicht regestrierte kunden ausgeführt
        // daher guest = 1, also immer erstmal als Gastbenutzer anlegen
        // kunde entscheidet dann auf express-confirmation ob 'echter' account, also mit pwd/anmeldung
		$c_data = [
		    'customers_email_address' => $_SESSION['reshash']['EMAIL'],
            'guest' => 1
        ];

		$_SESSION['customer']->_buildCustomerData($c_data, 'insert', false);

		$data['customers_id']				= $_SESSION['customer']->data_customer_id;
		$data['customers_gender']			= '';
		$data['customers_phone']			= $_SESSION['reshash']['PHONENUM'];
		$data['customers_company']			= $_SESSION['reshash']['BUSINESS'];
		$data['customers_firstname']		= substr($_SESSION['reshash']['SHIPTONAME'], 0, $pos);
		$data['customers_lastname']			= substr($_SESSION['reshash']['SHIPTONAME'], ($pos+1), $lenght);
        $data['customers_street_address']	= $_SESSION['reshash']['SHIPTOSTREET'];
        $data['customers_address_addition']	= $_SESSION['reshash']['SHIPTOSTREET2'];
		$data['customers_postcode']			= $_SESSION['reshash']['SHIPTOZIP'];
		$data['customers_city']				= $_SESSION['reshash']['SHIPTOCITY'];
		$data['customers_country_code']		= $_SESSION['reshash']['SHIPTOCOUNTRYCODE'];
		$data['address_class']				= 'default';

		$_SESSION['customer']->_buildCustomerAddressData($data, 'insert', false);

		if($this->address_override == 'true'){
			$data['address_class']	= 'paypal_express';
			$add_type = $this->checkPayPalAddress($_SESSION['customer']->data_customer_id);
			$data['address_book_id'] = $add_type['address_book_id'];
			$_SESSION['customer']->_buildCustomerAddressData($data, $add_type, false);
		}

		return $_SESSION['customer'];
	}

	function setPayPalShippingAddress()
	{
		$addr = $_SESSION['customer']->_buildAddressData($_SESSION['customer']->customers_id, 'paypal_express');
		$_SESSION['customer']->customer_shipping_address = $addr;
		$_SESSION['customer']->customer_shipping_address['allow_change'] = false;
		$_SESSION['customer']->customer_payment_address = $addr;
		$_SESSION['customer']->customer_payment_address['allow_change'] = false;
		$_SESSION['customer']->customer_default_address['allow_change'] = false;
	}

	function checkPayPalAddress($cID){
		global $db;

		$record = $db->Execute("SELECT * FROM " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id=".$cID." and address_class='paypal_express'");
		if($record->RecordCount() == 0){
			$data = array('type'=>'insert');
			return $data;
		}else{
			$data = array('type'=>'update', 'address_book_id' => $record->fields['address_book_id']);
			return $data;
		}
	}


    /**
     *
     * capture an authorized paypal order
     *
     * @param $orders_id
     *
     */
    function doCaptureRequest($orders_id) {
        global $db;
        // https://www.x.com/developers/paypal/documentation-tools/api/docapture-api-operation-nvp

        /*
         * AuthorizationID
         */

        $query = "SELECT * FROM ".TABLE_ORDERS." WHERE orders_id='".$orders_id."'";
        $rs = $db->GetRow($query);

        if (count($rs)>0) {

            $nvpstr='&AUTHORIZATIONID='.$rs['authorization_id'].
                '&AMT='.round($rs['authorization_amount'],2).
                '&CompleteType=Complete'.
                '&CURRENCYCODE='.$rs['currency_code'].
                '&INVNUM='.$orders_id.
                '&NOTE=';

            $resArray=$this->hash_call("DoCapture",$nvpstr);
            $ack = strtoupper($resArray["ACK"]);

            if($ack!="SUCCESS"){
                $log_data = array();
                $log_data['module'] = $rs['payment_code'];
                $log_data['class'] = 'error';
                $log_data['orders_id'] = $orders_id;
                $log_data['error_msg'] = 'doCaptureRequest - CODE:'.$resArray['L_ERRORCODE0'].' '.$resArray['L_SHORTMESSAGE0'];
                $log_data['error_data'] = serialize($resArray);
                $this->_addCallbackLog($log_data);
                return $resArray['L_LONGMESSAGE0'];
            } else {
                // clear table
                $db->Execute("UPDATE ".TABLE_ORDERS." SET authorization_id='".$this->data['txn_id']."',authorization_amount='0' WHERE orders_id='".$orders_id."'");
                return 'true';

            }
        }
    }

    function doAuthorizationRequest($transaction_id,$amount,$currency) {

		global $info, $xtLink;
        // https://www.x.com/developers/paypal/documentation-tools/api/doauthorization-api-operation-nvp

        $nvpstr='&TRANSACTIONID='.$transaction_id.
            '&TRANSACTIONENTITY=Order'.
            '&AMT='.$amount.
            '&CURRENCYCODE='.$currency;

        $resArray=$this->hash_call("DoAuthorization",$nvpstr);

        $ack = strtoupper($resArray["ACK"]);
        if($ack!="SUCCESS"){
            $log_data = array();
            $log_data['module'] = 'xt_paypal';
            $log_data['class'] = 'error';
            $log_data['orders_id'] = $this->orders_id;
            $log_data['error_msg'] = 'doAuthorizationRequest - CODE:'.$resArray['L_ERRORCODE0'].' '.$resArray['L_SHORTMESSAGE0'];
            $log_data['error_data'] = serialize($resArray);
            $this->_addCallbackLog($log_data);

			if($resArray['L_ERRORCODE0'] == 10486)
			{
				// Redirect to paypal.com again
				$url = $this->EXPRESS_URL.'&orders_id='.$this->orders_id;
				$xtLink->_redirect($url);
			}

			$this->_addErrorMessage2($resArray);
        }
    }

	function completeStandardCheckout($type = ''){
		global $order,$filter,$xtLink, $info;

		// totals
		$buyer_data = array();
		$buyer_data['token'] =  $_SESSION['reshash']['TOKEN'];

		if($_SESSION['reshash']['PAYERID'])
		$buyer_data['payer_id'] =  $_SESSION['reshash']['PAYERID'];
		else
		$buyer_data['payer_id'] = $_GET['PayerID'];

		$buyer_data['button_source'] = $this->ppAPIec;

		$buyer_data = array_merge($buyer_data,$this->_getBuyerdata());
		$buyer_data = array_merge($buyer_data,$this->_getOrderData());
		
		foreach ($buyer_data as $key => $val) {
			$buyer_data[$key] = urlencode($val);
		}
		
		$product_data = $this->getOrderProducts();

		$address = '&SHIPTONAME='.$buyer_data['buyer_name'].
				   '&SHIPTOSTREET='.$buyer_data['buyer_street'].
                   '&SHIPTOSTREET2='.$buyer_data['buyer_address_addition'].
				   '&SHIPTOCITY='.$buyer_data['buyer_city'].
				   '&SHIPTOCOUNTRY='.$buyer_data['buyer_country'].
				   '&SHIPTOSTATE='.$buyer_data['buyer_state'].
				   '&SHIPTOZIP='.$buyer_data['buyer_zip'].
				   '&SHIPTOPHONENUM='.$buyer_data['buyer_phone'].'';

		$nvpstr='&TOKEN='.$buyer_data['token'].
				'&PayerID='.$buyer_data['payer_id'].
				'&PAYMENTACTION='.$this->PaymentType.
				'&AMT='.round($buyer_data['amount'],2).
				'&CURRENCYCODE='.$buyer_data['currency_code'].
				'&IPADDRESS='.$buyer_data['buyer_ip'].
				'&NOTIFYURL='.$buyer_data['notify_url'].
				'&INVNUM='.$buyer_data['invoice_id'].
		        $address.
				'&BUTTONSOURCE='.$buyer_data['button_source'];


		if($_SESSION['reshash']['REDIRECTREQUIRED']==true){
			$nvpstr.="&GIROPAYSUCCESSURL=".$this->GIROPAYSUCCESSURL.
                	 "&GIROPAYCANCELURL=".$this->GIROPAYCANCELURL.
                	 "&BANKTXNPENDINGURL=".$this->BANKTXNPENDINGURL;				
		}

		$nvpstr.= $product_data;

		//__debug($nvpstr, 'PARAMS');
		//die;
		$resArray=$this->hash_call("DoExpressCheckoutPayment",$nvpstr);

		$_SESSION['reshash'] = array_merge($_SESSION['reshash'], $resArray) ;
		$ack = strtoupper($resArray["ACK"]);

		//__debug($_SESSION, 'SESSION');
		//die;

		unset($_SESSION['expressButtonCheckout']); // could be set to true in $this->pspSuccess

		if($ack!="SUCCESS"){
			$log_data = array();
			$log_data['module'] = 'xt_paypal';
			$log_data['class'] = 'error2';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['error_msg'] = 'complete checkout error: '.$resArray['L_ERRORCODE0'].' '.$resArray['L_SHORTMESSAGE0'];
			$log_data['error_data'] = serialize($resArray);
			$this->_addCallbackLog($log_data);

			if($resArray['L_ERRORCODE0'] == 10486)
			{
				// Redirect to paypal.com again
				$token = urldecode($_SESSION['reshash']["TOKEN"]);
				$url = $this->EXPRESS_URL.''.$token;
				$xtLink->_redirect($url);
			}

			$this->_addErrorMessage2($resArray);

			if($type=='checkout'){
				$this->payPalURL = $this->CANCEL_URL;
			}else{
				$this->payPalURL = $this->CANCEL_EXPRESS_URL;
			}
				
			return $this->payPalURL;
		}else if ($this->PaymentType=='order'){
            // do we need to authorize ?
            $this->doAuthorizationRequest($resArray['PAYMENTINFO_0_TRANSACTIONID'],$resArray['PAYMENTINFO_0_AMT'],$resArray['PAYMENTINFO_0_CURRENCYCODE']);
        }

		return true;
	}


	/**
	 * hash_call: Function to perform the API call to PayPal using API signature
	 * @methodName is name of API  method.
	 * @nvpStr is nvp string.
	 * returns an associtive array containing the response from the server.
	 */
	function hash_call($methodName,$nvpStr,$pp_token='')
	{
		//declaring of global variables
		//global $API_Endpoint,$version,$API_UserName,$API_Password,$API_Signature,$nvp_Header;

		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->API_Endpoint.$pp_token);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$config = include _SRV_WEBROOT . "plugins/xt_paypal/conf/curl_config.php";
        curl_setopt($ch, CURLOPT_SSLVERSION, $config["SSL_VERSION"] );
        if (!empty($config["CIPHER_LIST"]))
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, $config["CIPHER_LIST"]);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
		if($this->USE_PROXY)
		curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);


		//NVPRequest for submitting to server
		$nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($this->version)."&PWD=".urlencode($this->API_Password)."&USER=".urlencode($this->API_UserName)."&SIGNATURE=".urlencode($this->API_Signature).$nvpStr;

		$this->dataLog('----- '.date('Y-m-d H:i:s'). '  '.$methodName);

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

		//getting response from server
		$response = curl_exec($ch);

		//convrting NVPResponse to an Associative Array
		$nvpResArray=$this->deformatNVP($response);
		$nvpReqArray=$this->deformatNVP($nvpreq);

        $this->dataLog('nvpReq => '.print_r($nvpReqArray,true));
        $this->dataLog('nvpRes => '.print_r($nvpResArray,true));

		$_SESSION['nvpReqArray']= $nvpReqArray;

		if (curl_errno($ch)) {
			// moving to display page to display curl errors
			$_SESSION['curl_error_no']=curl_errno($ch);
			$_SESSION['curl_error_msg']=curl_error($ch);
			curl_close($ch);
			$response = array_merge($response,array('curl_error'=>$_SESSION['curl_error_no'],'curl_error_no'=>$_SESSION['curl_error_msg']));
			$log_data = array();
			$log_data['module'] = 'xt_paypal';
			$log_data['class'] = 'error3';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['error_msg'] = $_SESSION['curl_error_msg'];
			$log_data['error_data'] = $response;
			$this->_addCallbackLog($log_data);
		} else {
			//closing the curl
			curl_close($ch);
		}

		return $nvpResArray;
	}


	/**
	 * Get buyer addressdata from order
	 *
	 * @return array addressdata
	 */
	function _getBuyerdata() {
		global $order;
		$buyer_data = array();
		$buyer_data['buyer_name'] = ($order->order_data['delivery_firstname'].' '. $order->order_data['delivery_lastname']);
		$buyer_data['buyer_street'] = ($order->order_data['delivery_street_address']);
		$buyer_data['buyer_address_addition'] = ($order->order_data['delivery_address_addition']);
		$buyer_data['buyer_city'] =  ($order->order_data['delivery_city']);
		$buyer_data['buyer_zip'] =  ($order->order_data['delivery_postcode']);
		$buyer_data['buyer_state'] =  ($order->order_data['delivery_federal_state_code_iso']);
		$buyer_data['buyer_country'] =  ($order->order_data['delivery_country_code']);
		$buyer_data['buyer_phone'] =  ($order->order_data['delivery_phone']);
		if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
			$buyer_data['buyer_ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			$buyer_data['buyer_ip'] = $_SERVER["REMOTE_ADDR"];
		}
		return $buyer_data;
	}

	/**
	 * Get order informations
	 *
	 * @return unknown
	 */
	function _getOrderData() {
		global $order;

		$buyer_data=array();
		$buyer_data['amount'] = $order->order_total['total']['plain'];
		$buyer_data['amount'] = str_replace(',','.',$buyer_data['amount']);
		$buyer_data['currency_code'] = $order->order_data['currency_code'];
		$buyer_data['server_name'] = $_SERVER['SERVER_NAME'];
		$buyer_data['notify_url']  = $this->NOTIFY_URL;
		$buyer_data['invoice_id'] = $order->oID;
		$buyer_data['amount_item'] = $order->order_total['product_total']['plain'];
		$buyer_data['amount_item'] = str_replace(',','.',$buyer_data['amount_item']);
		$total_tax = 0;
		if (is_array($order->order_total['total_tax'])) {
			foreach ($order->order_total['total_tax'] as $key => $arr) $total_tax += $arr['tax_value']['plain'];
		}
		$buyer_data['amount_tax'] = $total_tax;
		$buyer_data['amount_tax'] = round($buyer_data['amount_tax'],2);
		$buyer_data['amount_tax'] = str_replace(',','.',$buyer_data['amount_tax']);

		$total_shipping = 0;
		if(is_array($order->order_total_data)){
			foreach ($order->order_total_data as $key => $arr) {
				if ($arr['orders_total_key']=='shipping') $total_shipping+=$arr['orders_total_price']['plain'];
			}
		}
		$buyer_data['ammount_shipping'] = $total_shipping;
		$buyer_data['ammount_shipping'] = round($buyer_data['ammount_shipping'],2);
		$buyer_data['ammount_shipping'] = str_replace(',','.',$buyer_data['ammount_shipping']);

		return $buyer_data;

	}

	function getOrderProducts(){
		global $order, $customers_status;

		// products
		$i = 0;
		$tmp_products = '';
		$itmamt = $itmtax = 0;
		foreach ($order->order_products as $key => $arr) {
			if($customers_status->customers_status_show_price_tax == '1'){
				$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
								 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
								 '&L_QTY'.$i.'='.$arr['products_quantity'].
								 '&L_AMT'.$i.'='. round($arr['products_price']['plain'],2);
				$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain'],2));
				$itmtax = 0;
			}
			else{
				$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
											 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
											 '&L_QTY'.$i.'='.$arr['products_quantity'].
											 '&L_AMT'.$i.'='. round($arr['products_price']['plain_otax'],2);
				$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain_otax'],2));
				if($customers_status->customers_status_add_tax_ot=='1'){
					$itmtax += ($arr['products_quantity']*round($arr['products_tax']['plain'],2));
				}else{
					$itmtax = 0;
				}
			}
			$i++;
		}

		// Other like shipping etc
		if(is_array($order->order_total_data)){
			foreach ($order->order_total_data as $key => $arr) {
				if($customers_status->customers_status_show_price_tax == '1'){
					$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['orders_total_name']).
									 '&L_NUMBER'.$i.'='.urlencode($arr['orders_total_model']).
									 '&L_QTY'.$i.'='.$arr['orders_total_quantity'].
									 '&L_AMT'.$i.'='. round($arr['orders_total_price']['plain'],2);

					$itmamt += ($arr['orders_total_quantity']*round($arr['orders_total_price']['plain'],2));
					$itmtax = 0;
				}else{
					$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['orders_total_name']).
														 '&L_NUMBER'.$i.'='.urlencode($arr['orders_total_model']).
														 '&L_QTY'.$i.'='.$arr['orders_total_quantity'].
														 '&L_AMT'.$i.'='. round($arr['orders_total_price']['plain_otax'],2);
						
					$itmamt += ($arr['orders_total_quantity']*round($arr['orders_total_price']['plain_otax'],2));
					if($customers_status->customers_status_add_tax_ot=='1'){
						$itmtax += ($arr['orders_total_quantity']*round($arr['orders_total_tax']['plain'],2));
					}else{
						$itmtax = 0;
					}
				}
				$i++;
			}
		}

		$handlingamt  = round($order->order_total['total']['plain'],2)  - round($itmamt,2) - round($itmtax,2);

		if(round($handlingamt,2) != 0 && XT_PAYPAL_CORRECTION == 'true'){
			$tmp_products .= '&L_NAME'.$i.'='.TEXT_PAYPAL_PAYMENT_CORRECTION_PRODUCT_NAME.
											 '&L_NUMBER'.$i.'='.TEXT_PAYPAL_PAYMENT_CORRECTION_PRODUCT_NUMBER.
											 '&L_QTY'.$i.'=1'.
											 '&L_AMT'.$i.'='. round($handlingamt,2);
			$itmamt += $handlingamt;
		}

		$products = $tmp_products.'&ITEMAMT='.round($itmamt,2).'&TAXAMT='.round($itmtax,2);

		return $products;
	}

	function getCartProducts(){
		global $customers_status;

		// products
		$i = 0;
		$tmp_products = '';
		$itmamt = $itmtax = 0;
		foreach ($_SESSION['cart']->show_content as $key => $arr) {
			if($customers_status->customers_status_show_price_tax == '1'){
				$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
								 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
								 '&L_QTY'.$i.'='.$arr['products_quantity'].
								 '&L_AMT'.$i.'='. round($arr['products_price']['plain'],2);

				$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain'],2));
				$itmtax = 0;
			}else{
				$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
												 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
												 '&L_QTY'.$i.'='.$arr['products_quantity'].
												 '&L_AMT'.$i.'='. round($arr['products_price']['plain_otax'],2);

				$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain_otax'],2));
				if($customers_status->customers_status_add_tax_ot=='1'){
					$itmtax += ($arr['products_quantity']*$arr['products_tax']['plain']);
				}else{
					$itmtax = 0;
				}
			}
			$i++;
		}

		// Other like shipping etc
		if(is_array($_SESSION['cart']->show_sub_content)){
			foreach ($_SESSION['cart']->show_sub_content as $key => $arr) {
				if($customers_status->customers_status_show_price_tax == '1'){
					$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
									 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
									 '&L_QTY'.$i.'='.$arr['products_quantity'].
									 '&L_AMT'.$i.'='. round($arr['products_price']['plain'],2);

					$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain'],2));
					$itmtax = 0;
				}else{
					$tmp_products .= '&L_NAME'.$i.'='.urlencode($arr['products_name']).
														 '&L_NUMBER'.$i.'='.urlencode($arr['products_model']).
														 '&L_QTY'.$i.'='.$arr['products_quantity'].
														 '&L_AMT'.$i.'='. round($arr['products_price']['plain_otax'],2);
						
					$itmamt += ($arr['products_quantity']*round($arr['products_price']['plain_otax'],2));
					if($customers_status->customers_status_add_tax_ot=='1'){
						$itmtax += ($arr['products_quantity']*$arr['products_tax']['plain']);
					}else{

						$itmtax = 0;
					}
				}

				$i++;
			}
		}

		$handlingamt  = $_SESSION['cart']->total['plain']  - round($itmamt,2) - round($itmtax,2);

		if(round($handlingamt,2) != 0 && XT_PAYPAL_CORRECTION == 'true'){
			$tmp_products .= '&L_NAME'.$i.'='.TEXT_PAYPAL_PAYMENT_CORRECTION_PRODUCT_NAME.
											 '&L_NUMBER'.$i.'='.TEXT_PAYPAL_PAYMENT_CORRECTION_PRODUCT_NUMBER.
											 '&L_QTY'.$i.'=1'.
											 '&L_AMT'.$i.'='. round($handlingamt,2);
			$itmamt += $handlingamt;
		}

		$products = $tmp_products.'&ITEMAMT='.round($itmamt,2).'&TAXAMT='.round($itmtax,2);


		return $products;
	}

	/**
	 * Get cart informations
	 *
	 * @return unknown
	 */
	function _getCartData() {
		global $currency;

		$buyer_data=array();
		$buyer_data['amount'] = $_SESSION['cart']->total['plain'];
		$buyer_data['amount'] = str_replace(',','.',$buyer_data['amount']);
		$buyer_data['currency_code'] = $currency->code;
		$buyer_data['server_name'] = $_SERVER['SERVER_NAME'];
		$buyer_data['notify_url']  = $this->NOTIFY_URL;
		$buyer_data['invoice_id'] = 0;
		$buyer_data['amount_item'] = $_SESSION['cart']->content_total['plain'];
		$buyer_data['amount_item'] = str_replace(',','.',$buyer_data['amount_item']);
		$total_tax = 0;
		if (is_array($_SESSION['cart']->tax)) {
			foreach ($_SESSION['cart']->tax as $key => $arr) $total_tax += $arr['tax_value']['plain'];
		}
		$buyer_data['amount_tax'] = $total_tax;
		$buyer_data['amount_tax'] = round($buyer_data['amount_tax'],2);
		$buyer_data['amount_tax'] = str_replace(',','.',$buyer_data['amount_tax']);

		$total_shipping = 0;
		$buyer_data['ammount_shipping'] = $total_shipping;
		$buyer_data['ammount_shipping'] = round($buyer_data['ammount_shipping'],2);
		$buyer_data['ammount_shipping'] = str_replace(',','.',$buyer_data['ammount_shipping']);

		return $buyer_data;

	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 * It is usefull to search for a particular key and displaying arrays.
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 */
	function deformatNVP($nvpstr)
	{

		$intial=0;
		$nvpArray = array();


		while(strlen($nvpstr)){

			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
		}
		return $nvpArray;
	}


	function buildAPIKey($key, $pay){
		$key_arr=explode(',',$key);
		$k='';
		for ($i=0; $i<count($key_arr);$i++) $k.=chr($key_arr[$i]);
		if($pay=='ec'){
			return $k.'EC_AT_31';
		}elseif($pay=='dp'){
			return $k.'DP_AT_31';
		}
	}

	function _addCallbackLog($log_data) {
		global $db;
		if (is_array($log_data['callback_data'])) $log_data['callback_data'] = serialize($log_data['callback_data']);
		if (is_array($log_data['error_data'])) $log_data['error_data'] = serialize($log_data['error_data']);
		if ($log_data['error_data']=='') $log_data['error_data']='';
		//$log_data['created'] =  $db->DBTimeStamp(time());
		$db->AutoExecute(TABLE_CALLBACK_LOG,$log_data,'INSERT');
	}

	function _addErrorMessage2($data){
		global $info;

		$pos = array();
		foreach($data as $key=>$val)
		{
			$pos = strpos($key, 'L_ERRORCODE');
			if($pos !== false)
			{
				$exp = explode('L_ERRORCODE',$key);
				if(!empty($exp[1])) $pos[]  = $exp[1];
			}

		}
		foreach ($pos as $i){
			$info->_addInfo('ERROR CODE: '.$data['L_ERRORCODE'.$i].'<br />ERROR MESSAGE: '.$data['L_LONGMESSAGE'.$i], 'error');
		}

		return $info->info_content;
	}

	function _addErrorMessage(){
		global $info;

		if($_SESSION['reshash']['ACK']=='Failure'){
			$count = 0;
			foreach($_SESSION['reshash'] as $key=>$val){

				$pos = strpos($key, 'L_ERRORCODE');
				if($pos !== false){
					$count  = $count+1;
				}

			}
			for ($i=0; $i<$count;$i++){
				$info->_addInfo('ERROR CODE: '.$_SESSION['reshash']['L_ERRORCODE'.$i].'<br />ERROR MESSAGE: '.$_SESSION['reshash']['L_LONGMESSAGE'.$i], 'error');
			}
		}

		//unset($_SESSION['reshash']);
		//unset($_SESSION['nvpReqArray']);

		return $info->info_content;
	}

	function filterPayments($data){

		if($data['payment_code']=='xt_paypal'){
			$new_data = $data;
		}

		return $new_data;
	}

    function dataLog($data_string, $force = false)
    {
        if($force == false && (!defined('XT_PAYPAL_DEBUG') || constant('XT_PAYPAL_DEBUG') != true)) return;
        $f=fopen(_SRV_WEBROOT.'xtLogs/paypal.log', 'a+');
        if($f)
        {
            $data_string = preg_replace('/PWD=.*/', 'PWD=***', $data_string);
            $data_string = preg_replace('/\[PWD\] =>.*/', '[PWD] => ***', $data_string);
            $data_string = preg_replace('/SIGNATURE=.*/', 'SIGNATURE=***', $data_string);
            $data_string = preg_replace('/\[SIGNATURE\] =>.*/', '[SIGNATURE] => ***', $data_string);
            fwrite($f, date('Y-m-d H:i:s') ."\n". $data_string ."\n");
            fclose($f);
        }
    }
}
