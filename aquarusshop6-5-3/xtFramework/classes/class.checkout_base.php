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

class checkout_base {

    public $shipping_to = ['zones' => [], 'countries' => []];
    public array $shipping_errors = [];

    function __construct(){
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:checkout_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	}

	function _selectShipping(){
		global $xtPlugin, $template,$brotkrumen,$xtLink, $db;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectShipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$contents = $this->_getShipping();

		$content_count = count($contents);

        $data = [];

		if(is_data($contents)){
            foreach($contents as $key => $value) {

				if($content_count==1){
					$value['shipping_hidden'] = true;
				}

				$tpl_data = array();
				$tmp_data = '';
				$tpl_data = $value;

				if(!empty($value['shipping_tpl'])){
					$tpl = $value['shipping_tpl'];
				}else{
					$tpl = 'shipping_default.html';
				}

				$template = new Template();
				$template->getTemplatePath($tpl, $value['shipping_dir'], '', 'shipping');

				($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectShipping_tpl_data')) ? eval($plugin_code) : false;
				$tmp_data = $template->getTemplate($value['shipping_code'].'_shipping_smarty', $tpl, $tpl_data);

				$data[$value['shipping_code']] = array('shipping' => $tmp_data);
			}
		    ($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectShipping_bottom')) ? eval($plugin_code) : false;
		}

        return $data;
	}

	function _getShipping(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_getShipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$shipping = new shipping();
		$shipping->_shipping();
		$shipping_data = $shipping->shipping_data;
        $this->shipping_errors = $shipping->shipping_errors;

        $this->shipping_to = $shipping->shipping_to;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_getShipping')) ? eval($plugin_code) : false;
		return $shipping_data;

	}

	function _setShipping($shipping){
		global $xtPlugin, $db;

		if (!$shipping) return false;
		
		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_setShipping_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$_SESSION['selected_shipping'] = $shipping;

	}

	function _selectPayment(){
		global $xtPlugin, $template,$brotkrumen,$xtLink, $db;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectPayment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$contents = $this->_getPayment();

		$content_count = count($contents);

        $data = [];

		if(is_data($contents)){
            foreach($contents as $key => $value) {

				if($content_count==1){
					$value['payment_hidden'] = true;
				}

				$tpl_data = array();
				$tmp_data = '';
				$tpl_data = $value;
				$tpl_data['payment_country_code']=$_SESSION['customer']->customer_payment_address['customers_country_code'];

				if(!empty($value['payment_tpl'])){
					$tpl = $value['payment_tpl'];
				}else{
					$tpl = 'payment_default.html';
				}

				$template = new Template();

				$template->getTemplatePath($tpl, $value['payment_dir'], '', 'payment');
				
				($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectPayment_tpl_data')) ? eval($plugin_code) : false;
				$tmp_data = $template->getTemplate($value['payment_code'].'_payment_smarty', $tpl, $tpl_data);

				$data[$key] = array('payment' => $tmp_data);
			}
		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_selectPayment_bottom')) ? eval($plugin_code) : false;
		return $data;
		}
	}

	function _getPayment(){
		global $xtPlugin, $db;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_getPayment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$payment = new payment();
		$payment->_payment();
		$payment_data = $payment->payment_data;

		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_getPayment')) ? eval($plugin_code) : false;
		return $payment_data;

	}

	function _setPayment($payment){
		global $xtPlugin, $db;
		
		if (!$payment) return false;
		
		($plugin_code = $xtPlugin->PluginCode('class.checkout.php:_setPayment_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$_SESSION['selected_payment'] = $payment;

	}



    function process_pageAction($action = '')
    {
        global $xtPlugin, $xtLink, $info, $currency, $store_handler, $customers_status, $system_status, $db;

        switch ($action) {
            case 'shipping' :
                $this->process_pageAction_shipping();
                break;

            case 'payment' :
                $this->process_pageAction_payment();
                break;

            case 'process' :
                $this->process_pageAction_process();
                break;

            default:
                $warn = true;
                ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_switch')) ? eval($plugin_code) : false;
                if($warn)
                {
                    global $info;
                    $info->_addInfoSession('Action ['.$_POST['action'].'] not available in this Version of xt:Commerce.');
                    $tmp_link  = $xtLink->_link(array('page'=>'cart', 'conn'=>'SSL'));
                    $xtLink->_redirect($tmp_link);
                }

        }
    }

    protected function process_pageAction_shipping()
    {
        global $xtPlugin, $xtLink, $info, $currency, $store_handler, $customers_status, $system_status;

        $xtLink->_redirect($xtLink->_link(array('page'=>'cart')));
    }

    protected function process_pageAction_payment()
    {
        global $xtPlugin, $xtLink, $info, $currency, $store_handler, $customers_status, $system_status, $db, $filter;

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_payment_top')) ? eval($plugin_code) : false;

        $tmp_payment_data = $this->_getPayment();

        $_payment = $_POST['selected_payment'];
        if (strpos($_payment,':')) {
            $_payments = explode(':',$_payment);
            $_payment = $_payments[0];
            $_payment_sub = $_payments[1];
        }

        $payment_data = $tmp_payment_data[$_payment];

        $payment_class_path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$payment_data['payment_dir'].'/classes/';
        $payment_class_file = 'class.'.$payment_data['payment_code'].'.php';

        if (file_exists($payment_class_path . $payment_class_file)) {
            require_once($payment_class_path.$payment_class_file);
            $payment_module_data = new $payment_data['payment_code']($payment_data);
        }


        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_payment_check')) ? eval($plugin_code) : false;

        if($payment_data['payment_price']['plain_otax']){
            // payment discount ?

            if ($payment_data['payment_price']['discount']==1) {

            } else {

                $payment_data_array = array('customer_id' => $_SESSION['registered_customer'],
                    'qty' => $payment_data['payment_qty'],
                    'name' => $payment_data['payment_name'],
                    'model' => $payment_data['payment_code'],
                    'key_id' => $payment_data['payment_id'],
                    'price' => $payment_data['payment_price']['plain_otax'],
                    'tax_class' => $payment_data['payment_tax_class'],
                    'sort_order' => $payment_data['payment_sort_order'],
                    'type' => $payment_data['payment_type']
                );
            }

        }

        $_SESSION['cart']->_deleteSubContent('payment');
        if(!empty($payment_data_array)){
            ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_payment_data')) ? eval($plugin_code) : false;
            $_SESSION['cart']->_addSubContent($payment_data_array);
        }

        $this->_setPayment($_POST['selected_payment']);

        if(is_data($_POST['conditions_accepted']) && $_POST['conditions_accepted'] == 'on'){
            $_SESSION['conditions_accepted'] = 'true';
        }
        unset($_SESSION['order_comments']);
        if (is_data($_POST['comments'])) {
            $_SESSION['order_comments']=$_POST['comments'];
        }

        $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'confirmation', 'conn'=>'SSL'));
        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_payment_bottom')) ? eval($plugin_code) : false;
        $xtLink->_redirect($tmp_link);
    }

    protected function process_pageAction_process()
    {
        global $xtPlugin, $xtLink, $info, $currency, $store_handler, $customers_status, $system_status, $db;

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_top')) ? eval($plugin_code) : false;

        $_check_error = false;
        if (_STORE_TERMSCOND_CHECK == 'true') {
            if($_POST['conditions_accepted'] != 'on'){
                $_check_error=true;
                $info->_addInfoSession(__text('ERROR_CONDITIONS_ACCEPTED'));
            }
        }

        if (_STORE_DIGITALCOND_CHECK=='true' && ($_SESSION['cart']->type=='virtual' || $_SESSION['cart']->type=='mixed')) {
            if($_POST['withdrawal_reject_accepted'] != 'on'){
                $_check_error=true;
                $info->_addInfoSession(__text('TEXT_DIGITALCOND_CHECK_CHECK_ERROR'));
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_check')) ? eval($plugin_code) : false;

        if ($_check_error==true) {
            $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'confirmation', 'conn'=>'SSL'));
            $xtLink->_redirect($tmp_link);
        }

        $shop_id = $store_handler->shop_id;

        $shipping_code = $_SESSION['selected_shipping'];
        if(empty($shipping_code) && $_SESSION['cart']->type != 'virtual')
        {
            $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'shipping', 'conn'=>'SSL'));
            $xtLink->_redirect($tmp_link);
        }
        $payment_code = $_SESSION['selected_payment'];
        if(empty($payment_code))
        {
            $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));
            $xtLink->_redirect($tmp_link);
        }
        $subpayment_code = '';
        if ($_SESSION['selected_payment_sub']!='') $subpayment_code=$_SESSION['selected_payment_sub'];

