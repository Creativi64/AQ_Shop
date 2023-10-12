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

class ProductFilter extends formFilter {
    

    public static function  formFields(){
    	global $xtPlugin;
    	
        $eF = new ExtFunctions();
        
        $filter_product_name = PhpExt_Form_TextField::createTextField("filter_product_name",ucfirst(TEXT_NAME))->setEmptyText(TEXT_INCLUDES);
        if ($_SESSION['filters_product']['filter_product_name'] != ""){
            $filter_product_name->setValue($_SESSION['filters_product']['filter_product_name']);
        }
        $f[] = self::setWidth($filter_product_name, "150px");
		
        $filter_product_name = PhpExt_Form_TextField::createTextField("filter_products_model",ucfirst(TEXT_PRODUCTS_MODEL))->setEmptyText(TEXT_INCLUDES);
        if ($_SESSION['filters_product']['filter_products_model'] != ""){
            $filter_product_name->setValue($_SESSION['filters_product']['filter_products_model']);
        }
        $f[] = self::setWidth($filter_product_name, "150px");
		
		if(isset($_GET['parentNode'])) {
			$catst = explode("catst_",$_GET['parentNode']);
			$store_cat_id = '';
			if ($catst[1]) 
				$store_cat_id = 'catst_'.$catst[1];
		}
        $itemsPerPage = _SYSTEM_ADMIN_PAGE_SIZE;
        $filter_items_per_page = PhpExt_Form_NumberField::createNumberField("filter_items_per_page",ucfirst(TEXT_ITEMS_PER_PAGE), 'filter_items_per_page_' . __CLASS__ .'_product'.$_REQUEST['catID'].$store_cat_id)->setValue($itemsPerPage);
        if ($_SESSION['filters_product']['filter_items_per_page'] != ""){
            $filter_items_per_page->setValue($_SESSION['filters_product']['filter_items_per_page']);
        }
        $f[] = self::setWidth($filter_items_per_page, "150px");
        
        $f1 = PhpExt_Form_NumberField::createNumberField("filter_price_from",ucfirst(TEXT_PRODUCTS_PRICE))->setEmptyText(TEXT_FROM); 
        $f2 = PhpExt_Form_NumberField::createNumberField("filter_price_to","")->setEmptyText(TEXT_TO);   
        if ($_SESSION['filters_product']['filter_price_from'] != ""){
            $f1->setValue($_SESSION['filters_product']['filter_price_from']);
        }
        if ($_SESSION['filters_product']['filter_price_to'] != ""){
            $f2->setValue($_SESSION['filters_product']['filter_price_to']);
        }
        $f[] = self::twoCol($f1, $f2);
        
        $combo = $eF->_comboBox('filter_manufacturer', ucfirst(FEED_MANUFACTURER),self::$dropdownUrl.'?get=manufacturers',"156");
        if ($_SESSION['filters_product']['filter_manufacturer'] != ""){
            $combo->setValue($_SESSION['filters_product']['filter_manufacturer']);
        }
        $f[] = self::setWidth($combo,"133px");
		
        $combo = $eF->_comboBox('filter_products_status', ucfirst(TEXT_ADMIN_ACTON_STATUS),self::$dropdownUrl.'?get=status_product',"156");
        if ($_SESSION['filters_product']['filter_products_status'] != ""){
            $combo->setValue($_SESSION['filters_product']['filter_products_status']);
        }
		$f[]  =self::setWidth($combo,"133px");

        if (isset($xtPlugin->active_modules['xt_master_slave']))
        {
            $combo = $eF->_comboBox('filter_master_slave_products', ucfirst(TEXT_FILTER_MASTER_SLAVE),self::$dropdownUrl.'?get=master_slave_product',"156");
            if ($_SESSION['filters_product']['filter_master_slave_products'] != ""){
                $combo->setValue($_SESSION['filters_product']['filter_master_slave_products']);
            }
            $f[]  =self::setWidth($combo,"133px");
        }
        
        $nf = PhpExt_Form_NumberField::createNumberField("filter_stock_from",ucfirst(TEXT_PRODUCTS_QUANTITY));
        if ($_SESSION['filters_product']['filter_stock_from'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_stock_from']);
        }
        $a1 = self::setWidth($nf)->setEmptyText(TEXT_FROM);
        $nf = PhpExt_Form_NumberField::createNumberField("filter_stock_to",ucfirst(TEXT_TO));
        if ($_SESSION['filters_product']['filter_stock_to'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_stock_to']);
        }
        $a2 = $nf->setEmptyText(TEXT_TO);
        $a[] = self::twoCol2($a1, $a2);
		
        $nf = PhpExt_Form_NumberField::createNumberField("filter_weight_from",ucfirst(TEXT_PRODUCTS_WEIGHT));
        if ($_SESSION['filters_product']['filter_weight_from'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_weight_from']);
        }
		$a1 = self::setWidth($nf)->setEmptyText(TEXT_FROM);
        $nf = PhpExt_Form_NumberField::createNumberField("filter_weight_to","");
        if ($_SESSION['filters_product']['filter_weight_to'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_weight_to']);
        }
        $a2 = $nf->setEmptyText(TEXT_TO);
        $a[] = self::twoCol2($a1, $a2);

        $cb = PhpExt_Form_Checkbox::createCheckbox("filter_isDigital","<nobr>".ucfirst(TEXT_PRODUCTS_DIGITAL)."</nobr>");
        if ($_SESSION['filters_product']['filter_isDigital'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_isDigital']);
        }
        $a[] = $cb;
        
        $cb = PhpExt_Form_Checkbox::createCheckbox("filter_isFSK18",ucfirst(TEXT_PRODUCTS_FSK18));
        if ($_SESSION['filters_product']['filter_isFSK18'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_isFSK18']);
        }
        $a[] = $cb;

        global $store_handler;
        $stores = $store_handler->getStores();
        if(count($stores)>1)
        {
            $cb = $eF->_comboBox('filter_shop', ucfirst(TEXT_STORE), self::$dropdownUrl . '?get=stores', "156");
            if ($_SESSION['filters_product']['filter_shop'] != ""){
                $nf->setValue($_SESSION['filters_product']['filter_shop']);
        }
            $a[] = self:: setWidth($cb, "133px");
        }

        $cb = $eF->_comboBox('filter_permission', ucfirst(TEXT_PERMISSION), self::$dropdownUrl.'?get=customers_status',"156");
        if ($_SESSION['filters_product']['filter_permission'] != ""){
            $nf->setValue($_SESSION['filters_product']['filter_permission']);
        }
        $a[] = self:: setWidth($cb, "133px");
       	
        ($plugin_code = $xtPlugin->PluginCode('class.productFilter.php:formFields_bottom')) ? eval($plugin_code) : false;
        
        foreach($a as $field){
            $f[] = $field;
        }
     
       return $f;
    }
    
  
    

    
    private static function twoCol2($f1, $f2){
        
        $columnPanel = new PhpExt_Panel();
        $columnPanel->setLayout(new PhpExt_Layout_ColumnLayout())->setWidth("268px")->setBorder(false);
        $firstColumn = new PhpExt_Panel();
        $firstColumn->setLayout(new PhpExt_Layout_FormLayout())->setBorder(false);
        $firstColumn->addItem(
	            $f1 ->setWidth("62px")
	          );
        
        $secondColumn = new PhpExt_Panel();
        $secondColumn->setLayout(new PhpExt_Layout_FormLayout())->setBorder(false);
        $secondColumn->addItem(
	            $f2->setHideLabel(true)->setWidth("62px")
               
              
	          );
        $columnPanel->addItem($firstColumn, new PhpExt_Layout_ColumnLayoutData(0.70));
        $columnPanel->addItem($secondColumn, new PhpExt_Layout_ColumnLayoutData(0.30));
        
        return $columnPanel;
    }
    
}
?>