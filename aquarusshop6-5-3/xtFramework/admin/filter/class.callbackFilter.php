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

class callbackFilter extends formFilter {
    
    
    public static function  formFields(){
        
   
        $eF = new ExtFunctions();
        
        $f1 = PhpExt_Form_DateField::createDateField("filter_date_from", ucfirst(TEXT_DATE)) ->setEmptyText(TEXT_FROM);
        $f1 =  self::setWidth($f1,"52px");
        $f2= PhpExt_Form_DateField::createDateField("filter_date_to", "")->setEmptyText(TEXT_TO); 
        $f2 =  self::setWidth($f2,"52px");
        $f[] = self::twoCol($f1, $f2);
        
        $f[] = self::setWidth(PhpExt_Form_TextField::createTextField("filter_orders_id",ucfirst(TEXT_ORDERS_ID)),"150px");

        $f[] = self::setWidth(PhpExt_Form_TextField::createTextField("filter_transaction_id",ucfirst(TEXT_TRANSACTION_ID)),"150px");


        $combo = $eF->_comboBox('filter_module', ucfirst(TEXT_MODULE),self::$dropdownUrl.'?get=callback_modules',"156");
        if ($_SESSION['filters_callback']['filter_module'] != ""){
            $combo->setValue($_SESSION['filters_callback']['filter_module']);
        }
        $f[] = self::setWidth($combo,"133px");

        $combo = $eF->_comboBox('filter_class', ucfirst(TEXT_CLASS),self::$dropdownUrl.'?get=callback_classes',"156");
        if ($_SESSION['filters_callback']['filter_class'] != ""){
            $combo->setValue($_SESSION['filters_callback']['filter_class']);
        }
        $f[] = self::setWidth($combo,"133px");

        $f[] = self::setWidth(PhpExt_Form_TextField::createTextField("filter_error_msg",ucfirst(TEXT_ERROR_MSG)),"150px");

        return $f;
    }
}
