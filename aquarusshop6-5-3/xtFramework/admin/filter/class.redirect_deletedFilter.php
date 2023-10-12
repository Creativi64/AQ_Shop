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

class redirect_deletedFilter extends formFilter{
    
    public static function  formFields(){
        $eF = new ExtFunctions();

        $combo = $eF->_comboBox('filter_language', ucfirst(TEXT_LANGUAGE),self::$dropdownUrl.'?get=language_codes&skip_empty=true',"156");
        $f[] = self::setWidth($combo,"133px");
        if ($_SESSION['filters_redirect_deleted']['filter_language'] != ""){
            $combo->setValue($_SESSION['filters_redirect_deleted']['filter_language']);
        }
        else {
            global $language;
            $_SESSION['filters_redirect_deleted']['filter_language'] = $language->code;
            $combo->setValue($language->code);
        }

        $f[] = PhpExt_Form_TextField::createTextField("filter_keyword",ucfirst(TEXT_KEYWORD))
                ->setCssStyle(new PhpExt_Config_ConfigObject(array("width"=>"150px"))); 
 
		$f[] = self::setWidth($eF->_comboBox('filter_store', ucfirst(TEXT_STORE),self::$dropdownUrl.'?get=stores',"156"),"133px");
		$f[] = self::setWidth($eF->_comboBox('filter_link_type', ucfirst(TEXT_LINK_TYPE),self::$dropdownUrl.'?get=seo_url_link_type',"156"),"133px");
        return $f;
    }
}

?>