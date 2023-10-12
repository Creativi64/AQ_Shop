<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once 'ignored_pages.inc.php';

if ($_POST['action']!='update_product' && $_POST['action']!='add_product' && $value['group_discount_allowed']=='1' && !XtCustomersDiscount_orderEditActive())
{
    global $customers_status;

    if (/*$customer_group_discount*/ $_SESSION['customer_group_discount'] > 0 && $value['flag_has_specials']!=true && empty($value['group_price']))
    {
        $final = round($regular_price,2)*$value['products_quantity'];
        if($customers_status->customers_status_show_price_tax == '1')
        {
            $final += $regular_price_final_tax;
        }
        $final = round($final,2);
        $saving = -($value['_original_products_price']['plain']*$value['products_quantity']-$final);
        $this->total_discount += $saving;
    }
}