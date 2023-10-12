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

class xt_master_slaveFilter extends formFilter {


    public static function  formFields(){
    	global $xtPlugin;
    	
        $eF = new ExtFunctions();

        $nf = PhpExt_Form_NumberField::createNumberField("filter_attributes_parent",ucfirst(TEXT_PRODUCTS_QUANTITY));
        $cb = $eF->_comboBox('filter_attributes_parent', ucfirst(TEXT_ATTRIBUTES_PARENT), self::$dropdownUrl . '?get=attrib_parent', "156");
        if ($_SESSION['filters_xt_master_slave']['filter_attributes_parent'] != ""){
            $nf->setValue(['filters_xt_master_slave']['filter_attributes_parent']);
        }
        $a[] = self:: setWidth($cb, "133px");

        $itemsPerPage = _SYSTEM_ADMIN_PAGE_SIZE;
        $filter_items_per_page = PhpExt_Form_NumberField::createNumberField("filter_items_per_page",ucfirst(TEXT_ITEMS_PER_PAGE), 'filter_items_per_page_Xt_master_slaveFilter_xt_master_slave' )->setValue($itemsPerPage);
        if ($_SESSION['filters_xt_master_slave']['filter_items_per_page'] != ""){
            $filter_items_per_page->setValue($_SESSION['filters_xt_master_slave']['filter_items_per_page']);
        }
        $f[] = self::setWidth($filter_items_per_page, "150px");

        ($plugin_code = $xtPlugin->PluginCode('class.msAttributesFilter.php:formFields_bottom')) ? eval($plugin_code) : false;

        foreach($a as $field){
            $f[] = $field;
        }
     
       return $f;
    }

}
