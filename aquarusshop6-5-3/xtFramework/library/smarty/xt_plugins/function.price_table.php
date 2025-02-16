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

function smarty_function_price_table($params, & $smarty) {
	global $xtPlugin,$p_info,$price, $tax;

    $tmp_p = false;
	if (array_key_exists('pid', $params) && (int)$params['pid'] > 0) {
		$tmp_p = product::getProduct($params['pid']);
	}
	

	if (!is_object($p_info) && !is_object($tmp_p)) return false;
		
    $price_matrix = array();
		
	if (is_object($p_info) && array_key_exists('group_price', $p_info->data) && is_array($p_info->data['group_price']['prices'])) {
		$price_matrix = $p_info->data['group_price']['prices'];
	}
	if (is_object($tmp_p) && array_key_exists('group_price', $tmp_p->data) && is_array($tmp_p->data['group_price']['prices'])) {
		$price_matrix = $tmp_p->data['group_price']['prices'];
	}

    if (count($price_matrix)) {

        // entweder wir nehmen den wirklich originalen preis als grundpreis
        // das wäre also ein streichpreis
        // der wird aber im Template so nicht ausgegeben, weil so nicht gedacht
        // kundengruppenpreis ist kein Streichpreis
        // $base_price = $p_info->data['products_price']['original_price_otax'];

        // daher nehmen wir den ersten gruppenpreis auf als 'Grundpreis'
        // zur berechnung der %
        $base_price = $price_matrix[0]['price'];

		// calculate taxes
		$group_prices = array();
		for ($i = 0, $n = sizeof($price_matrix); $i < $n; $i ++) {
			if ($price_matrix[$i]['qty'] == 1) {
				$qty = $price_matrix[$i]['qty'];
				if (isset($price_matrix[$i +1]['qty']) && $price_matrix[$i +1]['qty'] != 2)
				    $qty = $price_matrix[$i]['qty'].'-'. ($price_matrix[$i +1]['qty'] - 1);
				else
                    $qty = $price_matrix[$i]['qty'];
			} else {
				$qty = ' >= '.$price_matrix[$i]['qty'];
				if (isset($price_matrix[$i +1]['qty']))
				$qty = $price_matrix[$i]['qty'].'-'. ($price_matrix[$i +1]['qty'] - 1);
			}
			$g_price = $price_matrix[$i]['price'];

            $saving = 0;
            // saving nur erzeugen, wenn der festgelegte grundpreis grösser als der aktuelle matrix-preis
            if ($g_price!=0 && $base_price!=0 && $base_price > $price_matrix[$i]['price']) {
                $saving = 100-($g_price/$base_price*100);
                $saving = round($saving,0);
            }
            
            $g_price = $price->_AddTax($g_price, $p_info->data['products_tax_rate']);
			$g_price=array('plain'=>$g_price,'formated'=>$price->_StyleFormat($g_price));
			
			if ($p_info->data['products_vpe_status'] == 1 && $p_info->data['products_vpe_value'] > 0) {
				$pricePerUnit_num = $price->_StyleFormat($g_price['plain']/$p_info->data['products_vpe_value']);
				$perUnitHeader_str = constant('TEXT_SHIPPING_BASE_PER').'&nbsp;'.$p_info->data['base_price']['vpe']['name'];

				$group_prices[] = array ('QTY' => $qty, 'PRICE' => $g_price,'saving'=>$saving, 'pricePerUnit'=>$pricePerUnit_num, 'perUnitHeader'=>$perUnitHeader_str);
			}
			else {
				$group_prices[] = array ('QTY' => $qty, 'PRICE' => $g_price,'saving'=>$saving);
			}
			
		}
		$template = $price->_Format(array('prices'=>$group_prices,'format'=>true,'format_type'=>'graduated-table'));
		echo $template['formated'];

	} else {
		return false;
	}
}
