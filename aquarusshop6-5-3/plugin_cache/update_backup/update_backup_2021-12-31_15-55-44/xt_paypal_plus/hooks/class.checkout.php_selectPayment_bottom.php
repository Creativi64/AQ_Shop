<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db,$xtLink,$language,$page,$xtPlugin,$price;

if (XT_PAYPAL_PLUS_ENABLED && USER_POSITION=='store' && isset($xtPlugin->active_modules['xt_paypal_plus'])){

	unset($_REQUEST['ppp_available_data']);
	include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
	$ppp = new paypal_plus;
	if ( !isset($_SESSION['plus_'.$ppp->customer]['execute_payment_error_detected']) &&
		(isset($_SESSION['selected_shipping']) || $_SESSION['cart']->type == 'virtual') &&
		($page->page_action=='payment' ||  $page->page_action=='confirmation' ) &&
		$ppp->isPPPavailable()  &&
		isset($_SESSION['plus_'.$ppp->customer]['approval_url']) &&
		$_SESSION['disablePPPdue2Browser'] != true )
	{
		if($page->page_action=='payment' )
		{
			//$_SESSION['cart']->_deleteSubContent('payment');
		}

		$template3 = new Template();
		$tpl = 'paypal_plus.html';
		$template3->getTemplatePath($tpl, 'xt_paypal_plus', '', 'plugin');
		$tpl_data['approval_url'] = $_SESSION['plus_'.$ppp->customer]['approval_url'];
		$tpl_data['preselect_paypal'] = (XT_PAYPAL_PLUS_PRESELECT_PAYPAL=="true")?'paypal':'none';
		$tpl_data['language'] = Plus_lang_mapping($language->code);
		$tpl_data['country'] = $_SESSION['customer']->customer_payment_address['customers_country_code'];
		$tpl_data['payment_code'] = 'xt_paypal_plus';
		$tpl_data['ppp_mode'] = XT_PAYPAL_PLUS_MODE;
		$tpl_data['otherPayments'] = array();
		$tpl_data['ppp_button_selector'] = XT_PAYPAL_PLUS_PPP_BUTTON_NEXT_SELECTOR;
		$tpl_data['ppp_box_selector'] = XT_PAYPAL_PLUS_PPP_BOX_SELECTOR;
		//$tpl_data['ppp_comment_selector'] = XT_PAYPAL_PLUS_PPP_COMMENT_SELECTOR;  // removed in 1.1.5
		$tpl_data['ppp_continue_link'] = $xtLink->_link(array('page'=>'checkout/ppp_patch_addresses', 'conn'=>'SSL'));
		$tpl_data['ppp_enable_pui_sandbox'] = XT_PAYPAL_PLUS_SANDBOX_ENABLE_PUI ? 'true' : 'false';
		$fee  = $ppp->PPPaditionalPrice();
		$tpl_data['ppp_additional_fee'] = $fee;
		$tpl_data['ppp_additional_fee_formated'] = $fee != 0 ? $price->_StyleFormat($fee) : 0;
		/*
		$otherPayments = $ppp->get3rdLevelPayments();
        foreach($otherPayments as $k => $p)
        {
            $otherPayments[$k]['payment_desc'] = str_replace("\n", "\\\n", $p['payment_desc']);
            $otherPayments[$k]['payment_email_desc_txt'] = str_replace("\n", "\\\n", $p['payment_email_desc_txt']);
            $otherPayments[$k]['payment_email_desc_html'] = str_replace("\n", "", $p['payment_email_desc_html']);
        }
		*/
		$tpl_data['otherPayments'] = array();//$otherPayments;
		$tpl_data['otherPayments_count'] = count($tpl_data['otherPayments']);
		$tpl_data['additional_price'] = $ppp->PPPaditionalPrice();
		$tpl_data['show'] = 1;

		$baseUrl = $xtLink->_link(array('page'=>'xt_paypal_plus', 'conn' => 'SSL'));
		$tpl_data['baseUrl'] = $baseUrl;

		$tmp_data = $template3->getTemplate('smarty_xt_paypal_plus', $tpl, $tpl_data);
		//unset($data);
		$ppp_set = false;
		foreach($data as $k => &$p)
		{
			if ( (empty($p['payment']) || $k === 'xt_paypal_plus')
				&& !$ppp_set && defined('XT_PAYPAL_PLUS_PPP_TROS') && XT_PAYPAL_PLUS_PPP_TROS==1)
			{
				$p['payment'] = $tmp_data;
				$ppp_set = true;
				continue;
			}
			if (empty($p['payment'])) unset ($data[$k]);
		}
		if (!defined('XT_PAYPAL_PLUS_PPP_TROS') || XT_PAYPAL_PLUS_PPP_TROS!=1)
		{
			$data = array_values($data);
			$data = array_slice($data,0,5,true);
			array_unshift($data, array('payment' => $tmp_data));
		}

		$_REQUEST['ppp_available_data'] = $tpl_data;
	}
	else if ($ppp->isPPPavailable() && isset($_SESSION['plus_'.$ppp->customer]['approval_url']) && $_SESSION['disablePPPdue2Browser'] == true )
	{
		$tplFile = 'paypal_plus_disabledDue2Browser.html';
		$template4 = new Template();
		$template4->getTemplatePath($tplFile, 'xt_paypal_plus', '', 'plugin');
		$tmp_data = $template4->getTemplate('smarty_xt_paypal_plus_disabled', $tplFile, array());
		//unset($data);
		foreach($data as $k => $p)
		{
			if (empty($p['payment'])) unset ($data[$k]);
		}
		if (!defined('XT_PAYPAL_PLUS_PPP_TROS') || XT_PAYPAL_PLUS_PPP_TROS!=1)
		{
			$data = array_values($data);
			//$data = array_slice($data,0,5,true);
			array_unshift($data, array('payment' => $tmp_data));
		}
	}
}
else if(array_key_exists('xt_paypal_plus', $data)){
    unset($data['xt_paypal_plus']);
}

?>