        // Shipping
        $tmp_shipping_data = $this->_getShipping();
        $shipping_data = $tmp_shipping_data[$shipping_code];
        $shipping_class_path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$shipping_data['shipping_dir'].'/classes/';
        $shipping_class_file = 'class.'.$shipping_data['shipping_code'].'.php';

        if (file_exists($shipping_class_path . $shipping_class_file)) {
            require_once($shipping_class_path.$shipping_class_file);
            $shipping_module_data = new $shipping_data['shipping_code']();
        }


        // Payment
        $tmp_payment_data = $this->_getPayment();
        $payment_data = $tmp_payment_data[$payment_code];

        $payment_class_path = _SRV_WEBROOT._SRV_WEB_PLUGINS.$payment_data['payment_dir'].'/classes/';
        $payment_class_file = 'class.'.$payment_data['payment_code'].'.php';

        if (file_exists($payment_class_path . $payment_class_file)) {
            require_once($payment_class_path.$payment_class_file);
            $payment_module_data = new $payment_data['payment_code']();
        }
        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_payment_data')) ? eval($plugin_code) : false;

        $currency_code = $currency->code;
        $currency_value = $currency->value_multiplicator;

        $account_type = $_SESSION['customer']->customer_info['account_type'];

        $orders_status = $system_status->_getSingle('order_status', 'default', 'true', 'id');

