<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($_SESSION['customer']->customers_id && $_SESSION['selected_payment']=='xt_paypal_plus' && isset($xtPlugin->active_modules['xt_paypal_plus']))
{
	include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
	$ppp = new paypal_plus;
	if ($ppp->isPPPavailable() && $ppp->CheckAccessTokenLivetime(false) && (isset($order))){
		try {
			$ppp->setOrderId($_SESSION['last_order_id']);
			$success = $ppp->executePayment($order);
		}
		catch (Exception $ex)
		{
			$_SESSION['plus_'.$ppp->customer]['execute_payment_error_detected'] = true;
			unset($_SESSION['plus_'.$ppp->customer]['approval_url']);
			global $info;
			$info->_addInfoSession(TEXT_PPP_PAYMENT_ERROR.'<br />'.$ex->getMessage());
			$tmp_link  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));
			$xtLink->_redirect($tmp_link);

		}
	}
}
else if($_SESSION['customer']->customers_id && $_SESSION['selected_payment']!='xt_paypal_plus')
{
	unset($_SESSION['plus_'.$ppp->customer]['execute_payment_error_detected']);
}
