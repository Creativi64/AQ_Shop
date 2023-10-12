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

class acl_UserFilter extends formFilter{
    
    
    public static function  formFields(){
        $eF = new ExtFunctions();
        $f[] = PhpExt_Form_TextField::createTextField("filter_name",ucfirst(TEXT_NAME))
                ->setEmptyText(TEXT_INCLUDES)
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"150px"))); 
       
        $f[] = PhpExt_Form_TextField::createTextField("filter_username",ucfirst(TEXT_HANDLE))
                ->setEmptyText(TEXT_INCLUDES)
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"150px"))); 
        $f[] = PhpExt_Form_Checkbox::createCheckbox("filter_userstatus",ucfirst(TEXT_ADMIN_ACTON_STATUS));
        $f[] = self::setWidth($eF->_multiComboBox2( ucfirst(TITLE_ACL_GROUPS),"filter_usergroup", self::$dropdownUrl.'?get=acl_group_list'),"133px"); 
 
        return $f;
    }
}
?>