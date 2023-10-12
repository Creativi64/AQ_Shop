<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . 'plugins/xt_easy_credit/classes/class.xt_easy_credit.php';

if (xt_easy_credit::isAvailable())
{
    global $p_info;
    $html = '';
    $value = $p_info->data['products_price']['plain'];

    if($value>=xt_easy_credit::getFinancingMin() &&
        $value<=xt_easy_credit::getFinancingMax())
    {
        $value = array(
            'value' => $value,
            'example' => array('monthly_payment' => array('currency_code' => 'EUR')),
            'financing_options' => array( 0 => array('financing_value' => number_format($value,2, ',', '.')))
        );
        $html = xt_easy_credit::buildInfoButton($value, 'product', 'link');
    }
    else {
        $html = xt_easy_credit::buildInfoButton($value, 'product');
    }

    echo $html;
}
