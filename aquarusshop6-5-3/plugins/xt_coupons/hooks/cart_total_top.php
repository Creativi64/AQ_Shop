<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ( ! empty($_SESSION['sess_coupon']) && is_array($_SESSION['sess_coupon'])) {

	if ( ! empty($_SESSION['sess_coupon']['coupon_token_code'])) $key = 'coupon_token_code';
	else $key = empty($_SESSION['sess_coupon']['coupon_code']) ? null : 'coupon_code';

	if (isset($key))
	{
		global $xtLink,$currency;

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
	        $tpl = 'coupons_cart_total_top.html';
			$tpl_data['arr_coupon'] = $_SESSION['sess_coupon'];
			
			if ($_SESSION['sess_coupon']["coupon_percent"]>0)
			{
				$coupon_total_before_discount = 0;
                $coupon_saving = 0;
				$cp = new xt_coupons();
				$cpic = $cp->_get_coupon_products_in_cart($_SESSION['sess_coupon']['coupon_id']);
				/*
				$cpic_found = array();
				foreach($cpic['found'] as $pf)
                {
                    $cpic_found[] = $pf['products_id'];
                }
				*/
				foreach ($_SESSION['cart']->show_content as &$pr)
				{

				    $oldPrice = round($pr['products_price_before_discount']['plain'],2);
				    /*
                    if(in_array($pr['products_id'], $cpic_found))
                    {
                        // im tmpl wird der _original_prodcuts_price für den streichpreis genommen
                        // dieser ist aber unterwegs verlorengegangen , uU durch kundengruppenrabatt etc
                        // wir brauchen wieder ein saubers, ungecachtes product
                        $cleanProduct = new product($pr['products_id'],'price',1);
                        $coupon_saving += ($oldPrice -$cleanProduct->data['products_price']['plain']) * $pr['products_quantity'];
                        $oldPrice= $cleanProduct->data['products_price']['plain'];
                    }
				    */
                    $coupon_saving += ($oldPrice - $pr['products_price']['plain']) * $pr['products_quantity'];


                    $coupon_total_before_discount += $oldPrice * $pr['products_quantity'];


				}
				global $price;
				$coupon_saving_formated = $price->_StyleFormat(abs($coupon_saving));
				$tpl_data['coupon_amount'] =  ' - '.$coupon_saving_formated.' ('.$_SESSION['sess_coupon']["coupon_percent"].' % )';
				
				$tpl_data['currency'] = $currency->code;
				
				$tpl_data['new_total'] = $price->_StyleFormat($new_amount);
				$coupon_hash = md5($_SESSION['sess_coupon'][$key]);
				$tpl_data['remove_coupon_link'] = $xtLink->_link(array('page' => 'cart', 'params' => 'remove_coupon='.$coupon_hash));
				$tpl_data['coupon_total_before_discount'] = $price->_StyleFormat($coupon_total_before_discount);
		        $plugin_template = new Template();
		        $plugin_template->getTemplatePath($tpl, 'xt_coupons', '', 'plugin');
		        echo ($plugin_template->getTemplate('', $tpl, $tpl_data));
		   }
	    }
	}
}