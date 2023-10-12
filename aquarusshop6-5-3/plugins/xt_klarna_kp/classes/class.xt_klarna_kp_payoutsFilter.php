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

class xt_klarna_kp_payoutsFilter extends formFilter{

    public static function  formFields(){
        $eF = new ExtFunctions();

        $combo = $eF->_comboBox('filter_currency', ucfirst(TEXT_CURRENCY),self::$dropdownUrl.'?get=currencies',"156");
        $f[] = self::setWidth($combo,"133px");
        if ($_SESSION['filters_klarna_kp_payouts']['filter_currency'] != ""){
            $combo->setValue($_SESSION['filters_klarna_kp_payouts']['filter_currency']);
        }

        $now = new DateTime();
        $before = $now->sub(new DateInterval('P30D'));

        $f1 = PhpExt_Form_DateField::createDateField("filter_date_from", ucfirst(TEXT_DATE))
            ->setEmptyText(TEXT_FROM)
            ->setFormat('Y/m/d');
        $f1 =  self::setWidth($f1,"52px");
        if ($_SESSION['filters_klarna_kp_payouts']['filter_date_from'] != ""){
            $f1->setValue($_SESSION['filters_klarna_kp_payouts']['filter_date_from']);
        }
        else {
            $f1->setValue($before->format('Y/m/d'));
        }


        $f2= PhpExt_Form_DateField::createDateField("filter_date_to", "")
            ->setEmptyText(TEXT_TO)
            ->setFormat('Y/m/d');;
        $f2 =  self::setWidth($f2,"52px");
        if ($_SESSION['filters_klarna_kp_payouts']['filter_date_to'] != ""){
            $f2->setValue($_SESSION['filters_klarna_kp_payouts']['filter_date_to']);
        }
        else {
            $now = new DateTime();
            $f2->setValue($now->format('Y/m/d'));
        }

        $f[] = self::twoCol($f1, $f2);

        return $f;
    }
}
