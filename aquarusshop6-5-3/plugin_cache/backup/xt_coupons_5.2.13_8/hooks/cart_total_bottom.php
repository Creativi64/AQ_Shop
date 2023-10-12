<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ( ! empty($_SESSION['sess_coupon']) && is_array($_SESSION['sess_coupon'])) {

	if ( ! empty($_SESSION['sess_coupon']['coupon_token_code'])) $key = 'coupon_token_code';
	else $key = empty($_SESSION['sess_coupon']['coupon_code']) ? null : 'coupon_code';

	if (isset($key))
	{
		global $xtLink,$currency, $price;

		$coupon_hash = md5($_SESSION['sess_coupon'][$key]);
		/*
		echo '<br /><b>'.TEXT_COUPON_AKTIV.': '.$_SESSION['sess_coupon'][$key].'</b>'
			.'<br /><a class="float-right underline" href="'
			.$xtLink->_link(array('page' => 'cart', 'params' => 'remove_coupon='.$coupon_hash)).'">'.TEXT_COUPON_REMOVE.'</a>';
		$show_field = false;
		*/
		if(XT_COUPONS_LOGIN != 1){
			$show_field = true;
		}elseif($_SESSION['registered_customer']){
			$show_field = true;
		}

		if($show_field == true){
			
	        $tpl = 'coupons_cart_total_bottom.html';
			$tpl_data['arr_coupon'] = $_SESSION['sess_coupon'];


            if($tpl_data['arr_coupon']['coupon_tax_class'] > 0)
            {
                global $tax;
                $tpl_data['arr_coupon']['coupon_amount'] = $price->_AddTax($tpl_data['arr_coupon']['coupon_amount'], $tax->data[$tpl_data['arr_coupon']['coupon_tax_class']]);
            }

			$new_amount = round(floatval($_SESSION['cart']->content_total['plain']) -  floatval($tpl_data['arr_coupon']['coupon_amount']), 2);
			if($new_amount<0)
			{
				$new_amount = 0; // safe, cause view data only
			}

            if ($tpl_data['arr_coupon']["coupon_amount"]>0)
            {   // 4tfm tax fix BWV-987-81997
                global $tax;
                $tmp_amount = $tpl_data['arr_coupon']["coupon_amount"]; //$price->_AddTax($_SESSION['sess_coupon']["coupon_amount"], $tax->data[$_SESSION['sess_coupon']["coupon_tax_class"]]);
                $tpl_data['coupon_amount'] = $price->_StyleFormat($tmp_amount);

            }
			else if ($tpl_data['arr_coupon']["coupon_free_shipping"]>0)
				$tpl_data['coupon_amount'] =  TEXT_COUPON_TYPE_FREESHIPPING;
				
			$tpl_data['currency'] = $currency->code;
			$tpl_data['coupon_percent'] = $tpl_data['arr_coupon']["coupon_percent"];
			$tpl_data['new_total'] = $price->_StyleFormat($new_amount);
			
			$coupon_hash = md5($tpl_data['arr_coupon'][$key]);
			$tpl_data['remove_coupon_link'] = $xtLink->_link(array('page' => 'cart', 'params' => 'remove_coupon='.$coupon_hash));
			
	        $plugin_template = new Template();
	        $plugin_template->getTemplatePath($tpl, 'xt_coupons', '', 'plugin');
			$s = $plugin_template->getTemplate('', $tpl, $tpl_data);
	        echo ($s);
	    }
	}
}