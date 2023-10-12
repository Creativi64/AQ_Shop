<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


global $page, $db, $language, $xtLink, $currency, $tax, $countries, $customers_status, $info;

if(true || ($page->page_name == 'checkout' && $page->page_action == 'confirmation'))
{
    $tpl_file = 'modal_paypal_express.tpl.html';
    $template = new Template();
    $template->getTemplatePath($tpl_file, 'xt_paypal', '', 'plugin');
    $tpl_data = [];

    $html = $template->getTemplate('', $tpl_file, $tpl_data);
    echo $html;
}
