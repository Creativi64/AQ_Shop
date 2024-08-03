<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_functions.php';

global $price;

if(XT_MASTER_SLAVE_ACTIVE && $this->data['products_master_model'] != '')
{
    $master_model = false;
    if (!empty($this->data['products_master_model']))
    {
        $master_model = $this->data['products_master_model'];
    }
    $m_data = false;
    if ($master_model)
    {
        $m_data = xt_master_slave_functions::getMasterData($master_model);
    }

    if ($m_data)
    {
        $sum_quantities = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_SUM_SLAVE_QUANTITY_FOR_GRADUATED_PRICE, 'sum_quantity_for_graduated_price', $m_data);

        if ($sum_quantities)
        {

            if (!empty($sp_type) && $sp_type == 'group')
            {
                if ($this->data['price_flag_graduated_' . $price->p_group] != 1)
                {
                    return true;
                }  // don't continue
            }
            else
            {
                if ($this->data['price_flag_graduated_all'] != 1)
                {
                    return true;
                }  // don't continue
            }
            $slaves = xt_master_slave_functions::get_slave_from_master($this->data['products_master_model']);
            $product_slaves = array(); // products_id of all slaves for the current products_master_model
            if (is_array($slaves))
            {
                foreach ($slaves as $slave)
                {
                    array_push($product_slaves, $slave['products_id']);
                }
            }

            $products_in_cart = $_SESSION['cart']->content; // products in the cart

            if (is_array($products_in_cart) && count($products_in_cart) > 0
                && is_array($product_slaves) && count($product_slaves) > 0)
            {
                $slaves_qnt = 0;
                foreach ($products_in_cart as $p)
                {
                    if (in_array($p['products_id'], $product_slaves))
                    {
                        $slaves_qnt += $p['products_quantity'];
                    }
                }
                if ($this->qty != $slaves_qnt)
                {
                    // change the quantity so the graduated price could be generated based on total master quantity (it's a sum of all slaves quantities)
                    $this->qty = $slaves_qnt;
                }
            }
        }
    }
}
