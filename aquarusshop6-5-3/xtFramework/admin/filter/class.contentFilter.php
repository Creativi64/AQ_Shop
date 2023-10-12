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

class ContentFilter extends formFilter {
    

    public static function  formFields(){
    	global $xtPlugin, $store_handler;
    	
        $eF = new ExtFunctions();
        
        $filter_name = PhpExt_Form_TextField::createTextField("filter_content_name",ucfirst(TEXT_NAME))->setEmptyText(TEXT_INCLUDES);
        $f[] = self::setWidth($filter_name, "150px");

        $itemsPerPage = _SYSTEM_ADMIN_PAGE_SIZE;
        $filter_items_per_page = PhpExt_Form_NumberField::createNumberField("filter_items_per_page",ucfirst(TEXT_ITEMS_PER_PAGE), 'filter_items_per_page_' . __CLASS__ .'_content')->setValue($itemsPerPage);
        $f[] = self::setWidth($filter_items_per_page, "150px");

        //$cb = PhpExt_Form_Checkbox::createCheckbox("filter_content_status",ucfirst(TEXT_ADMIN_ACTON_STATUS),null,1);
        //$cb->setChecked(true);
        //$f[]  = $cb;
        $f[]  =self::setWidth($eF->_comboBox('filter_content_status', ucfirst(TEXT_ADMIN_ACTON_STATUS),self::$dropdownUrl.'?get=status_truefalse',"156"),"133px");

        $stores = $store_handler->getStores();
        if(count($stores)>1)
        {
            $f[] = self:: setWidth($eF->_comboBox('filter_content_shop', ucfirst(TEXT_STORE), self::$dropdownUrl.'?get=stores',"156"),"133px");
        }
        $f[] = self:: setWidth($eF->_comboBox('filter_content_permission', ucfirst(TEXT_PERMISSION), self::$dropdownUrl.'?get=customers_status',"156"),"133px");

        ($plugin_code = $xtPlugin->PluginCode('class.contentFilter.php:formFields_bottom')) ? eval($plugin_code) : false;
     
        return $f;
    }
    
}
