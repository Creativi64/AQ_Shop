<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($_SESSION['easy_creditInstallmentsCheckout'] == true)
{
    global $price;

    $financing_data = $_SESSION['easy_credit']['ratenplan'];

    if ($financing_data)
    {
        $subDir = '';

        $tplFile = 'confirmation-financing-info.tpl.html';
        $template = new Template();
        $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
        $tpl_data = array(
            'financing_interest' => $price->_StyleFormat($financing_data['zinsen']['anfallendeZinsen']),
            'financing_total' => $price->_StyleFormat($financing_data['gesamtsumme']),
            'finanzierungPlan' => $_SESSION['easy_credit']['tilgungsplanText'],
            'urlVorvertraglicheInformationen' => $_SESSION['easy_credit']['allgemeineVorgangsdaten']['urlVorvertraglicheInformationen']
        );

        $html = $template->getTemplate('', $tplFile, $tpl_data);
        echo $html;
    }
}
