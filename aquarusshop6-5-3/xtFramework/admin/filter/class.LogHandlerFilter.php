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

class LogHandlerFilter extends formFilter {
    
    
    public static function  formFields(){
        
   
        $eF = new ExtFunctions();
        
        $f1 = PhpExt_Form_DateField::createDateField("filter_date_from", ucfirst(TEXT_DATE)) ->setEmptyText(TEXT_FROM);
        $f1 =  self::setWidth($f1,"52px");
        $f2= PhpExt_Form_DateField::createDateField("filter_date_to", "")->setEmptyText(TEXT_TO); 
        $f2 =  self::setWidth($f2,"52px");
        $f[] = self::twoCol($f1, $f2);
        
        $f[] = self::setWidth(PhpExt_Form_TextField::createTextField("filter_identification",ucfirst(TEXT_IDENTIFICATION)),"150px");


        $combo = $eF->_comboBox('filter_message_source', ucfirst(TEXT_MESSAGE_SOURCE),self::$dropdownUrl.'?get=loghandler_sources',"156");
        if ($_SESSION['filters_LogHandler']['filter_message_source'] != ""){
            $combo->setValue($_SESSION['filters_LogHandler']['filter_message_source']);
        }
        $f[] = self::setWidth($combo,"133px");

        $combo = $eF->_comboBox('filter_class', ucfirst(TEXT_CLASS),self::$dropdownUrl.'?get=loghandler_classes',"156");
        if ($_SESSION['filters_LogHandler']['filter_class'] != ""){
            $combo->setValue($_SESSION['filters_LogHandler']['filter_class']);
        }
        $f[] = self::setWidth($combo,"133px");

        return $f;
    }
}
