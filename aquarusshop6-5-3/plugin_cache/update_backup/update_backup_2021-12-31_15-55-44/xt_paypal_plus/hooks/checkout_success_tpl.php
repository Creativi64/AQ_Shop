<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS .'xt_paypal_plus/classes/class.paypal_plus.php';

global $language, $store_handler;

global $success_order;

if($success_order && is_array($success_order->order_data['bank_details']) && count($success_order->order_data['bank_details']))
{
    foreach($success_order->order_data['bank_details'] as $k => $bd)
    {
        if($bd['bad_tech_issuer'] == 'xt_paypal_plus')
        {
            $tpl = 'paypal_plus_pui_details.html';
            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_paypal_plus', '', 'plugin');
            $tp_data = array(
                'bd' => $bd,
            );
            $details = $template->getTemplate('', $tpl, $tp_data);

            $tpl = 'paypal_plus_pui_success.html';
            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_paypal_plus', '', 'plugin');
            $tp_data = array(
                'txt_claim_assign' => sprintf(TEXT_PPP_PUI_SUCCESS_CLAIM_ASSIGN, _STORE_SHOPOWNER_COMPANY),
                'txt_bank_details' => $details,
            );
            $tpl_html = $template->getTemplate('', $tpl, $tp_data);
            echo $tpl_html;
            break;
        }
    }

}

