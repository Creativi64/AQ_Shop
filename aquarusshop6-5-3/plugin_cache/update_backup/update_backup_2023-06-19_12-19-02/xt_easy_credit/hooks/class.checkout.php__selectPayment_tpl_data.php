<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $page;

if ($page->page_name == 'checkout' && ($page->page_action == 'payment' || $page->page_action == 'confirmation')  && xt_easy_credit::isAvailable() && $key == 'xt_easy_credit')
{
    if(xt_easy_credit::conditionsFulfilled())
    {
        $tpl_data['conditions_fulfilled'] = true;
        $ec = new easy_credit();
        $shopKennung = $ec->getClientID();

        if (empty($_SESSION['easy_credit_texte_' . $shopKennung]))
        {
            $text = $ec->getTextZustimmung();
            $_SESSION['easy_credit_texte_' . $shopKennung] = $ec->getTextZustimmung();
        }
        $tpl_data['text_zustimmung'] = $_SESSION['easy_credit_texte_' . $shopKennung]['zustimmungDatenuebertragungPaymentPage'];

        $custId = empty($_SESSION['registered_customer']) ? 0 : $_SESSION['registered_customer'];

            $dobTplData = customer::pageCustomerGetDobTplData('edit_address_edit', $_SESSION['customer']->customer_shipping_address['customers_dob'], $custId);

        $tpl_data = array_merge($tpl_data,$dobTplData);

        $tpl_data['customers_dob'] = $_SESSION['customer']->customer_shipping_address['customers_dob'];
    }
    else {
        $tpl_data['conditions_fulfilled'] = false;
        $txt = str_replace('EASY_CREDIT_MIN_AMOUNT', easy_credit::getFinancingMin(), TEXT_EASY_CREDIT_USAGE_CONDITIONS);
        $txt = str_replace('EASY_CREDIT_MAX_AMOUNT', easy_credit::getFinancingMax(), $txt);
        $tpl_data['text_not_available'] = $txt;
    }
}