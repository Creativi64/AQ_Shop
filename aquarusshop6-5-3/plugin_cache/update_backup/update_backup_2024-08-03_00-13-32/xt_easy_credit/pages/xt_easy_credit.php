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

global $info, $xtLink;

include_once _SRV_WEBROOT . 'plugins/xt_easy_credit/classes/class.easy_credit.php';


if (isset($_GET['easy_credit_action']))
{
    $action = $_GET['easy_credit_action'];
    $value = (float) $_GET['value'];
    if($action == 'getFinancingOptions')
    {
        if (xt_easy_credit::isAvailable())
        {
            global $p_info;
            $html = '<p>Es tut uns leid, scheinbar ist ein Fehler aufgetreten</p>';
            $data = array('available' => false);

            if($value>=xt_easy_credit::getFinancingMin() &&
                $value<=xt_easy_credit::getFinancingMax())
            {
                $easy_credit = new easy_credit();

                global $p_info, $currency, $language;
                $financingOptions = $easy_credit->getFinancingOptions($value, $currency->code, 'DE');
                $html = xt_easy_credit::buildInfoButton($financingOptions, 'product', 'popup');
                $data['available'] = false;

            }

            echo $html;
            //$data['html'] = $html;
            //echo json_encode($data);
            die();
        }
    }

}
