<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $smarty Smarty */
$bd_order_data = $smarty->_getSmartyObj()->getTemplateVars('order_data');
if($bd_order_data && is_array($bd_order_data['bank_details']) && count($bd_order_data['bank_details']))
{
    $tpl_bd = 'account_history_info_main_table_top.tpl.html';

    foreach($bd_order_data['bank_details'] as $bd)
    {
        $iban = str_replace(' ','',$bd["bad_international_bank_account_number"]);
        $bd["bad_international_bank_account_number"] = wordwrap($iban,4,' ',true);

        $tpl_data = ['bd' => $bd];
        $template = new Template();
        $template->getTemplatePath($tpl_bd, 'xt_bank_details', '', 'plugin');
        $html = $template->getTemplate('', $tpl_bd, $tpl_data);

        echo $html;
    }
}

