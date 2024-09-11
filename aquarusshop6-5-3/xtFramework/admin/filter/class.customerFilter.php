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

class CustomerFilter extends formFilter{
    
    
    public static function  formFields(){
        
        $eF = new ExtFunctions();
        $f[] = PhpExt_Form_TextField::createTextField("filter_name",ucfirst(TEXT_NAME))
                ->setEmptyText(TEXT_INCLUDES);

        $mb = $eF->_multiComboBox2(ucfirst(TEXT_STATUS), "filter_status", self::$dropdownUrl.'?get=customers_status&skip_empty=true', 180);
        $f[] = self::setWidth($mb, '133px');


        $f[] = PhpExt_Form_TextField::createTextField("filter_company",ucfirst(TEXT_STORE_ACCOUNT_COMPANY))
               ->setEmptyText(TEXT_INCLUDES);

        global $store_handler;
        $stores = $store_handler->getStores();
        if(count($stores)>1)
        {
            $f[] = self::setWidth($eF->_multiComboBox2( ucfirst(TEXT_SHOP),  'filter_customers_shop', self::$dropdownUrl.'?get=stores&skip_empty=true', 156),'133px');
        }


        $f[] = self::setWidth($eF->_multiComboBox2( ucfirst(TABTEXT_CUSTOMERS_STATUS),  'filter_customers_status', self::$dropdownUrl.'?get=customers_status&skip_empty=true', 156), '133px');

        $f[] = PhpExt_Form_TextField::createTextField("filter_email",ucfirst(TEXT_EMAIL), null, PhpExt_Form_FormPanel::VTYPE_EMAIL)
                 ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"150px")));



        $f[] = self::setWidth($eF->_comboBox('filter_language', ucfirst(TEXT_LANGUAGE), self::$dropdownUrl.'?get=language_codes',"154")
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"auto"))),"133px");

        $f[] = self::setWidth($eF->_comboBox('filter_gender', ucfirst(TEXT_CUSTOMERS_GENDER), self::$dropdownUrl.'?get=gender1',"154")
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"auto"))),"133px");

        // TODO  TEXT_LAST_ORDER_X_YEARS_AGO
        $f[] = PhpExt_Form_TextField::createTextField("filter_last_order_years_ago",TEXT_LAST_ORDER_X_YEARS_AGO)
            ->setEmptyText('');

        global $xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.customerFilter.php:formFields_bottom')) ? eval($plugin_code) : false;
        
        return $f;
    }
}
