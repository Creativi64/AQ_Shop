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

require_once _SRV_WEBROOT.'plugins/xt_easy_credit/classes/class.easy_credit.php';

class xt_easy_credit extends easy_credit {

	var $data=array();
	var $external = false;
	var $subpayments = false;
	var $iframe = false;

	public $url_data = array();

	function _getParams()
	{
		$params = array();
		return $params;
	}

	function setPosition($pos)
	{
	}

	function __construct(){
		parent::__construct();
	}

	function build_payment_info($data)
	{

	}

    static function isAvailable()
    {
        if (!array_key_exists('easy_credit_available', $_REQUEST))
        {
            $_REQUEST['easy_credit_available'] = false;

            $paym = new payment();
            $pp = $paym->_getPossiblePayment();
            foreach($pp as $p)
            {
                if($p['payment_code'] == 'xt_easy_credit')
                {
                    $_REQUEST['easy_credit_available'] = true;
                    break;
                }
            }
        }

        return $_REQUEST['easy_credit_available'];
    }

    static function conditionsFulfilled()
    {
        $cartAmount = self::getCartAmount();
        if($cartAmount < self::getFinancingMin() || $cartAmount > self::getFinancingMax()
            ||
            $_SESSION['customer']->customer_shipping_address['address_book_id'] != $_SESSION['customer']->customer_payment_address['address_book_id']
            ||
            ($_SESSION['customer']->customer_payment_address['customers_gender'] != 'm' && $_SESSION['customer']->customer_payment_address['customers_gender'] != 'f')
            ||
            ($_SESSION['customer']->customer_shipping_address['customers_gender'] != 'm' && $_SESSION['customer']->customer_shipping_address['customers_gender'] != 'f')
            ||
            $_SESSION['customer']->customer_shipping_address['customers_country_code'] != 'DE'
        )
        {
            return false;
        }
        return true;
    }

    static function getCartAmount()
    {
        global $customers_status;

        $amount = $_SESSION['cart']->content_total['plain'];
        if ($customers_status->customers_status_show_price_tax != '1' && $customers_status->customers_status_add_tax_ot=='1')
        {
            foreach($_SESSION['cart']->content_tax as $content_tax)
            {
                $amount += $content_tax['tax_value']['plain'];
            }
        }

        return $amount;
    }

}
