<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once 'ignored_pages.inc.php';

global $customers_status, $page, $calcMsPrice;

/** @var $products_tax float */
/** @var $special_price_otax float */
/** @var $products_price float */
/** @var $cheapest_price_otax float */

if ($customers_status->customers_status_discount_flag=='1' && $this->data['group_discount_allowed'] && !XtCustomersDiscount_orderEditActive())
{
    $this->data['customer_group_discount'] = is_numeric($_SESSION['customer_group_discount']) ? $_SESSION['customer_group_discount'] : 0;

    // calcMSPrice is set in product::getPrice
    if ($_SESSION['customer_group_discount'] && is_numeric($_SESSION['customer_group_discount'])&& !$calcMsPrice)
    {
        global $price;

        // do we have graduated prices
        $graduated          = !empty($grp_price) && $grp_price['no_graduated']!=1     && ($this->data['price_flag_graduated_all'] || $this->data['price_flag_graduated_' . $price->p_group]);
        $graduated_single   = !empty($grp_price) && $grp_price['graduated_single']==1 && ($this->data['price_flag_graduated_all'] || $this->data['price_flag_graduated_' . $price->p_group]);

        if($graduated)
        {
            $special_price_otax = $grp_price['price'];
            $special_price_otax = $special_price_otax - ($special_price_otax * ($_SESSION['customer_group_discount'] / 100));
            $special_price_otax = $price->_calcCurrency($special_price_otax);
            $special_price = $price->_AddTax($special_price_otax, $products_tax);
        }
        else {
            $special_price_otax = $special_price_otax ? $special_price_otax : $products_price;
            $special_price_otax = $special_price_otax - ($special_price_otax * ($_SESSION['customer_group_discount'] / 100));
            $special_price_otax = $price->_calcCurrency($special_price_otax);
            $special_price = $price->_AddTax($special_price_otax, $products_tax);
        }

        $cheapest_price_otax = $cheapest_price_otax - ($cheapest_price_otax * ($_SESSION['customer_group_discount'] / 100));
        $cheapest_price_otax = $price->_calcCurrency($cheapest_price_otax);
        $cheapest_price = $price->_AddTax($cheapest_price_otax, $products_tax);

        // change group price for smarty price_table
        if (is_array($this->data['group_price']) && $cheapest_price_otax)
        {
            foreach ($this->data['group_price']['prices'] as $k => $v)
            {
                $this->data['group_price']['prices'][$k]['price'] = $v['price'] - ($v['price'] * ($_SESSION['customer_group_discount'] / 100));
            }
        }

        if ($graduated ||
            (isset($ms_price) && $ms_price['to'] != $ms_price['from'])
        )
        {
            $format_type = 'graduated_discount';
        }
        else if ($graduated_single)
        {
            $format_type = 'special_graduated_single';
        }
        else
        {
            $format_type = 'special';
        }

    }

}

