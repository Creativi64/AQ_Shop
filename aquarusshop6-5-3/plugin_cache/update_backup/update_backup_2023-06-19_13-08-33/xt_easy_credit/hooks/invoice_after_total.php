<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

$smartyCtxOrderData = $smarty->smarty->tpl_vars['data']->value['order']['order_data'];

if ($smartyCtxOrderData['payment_code'] == 'xt_easy_credit')
{
    global $price;
    $error = true;

    $financing_data = $smartyCtxOrderData['easy_credit_financing'];

    if (is_array($financing_data))
    {
        $tplFile = 'invoice-financing-info.tpl.html';
        $template = new Template();
        $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
        $tpl_data = array(
            'financing_interest' => $price->_StyleFormat($financing_data['ratenplan']['zinsen']['anfallendeZinsen']),
            'financing_total' => $price->_StyleFormat($financing_data['ratenplan']['gesamtsumme']),
            'finanzierungPlan' => $financing_data['tilgungsplanText']
        );
        $html = $template->getTemplate('', $tplFile, $tpl_data);

        echo $html;
        $error = false;
    }

    if($error){
        error_log('xt_easy_credit email warning: Expected to find financing plan information but not found in orders_data');
    }
}