        $allow_tax = $customers_status->customers_status_show_price_tax;


        if(_SYSTEM_SAVE_IP=='true'){
            if($_SERVER["HTTP_X_FORWARDED_FOR"]){
                $customers_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }else{
                $customers_ip = $_SERVER["REMOTE_ADDR"];
            }
        }

        $comments = '';
        if (isset($_SESSION['order_comments'])) $comments = $_SESSION['order_comments'];

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_pre_data')) ? eval($plugin_code) : false;

        if ($shipping_data['shipping_code']!=$_SESSION['cart']->show_sub_content["shipping"]["products_model"])
        {
            $shipping_data3 = $this->_getShipping();
            $shipping_data2 = $shipping_data3[$_SESSION['selected_shipping']];
            $shipping_data_array = array('customer_id' => $_SESSION['registered_customer'],
                'qty' => $shipping_data2['shipping_qty'],
                'name' => $shipping_data2['shipping_name'],
                'model' => $shipping_data2['shipping_code'],
                'key_id' => $shipping_data2['shipping_id'],
                'price' => $shipping_data2['shipping_price']['plain_otax'],
                'tax_class' => $shipping_data2['shipping_tax_class'],
                'sort_order' => $shipping_data2['shipping_sort_order'],
                'type' => $shipping_data2['shipping_type']
            );
            $_SESSION['cart']->_deleteSubContent($shipping_data_array['type']);
            $_SESSION['cart']->_addSubContent($shipping_data_array);
            $_SESSION['cart']->_refresh();
        }

        $order_data = array('payment_code' => $payment_code,
            'subpayment_code'=>$subpayment_code,
            'shipping_code' => $shipping_code,
            'currency_code' => $currency_code,
            'currency_value' => $currency_value,
            'orders_status' => $orders_status,
            'account_type' => $account_type,
            'allow_tax' => $allow_tax,
            'comments' => $comments,
            'customers_id' => $_SESSION['registered_customer'],
            'shop_id' => $shop_id,
            'customers_ip' => $customers_ip,
            'delivery'=>$_SESSION['customer']->customer_shipping_address,
            'billing'=>$_SESSION['customer']->customer_payment_address
        );

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_process_data')) ? eval($plugin_code) : false;

        $order = new order();

        if($_SESSION['cart']->content_count == 0){
            $url=$xtLink->_link(array('page'=>'cart'));
            $xtLink->_redirect($url);
        }

        if(empty($_SESSION['last_order_id'])){
            $processed_data = $order->_setOrder($order_data, 'complete', 'insert');
            $_SESSION['last_order_id'] = $processed_data['orders_id'];
        }else{
            $processed_data = $order->_setOrder($order_data, 'complete', 'update', $_SESSION['last_order_id']);
        }

        global $order;
        $order = new order($_SESSION['last_order_id'],$_SESSION['customer']->customers_id);

        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_proccess_order_processed')) ? eval($plugin_code) : false;


        /**
         * If payment module is for external PSP redirect to PSP and return to payment_process
         */
        if ($payment_module_data->external == true) {
            $_pspurl = $payment_module_data->pspRedirect($processed_data);
            $xtLink->_redirect($_pspurl);
        }

        /**
         * if payment module is for external PSP with iframe solution
         */
        if ($payment_module_data->iframe == true) {
            $_pspurl = $payment_module_data->IFRAME_URL;
            $xtLink->_redirect($_pspurl);
        }

        /**
         * if payment module is for external PSP with POST form require
         */
        if (isset($payment_module_data->post_form) && $payment_module_data->post_form == true) {
            $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'pay', 'conn'=>'SSL'));
            $xtLink->_redirect($tmp_link);
        }

        $tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'success', 'conn'=>'SSL'));
        ($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_proccess_bottom')) ? eval($plugin_code) : false;
        $_SESSION['success_order_id'] = $_SESSION['last_order_id'];
        $order->_sendOrderMail($_SESSION['last_order_id']);

        unset($_SESSION['last_order_id']);
        unset($_SESSION['selected_shipping']);
        unset($_SESSION['selected_payment']);
        unset($_SESSION['conditions_accepted']);
        unset($_SESSION['order_comments']);
        $_SESSION['cart']->_resetCart();
        $xtLink->_redirect($tmp_link);
    }
}