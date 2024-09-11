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

class OrderFilter extends formFilter{
    
    
    public static function  formFields(){

        global $xtPlugin;
        
        $eF = new ExtFunctions();
        
        $itemsPerPage = _SYSTEM_ADMIN_PAGE_SIZE;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_form_fields_top')) ? eval($plugin_code) : false;

        $filter_items_per_page = PhpExt_Form_NumberField::createNumberField("filter_items_per_page",ucfirst(TEXT_ITEMS_PER_PAGE), 'filter_items_per_page_' . __CLASS__ .'_order')->setValue($itemsPerPage);
        $f[] = self::setWidth($filter_items_per_page, "150px");
        
        $f1 = PhpExt_Form_NumberField::createNumberField("filter_id_from","ID") ->setEmptyText(TEXT_FROM);     
        $f2 = PhpExt_Form_NumberField::createNumberField("filter_id_to","") -> setEmptyText(TEXT_TO);   
        $f[] = self::twoCol($f1, $f2);
        
        $f1 = PhpExt_Form_DateField::createDateField("filter_date_from",  ucfirst(TEXT_DATE_PURCHASED)) ->setEmptyText(TEXT_FROM);       
        $f1 =  self::setWidth($f1,"52px");
        $f2 = PhpExt_Form_DateField::createDateField("filter_date_to","") -> setEmptyText(TEXT_TO);   
        $f2 =  self::setWidth($f2,"52px");
        $f[] = self::twoCol($f1, $f2);

        $c = $eF->_multiComboBox2( ucfirst(TEXT_STATUS), 'filter_order_status', self::$dropdownUrl.'?get=order_status&skip_empty=true',"180");
        $f[] = self::setWidth($c,"133px");
        
        
        $customer = PhpExt_Form_TextField::createTextField("filter_name",ucfirst(TEXT_CUSTOMER_NAME)) ->setEmptyText(TEXT_INCLUDES); 
        $customer = self::setWidth($customer,"150px");
        $f[] =  $customer;
        
        $f1 = PhpExt_Form_NumberField::createNumberField("filter_amount_from",ucfirst(TEXT_AMOUNT)) ->setEmptyText(TEXT_FROM);     
        $f2 = PhpExt_Form_NumberField::createNumberField("filter_amount_to","") -> setEmptyText(TEXT_TO);   
        $f[] = self::twoCol($f1, $f2);

        global $store_handler;
        $stores = $store_handler->getStores();
        if(count($stores)>1)
        {
            $a[] = self::setWidth($eF->_multiComboBox2(ucfirst(TEXT_SHOP), 'filter_order_shop', self::$dropdownUrl . '?get=stores&skip_empty=true',"180"), "133px");
        }

         //TEXT_PAYMENT
        $a[] = self::setWidth($eF->_multiComboBox2(ucfirst(TEXT_PAYMENT), 'filter_payment_way',  self::$dropdownUrl.'?get=payment_methods_codes&skip_empty=true',"180"),"133px");

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_form_fields_payments')) ? eval($plugin_code) : false;
        
        $a[] = self::setWidth($eF->_multiComboBox2(ucfirst(TEXT_SHIPPING_PERMISSION), 'filter_shipping_way', self::$dropdownUrl.'?get=shipping_methods_codes&skip_empty=true',"180"),"133px");

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_form_fields_shippings')) ? eval($plugin_code) : false;
        
        
        $f1 = PhpExt_Form_DateField::createDateField("filter_last_modify_from",	ucfirst(TEXT_LAST_MODIFIED)) ->setEmptyText(TEXT_FROM);       
        $f1 =  self::setWidth($f1,"52px");
        $f2 = PhpExt_Form_DateField::createDateField("filter_last_modify_to","") -> setEmptyText(TEXT_TO);   
        $f2 =  self::setWidth($f2,"52px");
        $a[] = self::twoCol($f1, $f2);
    
        $email = PhpExt_Form_TextField::createTextField("filter_email",ucfirst(TEXT_EMAIL), null, PhpExt_Form_FormPanel::VTYPE_EMAIL);
        $email = self::setWidth($email,"150px");
        $a[] = $email;

        $zip = PhpExt_Form_TextField::createTextField("filter_zip",ucfirst(TEXT_CUSTOMERS_POSTCODE));
        $zip = self::setWidth($zip,"150px");
        $a[] =  $zip;

        $phone = PhpExt_Form_TextField::createTextField("filter_phone",ucfirst(TEXT_PHONE).'/'.ucfirst(TEXT_CUSTOMERS_FAX))->setEmptyText(TEXT_INCLUDES);
        $phone = self::setWidth($phone,"150px");
        $a[] =  $phone;

        $model = PhpExt_Form_TextField::createTextField("filter_products_model",ucfirst(TEXT_PRODUCTS_MODEL));
        $model = self::setWidth($model,"150px");
        $a[] = $model;

        $a[] = PhpExt_Form_Checkbox::createCheckbox("filter_no_valid_customer_assoc",TEXT_NO_VALID_CUSTOMER_ASSOC);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_form_fields_bottom')) ? eval($plugin_code) : false;
        
        foreach($a as $field){
            $f[] = $field;
        }

        return $f;
    }
}
