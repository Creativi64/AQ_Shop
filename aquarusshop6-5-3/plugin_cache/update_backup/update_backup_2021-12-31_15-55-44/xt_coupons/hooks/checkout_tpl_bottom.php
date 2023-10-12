<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$allow_on_checkout_page = array();

($plugin_code = $xtPlugin->PluginCode('coupons_hook_checkout_tpl_bottom:allow_on_checkout_page')) ? eval($plugin_code) : false;

if (XT_COUPONS_CHECKOUT_PAGE == 1 && count($allow_on_checkout_page) == 0)
{
	if ( ! isset($xtPlugin->active_modules['xt_socialcommerce']) || (isset($xtPlugin->active_modules['xt_socialcommerce']) && ! $_SESSION['xt_viral_campaigns_users_activities']->getCampaignsActivities()))
	{
		$tpl_data = array('message' => 'Test Message');
		if ( ! empty($_SESSION['sess_coupon']) && is_array($_SESSION['sess_coupon']))
		{
			$tpl_data['arr_coupon'] = $_SESSION['sess_coupon'];

			if ( ! empty($_SESSION['sess_coupon']['coupon_token_code'])) $key = 'coupon_token_code';
			else $key = empty($_SESSION['sess_coupon']['coupon_code']) ? null : 'coupon_code';

			if (isset($key))
			{
				global $xtLink;

				$coupon_hash = md5($_SESSION['sess_coupon'][$key]);
				$tpl_data['remove_coupon_link'] = $xtLink->_link(array('page' => 'checkout', 'paction' => 'confirmation', 'params' => 'remove_coupon='.$coupon_hash));
			}
		}

		$tpl = 'coupons_form.html';

		$plugin_template = new Template();
		$plugin_template->getTemplatePath($tpl, 'xt_coupons', '', 'plugin');
		echo ($plugin_template->getTemplate('', $tpl, $tpl_data));
	}
}