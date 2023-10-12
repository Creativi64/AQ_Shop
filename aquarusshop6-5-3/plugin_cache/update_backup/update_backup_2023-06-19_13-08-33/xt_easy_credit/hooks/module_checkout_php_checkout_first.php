<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . 'plugins/xt_easy_credit/classes/class.xt_easy_credit.php';

$vorgangInfo = false;
if (xt_easy_credit::isAvailable())
{
    global $page;

    if ($page->page_action == 'payment')
    {
        $_SESSION['cart']->_deleteSubContent('easy_credit_interest');
        $_SESSION['cart']->_refresh();
    }

    if ($_GET['easy_credit_installments'] == 'true')
    {
        $ec_error = true;
        $ec = new easy_credit();
        $decision = $ec->getDecision();
        if(is_array($decision) && !empty($decision['entscheidung']['entscheidungsergebnis']))
        {
            if($decision['entscheidung']['entscheidungsergebnis'] == 'GRUEN')
            {
                $_SESSION['easy_credit'] = array_merge($_SESSION['easy_credit'], $decision);

                $vorgangInfo = $ec->getVorgang();
                if (is_array($vorgangInfo))
                {
                    $_SESSION['easy_credit'] = array_merge($_SESSION['easy_credit'], $vorgangInfo);

                    $financingInfo = $ec->getFinancingInfo();
                    if (is_array($vorgangInfo))
                    {
                        $_SESSION['easy_credit'] = array_merge($_SESSION['easy_credit'], $financingInfo);
                        $_SESSION['easy_creditInstallmentsCheckout'] = true;
                        $ec_error = false;
                    }
                }
            }
            else
            {
                global $info, $xtLink;
                unset($_SESSION['easy_creditInstallmentsCheckout']);
                $info->_addInfoSession('Leider können wir Ihnen zum jetzigen Zeitpunkt keinen Ratenkauf anbieten. Bitte wählen Sie eine andere Zahlungsart. Wir bitten um Ihr Verständnis.','error');
                $xtLink->_redirect($decision['url']);
            }
        }
        if($ec_error)
        {
            global $info, $xtLink;
            unset($_SESSION['easy_creditInstallmentsCheckout']);
            $xtLink->_redirect($ec->CANCEL_REST_VORGANG_INIT_URL);
        }

    }

    // DONT set the interest as order total
    if (false && $_SESSION['easy_creditInstallmentsCheckout'] == true)
    {
        $_SESSION['selected_payment'] = 'xt_easy_credit';

        $interest = $_SESSION['easy_credit']['ratenplan']['zinsen']['anfallendeZinsen'];
        if ($_SESSION['easy_credit']['ratenplan']['zinsen']['anfallendeZinsen'])
        {
            $interest_data_array = array('customer_id' => $_SESSION['registered_customer'],
                'qty' => 1,
                'name' => TEXT_EASY_CREDIT_INTEREST,
                'model' => 'easy_credit_interest',
                'key_id' => 0,
                'price' => $interest,
                'tax_class' => 0,
                'sort_order' => 9999,
                'type' => 'easy_credit_interest'//'financing_fee'
            );
            $_SESSION['cart']->_addSubContent($interest_data_array);
            $_SESSION['cart']->_refresh();
        }
    }


    if ($page->page_action == 'shipping' || $page->page_action == 'payment' || $page->page_action == 'success')
    {
        unset($_SESSION['reshash']);
        unset($_SESSION['nvpReqArray']);
        unset($_SESSION['easy_creditInstallmentsCheckout']);
        unset($_SESSION['conditions_accepted_easy_credit']);
    }
}

