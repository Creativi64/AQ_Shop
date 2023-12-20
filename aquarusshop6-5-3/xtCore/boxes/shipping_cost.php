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

if (is_object($_SESSION['cart']) && count($_SESSION['cart']->show_content) > 0)
{
	include_once 'xtFramework/classes/class.countries.php';

    global $xtPlugin;
	
	$tpl = 'shipping_cost.html';

	$countries = new countries(TRUE,'store');

	$count_countries = count($countries->countries_list);
    if ($count_countries==1)
    {
        $countries_list_sorted = $countries->countries_list_sorted;
        reset($countries_list_sorted);
        $first_key = key($countries_list_sorted);
        $_POST["coupons_country"] = $countries_list_sorted[$first_key]["countries_iso_code_2"];
    }

    $cost ='';
	$shipping = new shipping();
	$shipping_data  = '';


    $ar = array();$data = array();

    $selected_country_code = $countries->default_country;
    $selected_country = $countries->countries_list[$selected_country_code];
    if($_SESSION["registered_customer"] && !empty(sessionCustomer()->customer_shipping_address['customers_country_code']))
    {
        $selected_country_code = sessionCustomer()->customer_shipping_address['customers_country_code'];
        $selected_country = $countries->countries_list[$selected_country_code];
    }
    if ($_POST["coupons_country"]!='')
    {
        $selected_country_code = $_POST["coupons_country"];
        $selected_country = $countries->countries_list[$_POST["coupons_country"]];
    }

    $data['customer_shipping_address']['customers_country_code'] = $selected_country_code;
    $data['customer_shipping_address']['customers_zone'] = $selected_country["zone_id"];
    $shipping->_shipping($data);
    $shipping_data = $shipping->shipping_data;
    $count_shipping= count($shipping_data);

    $selected_shipping_code = $_POST["coupons_shipping"];
    if ($selected_shipping_code != '')
    {
        $cost = $shipping_data[$_POST["coupons_shipping"]]["shipping_price"]["formated"];
    }
    else {
        reset($shipping_data);
        $selected_shipping_code = key($shipping_data);
        $cost = $shipping_data[$selected_shipping_code]["shipping_price"]["formated"];
    }

    ($plugin_code = $xtPlugin->PluginCode('box_shipping_cost:tpl_data')) ? eval($plugin_code) : false;
	
	$tpl_data['count_countries'] = $count_countries;
	$tpl_data['count_shipping'] = $count_shipping;
	$tpl_data['coupons_country'] = $countries->countries_list_sorted; //array(array('id'=>1,'text'=>'BG'),array('id'=>2,'text'=>'EN'));
	$tpl_data['coupons_shipping'] = $shipping_data;
	$tpl_data['selected_coupons_shipping'] = $selected_shipping_code;
	$tpl_data['selected_country'] = $selected_country_code;
    $tpl_data['selected_country_zone_id'] = $selected_country['zone_id'];
	$tpl_data['cost'] = $cost;
	
	$show_box = true;
}
else $show_box = false;
