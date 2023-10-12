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

class redirect_404Filter extends formFilter{
    
    public static function  formFields(){
        $eF = new ExtFunctions();
        $tf = PhpExt_Form_TextField::createTextField("filter_keyword",ucfirst(TEXT_KEYWORD))
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"150px"))); 
        if ($_SESSION['filters_redirect_404']['filter_keyword'] != ""){
            $tf->setValue($_SESSION['filters_redirect_404']['filter_keyword']);
        }
        $f[] = $tf;

        $combo = $eF->_comboBox('filter_store', ucfirst(TEXT_STORE),self::$dropdownUrl.'?get=stores',"156");
		$f[] = self::setWidth($combo,"133px");
        if ($_SESSION['filters_redirect_404']['filter_store'] != ""){
            $combo->setValue($_SESSION['filters_redirect_404']['filter_store']);
        }
 
        $combo = $eF->_comboBox('filter_language', ucfirst(TEXT_LANGUAGE),self::$dropdownUrl.'?get=language_codes',"156");
        $f[] = self::setWidth($combo,"133px");
        if ($_SESSION['filters_redirect_404']['filter_language'] != ""){
            $combo->setValue($_SESSION['filters_redirect_404']['filter_language']);
        }


        $f1 = PhpExt_Form_DateField::createDateField("filter_date_from", ucfirst(TEXT_DATE)) ->setEmptyText(TEXT_FROM);
        $f1 =  self::setWidth($f1,"52px");
        if ($_SESSION['filters_redirect_404']['filter_date_from'] != ""){
            $f1->setValue($_SESSION['filters_redirect_404']['filter_date_from']);
        }
        $f2= PhpExt_Form_DateField::createDateField("filter_date_to", "")->setEmptyText(TEXT_TO);
        $f2 =  self::setWidth($f2,"52px");
        if ($_SESSION['filters_redirect_404']['filter_date_to'] != ""){
            $f2->setValue($_SESSION['filters_redirect_404']['filter_date_to']);
        }
        $f[] = self::twoCol($f1, $f2);

        return $f;
    }
}

?>