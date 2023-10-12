<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtPlugin, $page, $xtLink;

include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
$ppp = new paypal_plus();
$pppAvailable = $ppp->isPPPavailable();

if(XT_PAYPAL_PLUS_ENABLED && strtoupper($page->page_action)=='PPP_PATCH_ADDRESSES')
{
	if ($pppAvailable)
	{
		$ppp->patchAddresses();

		/*
		$tplFile = 'paypal_plus_patch_addresses.html';
		$template5 = new Template();
		$template5->getTemplatePath($tplFile, 'xt_paypal_plus', '', 'plugin');
		$html = $template5->getTemplate('smarty_xt_paypal_plus_patch_addresses', $tplFile, array());
		echo $html;
		*/

		header('Location: '.$xtLink->_link(array('default_page'=> _SRV_WEB_PLUGINS.'xt_paypal_plus/templates/paypal_plus_patch_addresses.html')));
		exit();
	}
	else {
		global $xtLink;
		$tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));
		$xtLink->_redirect($tmp_link);
	}
}

if ($pppAvailable && XT_PAYPAL_PLUS_ENABLED && $page->page_action == 'payment'){
	//unset($_SESSION['selected_payment']);
	//$_SESSION['cart']->_refresh();
}

if ($pppAvailable&& XT_PAYPAL_PLUS_ENABLED && $page->page_action == 'shipping'){
    unset($_SESSION['disablePPPdue2Browser']);
}
else if ($pppAvailable && XT_PAYPAL_PLUS_ENABLED)
{
    $preg_res = preg_match('/MSIE\s(?P<v>\d+)/i', $_SERVER['HTTP_USER_AGENT'], $matches);
    if($preg_res === 1 && array_key_exists('v', $matches) && (int) $matches['v'] < 10)
    {
        $_SESSION['disablePPPdue2Browser'] = true;
    }
}

if ($pppAvailable && XT_PAYPAL_PLUS_ENABLED && $_SESSION['customer']->customers_id && USER_POSITION=='store' && isset($xtPlugin->active_modules['xt_paypal_plus']))
{
	include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
	$ppp = new paypal_plus;
	if ($ppp->isPPPavailable()){
		if($page->page_action=='shipping' || empty($_SESSION['selected_payment']))
		{
			unset($_SESSION['plus_'.$ppp->customer]['approval_url']);
		}
		if (isset($_SESSION['plus_'.$ppp->customer]['PPPLUS_ACCESS_TOCKEN'])){
			$ppp->CheckAccessTokenLivetime();
		}else{
			$ppp->generateSecurityToken();
		}

		$plus_customer = $_SESSION['customer']->_buildAddressData($ppp->customer,'xt_paypal_plus');

		if (!isset($_SESSION['plus_'.$ppp->customer]['PPPLUS_USER_PROFILE_ID'])){
			$ppp->generateUserProfile();
		}

		if (($page->page_action=='payment' || ($page->page_action=='confirmation' && $_SESSION['selected_payment']=='xt_paypal_plus')) && (isset($_SESSION['plus_'.$ppp->customer]['PPPLUS_USER_PROFILE_ID']))){
			if (!isset($_SESSION['plus_'.$ppp->customer]['approval_url']))
			{
				$ppp->createPayment();
			}
		}

		if ($_SESSION['selected_payment']=='xt_paypal_plus' && $page->page_action=='confirmation' && (isset($_SESSION['plus_'.$ppp->customer]['PPPLUS_USER_PROFILE_ID'])))
		{
			$checkout = new checkout();
			$checkout->_setPayment('xt_paypal_plus');
			$tmp_payment_data = $checkout->_getPayment();
			$payment_data = $tmp_payment_data['xt_paypal_plus'];
			if ($payment_data['payment_price']['discount']!=1){
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
				$_SESSION['cart']->_deleteSubContent('payment');
				$_SESSION['cart']->_addSubContent($payment_data_array);
				$_SESSION['cart']->_refresh();
			}
			if($payment_data['payment_price']['plain_otax']<=0){
				$_SESSION['cart']->_deleteSubContent('payment');
				$_SESSION['cart']->_refresh();
			}


		}

		if (($page->page_action=='confirmation') && (!isset($_SESSION['selected_payment']))){
			global $xtLink;
			$tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));
			$xtLink->_redirect($tmp_link);
		}
	}

}
