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


defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Hier werden alle SOAP Datentypen zum kapseln aller Daten zusammengefasst
 */
class SoapTypes {

    function __construct() {
        
    }

    /**
     * Alle SOAP Datentypen als array zurückliefern
     * @global type $language
     * @global type $db
     * @return array
     */
    static function getTypesArray() {
        global $language, $db,$xtPlugin;

        // load languages
        $_lng = array();
        
        // wenn Parameter ALL_LANGS gesetzt, dann werden alle möglichen Sprachcodes gesetzt, auch wenn sie in XT NICHT genutzt werden
        // Beispiel: http://localhost/shop/index.php?page=xt_soap&wsdl&ALL_LANGS
        if(isset($_GET['ALL_LANGS'])){
            $rs = $db->Execute("SELECT countries_iso_code_2 as iso2 FROM ".TABLE_COUNTRIES." ");
            while (!$rs->EOF) {
	        $_lng[ strtolower( $rs->fields['iso2'] ) ]=array('name'=>$val['code'],'type'=>'xsd:string');
                $rs->MoveNext();
            }
            
            // Sprachcode "en" könnte fehlen!
            // Nachtragen
            $_lng[ "en" ]=array('name'=>'en','type'=>'xsd:string');
            
        }
        else{

            foreach (SoapHelper::getLanguageList() as $key => $val) {
                $_lng[$val['code']] = array('name' => $val['code'], 'type' => 'xsd:string');
            }
        }

        // load countries
        $_cntr = array();
        $rs = $db->Execute("SELECT countries_iso_code_2 as iso2 FROM " . TABLE_COUNTRIES . " WHERE status='1'");
        $_ctry = array();
        while (!$rs->EOF) {

            $rs->MoveNext();
        }


        $aTypesArray = array(
            array(//begin- arrayOfInt
                'name' => 'arrayOfInt',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:int[]')),
                'arrayType' => 'xsd:int'), //end -arrayOfInt
            // string array defs
            array(//begin- arrayOfString
                'name' => 'arrayOfString',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:string[]')),
                'arrayType' => 'xsd:string'), //end -arrayOfString
            // string array defs
            array(//begin- arrayOfString
                'name' => 'arrayOfDecimal',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:decimal[]')),
                'arrayType' => 'xsd:decimal'), //end -arrayOfString
            // user defned complex types
            # language items  
            array(//begin- langItem 
                'name' => 'langItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => $_lng,
                'attrs' => array(),
                'arrayType' => ''), //end - langItem		
            array(//begin- productResultItem base
                'name' => 'productResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'), // 2013-01-06: Hinzugefügt
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - productResultItem base
            array(// begin- productsResultItemList
                'name' => 'productsResultItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productResultItem[]')),
                'arrayType' => 'tns:productResultItem'), //end- productsResultItemList
            array( // Rückgabe von getArticle
                'name' => 'getArticleResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'productsItemExport' => array('name' => 'productsItemExport', 'type' => 'tns:productsItemExport'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), 
            array( // Rückgabe von getArticles
                'name' => 'getArticlesResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'productsItemExportList' => array('name' => 'productsItemExportList', 'type' => 'tns:productsItemExportList'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''),
            array(/* Produkt Export getArticle */
                'name' => 'productsItemExport',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'permission_id' => array('name' => 'permission_id', 'type' => 'xsd:int'),
                    'products_owner' => array('name' => 'products_owner', 'type' => 'xsd:int'),
                    'products_ean' => array('name' => 'products_ean', 'type' => 'xsd:string'),
                    'products_quantity' => array('name' => 'products_quantity', 'type' => 'xsd:decimal'),
                    'products_average_quantity' => array('name' => 'products_average_quantity', 'type' => 'xsd:int'),
                    'products_shippingtime' => array('name' => 'products_shippingtime', 'type' => 'xsd:int'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'price_flag_graduated_all' => array('name' => 'price_flag_graduated_all', 'type' => 'xsd:int'),
                    'price_flag_graduated_1' => array('name' => 'price_flag_graduated_1', 'type' => 'xsd:int'),
                    'price_flag_graduated_2' => array('name' => 'price_flag_graduated_2', 'type' => 'xsd:int'),
                    'price_flag_graduated_3' => array('name' => 'price_flag_graduated_3', 'type' => 'xsd:int'),
                    'products_sort' => array('name' => 'products_sort', 'type' => 'xsd:int'),
                    'products_option_master_price' => array('name' => 'products_option_master_price', 'type' => 'xsd:string'),
                    'ekomi_allow' => array('name' => 'ekomi_allow', 'type' => 'xsd:int'),
                    'products_image' => array('name' => 'products_image', 'type' => 'xsd:string'),
                    'products_price' => array('name' => 'products_price', 'type' => 'xsd:decimal'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'date_available' => array('name' => 'date_available', 'type' => 'xsd:dateTime'),
                    'products_weight' => array('name' => 'products_weight', 'type' => 'xsd:decimal'),
                    'products_status' => array('name' => 'products_status', 'type' => 'xsd:int'),
                    'products_tax_class_id' => array('name' => 'products_tax_class_id', 'type' => 'xsd:int'),
                    'product_template' => array('name' => 'product_template', 'type' => 'xsd:string'),
                    'product_list_template' => array('name' => 'product_list_template', 'type' => 'xsd:string'),
                    'products_option_template' => array('name' => 'products_option_template', 'type' => 'xsd:string'),// 2017-02-28: Neues Feld products_option_template
                    'products_option_list_template' => array('name' => 'products_option_list_template', 'type' => 'xsd:string'),// 2017-02-28: Neues Feld products_option_list_template
                    'total_downloads' => array('name' => 'total_downloads', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld total_downloads
                    'products_canonical_master' => array('name' => 'products_canonical_master', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld products_canonical_master
                    'group_discount_allowed' => array('name' => 'group_discount_allowed', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld group_discount_allowed
                    'google_product_cat' => array('name' => 'google_product_cat', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld google_product_cat
                    'manufacturers_int_id' => array('name' => 'manufacturers_int_id', 'type' => 'xsd:int'),
                    'products_ordered' => array('name' => 'products_ordered', 'type' => 'xsd:int'),
                    'products_transactions' => array('name' => 'products_transactions', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld products_transactions
                    'products_fsk18' => array('name' => 'products_fsk18', 'type' => 'xsd:int'),
                    'products_vpe' => array('name' => 'products_vpe', 'type' => 'xsd:int'),
                    'products_unit' => array('name' => 'products_unit', 'type' => 'xsd:int'),
                    'products_vpe_status' => array('name' => 'products_vpe_status', 'type' => 'xsd:int'),
                    'products_vpe_value' => array('name' => 'products_vpe_value', 'type' => 'xsd:decimal'),
                    'products_startpage' => array('name' => 'products_startpage', 'type' => 'xsd:int'),
                    'products_startpage_sort' => array('name' => 'products_startpage_sort', 'type' => 'xsd:int'),
                    'products_average_rating' => array('name' => 'products_average_rating', 'type' => 'xsd:decimal'),
                    'products_rating_count' => array('name' => 'products_rating_count', 'type' => 'xsd:int'),
                    'products_digital' => array('name' => 'products_digital', 'type' => 'xsd:int'),
                    'flag_has_specials' => array('name' => 'flag_has_specials', 'type' => 'xsd:int'),
                    'products_serials' => array('name' => 'products_serials', 'type' => 'xsd:int'),
                    // master slave
                    'products_master_flag' => array('name' => 'products_master_flag', 'type' => 'xsd:int'), //typ geändert von string auf int J/N Problem
                    'products_image_from_master' => array('name' => 'products_image_from_master', 'type' => 'xsd:int'), 
                    'products_master_model' => array('name' => 'products_master_model', 'type' => 'xsd:string'),
                    'products_master_slave_order' => array('name' => 'products_master_slave_order', 'type' => 'xsd:int'), // neu - 2016-9-01
                    'products_description_from_master' => array('name' => 'products_description_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'products_short_description_from_master' => array('name' => 'products_short_description_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'products_keywords_from_master' => array('name' => 'products_keywords_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_load_masters_main_img' => array('name' => 'ms_load_masters_main_img', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_load_masters_free_downloads' => array('name' => 'ms_load_masters_free_downloads', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_open_first_slave' => array('name' => 'ms_open_first_slave', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_show_slave_list' => array('name' => 'ms_show_slave_list', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_filter_slave_list' => array('name' => 'ms_filter_slave_list', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    // description
                    'products_keywords' => array('name' => 'products_keywords', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'products_description' => array('name' => 'products_description', 'type' => 'tns:langItem'),  // typ geändert von string[] zu langItem!
                    'products_short_description' => array('name' => 'products_short_description', 'type' => 'tns:langItem'),  // neu - 2012-11-05
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'seo_url' => array('name' => 'url', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'url' => array('name' => 'url', 'type' => 'tns:langItem'), // Neu 2012-11-06
                    'products_special_prices' => array('name' => 'products_special_prices', 'type' => 'tns:specialpriceList'),
                    'products_name' => array('name' => 'products_name', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'categories' => array('name' => 'categories', 'type' => 'tns:arrayOfInt'),
                    'image_name' => array('name' => 'image_name', 'type' => 'xsd:string'), // name des Bildes
                    'image' => array('name' => 'image', 'type' => 'xsd:string'), // daten
                    'products_prices' => array('name' => 'products_prices', 'type' => 'tns:pricesetList'), // Staffelpreise
                    'products_cross_sell' => array('name' => 'products_cross_sell', 'type' => 'tns:arrayOfString'), 
                    'products_images' => array('name' => 'products_images', 'type' => 'tns:products_imageList'),
                    'products_categories' => array('name' => 'products_categories', 'type' => 'tns:arrayOfInt'),
                    'products_attributes' => array('name' => 'products_attributes', 'type' => 'tns:products_attributes_idsList'), 
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'), // Garcia Neu: 2012-11-15
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'), // Garcia Neu: 2012-12-17: Kapselt Inidividualfelder / Optional
                    'products_media_urlList' => array('name' => 'products_media_urlList', 'type' => 'tns:products_media_urlList'),
                    'mediaItemXTLinkList' => array('name' => 'mediaItemXTLinkList', 'type' => 'tns:mediaItemXTLinkList'), // 2014-07-23 Neu: Container für mediaIds 
                    'productDescriptionsList' => array('name' => 'productDescriptionsList', 'type' => 'tns:productDescriptionsList'),// 2014-12-01 Neu: Container für descriptions pro store - wenn vorhanden sticht description block in hauptarray
                    'products_startpageList' => array('name' => 'products_startpageList', 'type' => 'tns:products_startpageList'),// 2014-12-03 Neu: Container für startpages pro store - wenn vorhanden sticht startpage block in hauptarray
                    'productCategoriesList' => array('name' => 'productCategoriesList', 'type' => 'tns:productCategoriesList'),// 2015-02-27 Neu: Container für categories pro store
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - productsItem base

            /* getArticles */
            array(
                'name' => 'productsItemExportList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productsItemExport[]')),
                'arrayType' => 'tns:productsItemExport'), 
            array(
                'name' => 'products_media_url',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'type' => array('name' => 'type', 'type' => 'xsd:string'), 
                    'file' => array('name' => 'file', 'type' => 'xsd:string'), 
                    'width' => array('name' => 'width', 'type' => 'xsd:string'),
                    'height' => array('name' => 'height', 'type' => 'xsd:string'),
                    'folder' => array('name' => 'folder', 'type' => 'xsd:string'),
                    'url' => array('name' => 'url', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(
                'name' => 'products_media_urlList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_media_url[]')),
                'arrayType' => 'tns:products_media_url'),            
            /* xtcommerce structure */
            array(//begin- productsItem base
                'name' => 'productsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'permission_id' => array('name' => 'permission_id', 'type' => 'xsd:int'),
                    'products_owner' => array('name' => 'products_owner', 'type' => 'xsd:int'),
                    'products_ean' => array('name' => 'products_ean', 'type' => 'xsd:string'),
                    'products_quantity' => array('name' => 'products_quantity', 'type' => 'xsd:decimal'),
                    'products_average_quantity' => array('name' => 'products_average_quantity', 'type' => 'xsd:int'),
                    'products_shippingtime' => array('name' => 'products_shippingtime', 'type' => 'xsd:int'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'price_flag_graduated_all' => array('name' => 'price_flag_graduated_all', 'type' => 'xsd:int'),
                    'price_flag_graduated_1' => array('name' => 'price_flag_graduated_1', 'type' => 'xsd:int'),
                    'price_flag_graduated_2' => array('name' => 'price_flag_graduated_2', 'type' => 'xsd:int'),
                    'price_flag_graduated_3' => array('name' => 'price_flag_graduated_3', 'type' => 'xsd:int'),
                    'products_sort' => array('name' => 'products_sort', 'type' => 'xsd:int'),
                    'products_option_master_price' => array('name' => 'products_option_master_price', 'type' => 'xsd:string'),
                    'ekomi_allow' => array('name' => 'ekomi_allow', 'type' => 'xsd:int'),
                    'products_image' => array('name' => 'products_image', 'type' => 'xsd:string'),
                    'products_price' => array('name' => 'products_price', 'type' => 'xsd:decimal'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'date_available' => array('name' => 'date_available', 'type' => 'xsd:dateTime'),
                    'products_weight' => array('name' => 'products_weight', 'type' => 'xsd:decimal'),
                    'products_status' => array('name' => 'products_status', 'type' => 'xsd:int'),
                    'products_tax_class_id' => array('name' => 'products_tax_class_id', 'type' => 'xsd:int'),
                    'product_template' => array('name' => 'product_template', 'type' => 'xsd:string'),
                    'product_list_template' => array('name' => 'product_list_template', 'type' => 'xsd:string'),
                    'products_option_template' => array('name' => 'products_option_template', 'type' => 'xsd:string'),// 2017-03-03: Neues Feld products_option_template
                    'products_option_list_template' => array('name' => 'products_option_list_template', 'type' => 'xsd:string'),// 2017-03-03: Neues Feld products_option_list_template
                    'total_downloads' => array('name' => 'total_downloads', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld total_downloads
                    'products_canonical_master' => array('name' => 'products_canonical_master', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld products_canonical_master
                    'group_discount_allowed' => array('name' => 'group_discount_allowed', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld group_discount_allowed
                    'google_product_cat' => array('name' => 'google_product_cat', 'type' => 'xsd:int'),// 2017-02-28: Neues Feld google_product_cat
                    'manufacturers_ext_id' => array('name' => 'manufacturers_ext_id', 'type' => 'xsd:string'),
                    'manufacturers_int_id' => array('name' => 'manufacturers_int_id', 'type' => 'xsd:int'),
                    'products_image_from_master' => array('name' => 'products_image_from_master', 'type' => 'xsd:int'),
                    'products_unit' => array('name' => 'products_unit', 'type' => 'xsd:int'),
                    'products_ordered' => array('name' => 'products_ordered', 'type' => 'xsd:int'),
                    'products_transactions' => array('name' => 'products_transactions', 'type' => 'xsd:int'),// 2017-03-03: Neues Feld products_transactions
                    'products_fsk18' => array('name' => 'products_fsk18', 'type' => 'xsd:int'),
                    'products_vpe' => array('name' => 'products_vpe', 'type' => 'xsd:int'),
                    'products_vpe_status' => array('name' => 'products_vpe_status', 'type' => 'xsd:int'),
                    'products_vpe_value' => array('name' => 'products_vpe_value', 'type' => 'xsd:decimal'),
                    'products_startpage' => array('name' => 'products_startpage', 'type' => 'xsd:int'),
                    'products_startpage_sort' => array('name' => 'products_startpage_sort', 'type' => 'xsd:int'),
                    'products_average_rating' => array('name' => 'products_average_rating', 'type' => 'xsd:decimal'),
                    'products_rating_count' => array('name' => 'products_rating_count', 'type' => 'xsd:int'),
                    'products_digital' => array('name' => 'products_digital', 'type' => 'xsd:int'),
                    'flag_has_specials' => array('name' => 'flag_has_specials', 'type' => 'xsd:int'),
                    'products_serials' => array('name' => 'products_serials', 'type' => 'xsd:int'),
                    'image_name' => array('name' => 'image_name', 'type' => 'xsd:string'), // name des Bildes
                    'image' => array('name' => 'image', 'type' => 'xsd:string'), // daten
                    'products_master_flag' => array('name' => 'products_master_flag', 'type' => 'xsd:int'), //typ geändert von string zu int
                    'products_master_model' => array('name' => 'products_master_model', 'type' => 'xsd:string'),
                    'products_master_slave_order' => array('name' => 'products_master_slave_order', 'type' => 'xsd:int'), // neu - 2016-9-01
                    'products_description_from_master' => array('name' => 'products_description_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'products_short_description_from_master' => array('name' => 'products_short_description_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'products_keywords_from_master' => array('name' => 'products_keywords_from_master', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_load_masters_main_img' => array('name' => 'ms_load_masters_main_img', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_load_masters_free_downloads' => array('name' => 'ms_load_masters_free_downloads', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_open_first_slave' => array('name' => 'ms_open_first_slave', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_show_slave_list' => array('name' => 'ms_show_slave_list', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'ms_filter_slave_list' => array('name' => 'ms_filter_slave_list', 'type' => 'xsd:int'), // 2018-06-26: NEU
                    'products_name' => array('name' => 'products_name', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'products_keywords' => array('name' => 'products_keywords', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'products_description' => array('name' => 'products_description', 'type' => 'tns:langItem'),  // typ geändert von string[] zu langItem!
                    'products_short_description' => array('name' => 'products_short_description', 'type' => 'tns:langItem'),  // neu - 2012-11-05
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),// typ geändert von string[] zu langItem!
                    'seo_url' => array('name' => 'url', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'url' => array('name' => 'url', 'type' => 'tns:langItem'), // Neu 2012-11-06
                    'products_special_prices' => array('name' => 'products_special_prices', 'type' => 'tns:specialpriceList'), // Sonderpreise
                    'products_prices' => array('name' => 'products_prices', 'type' => 'tns:pricesetList'), // // Staffelpreise
                    'products_cross_sell' => array('name' => 'products_cross_sell', 'type' => 'tns:arrayOfString'),
                    'products_images' => array('name' => 'products_images', 'type' => 'tns:products_imageList'),
                    'products_categories' => array('name' => 'products_categories', 'type' => 'tns:arrayOfInt'),
                    'products_external_categories' => array('name' => 'products_categories', 'type' => 'tns:arrayOfString'),
                    'products_attributes' => array('name' => 'products_attributes', 'type' => 'tns:products_attributes_idsList'), // Garcia Neu: 2013-02-19
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'), // Garcia Neu: 2012-11-15
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// Garcia Neu: 2012-12-17: Kapselt Inidividualfelder / Optional
                    'mediaItemXTLinkList' => array('name' => 'mediaItemXTLinkList', 'type' => 'tns:mediaItemXTLinkList'),
                    'productDescriptionsList' => array('name' => 'productDescriptionsList', 'type' => 'tns:productDescriptionsList'), // Container für descriptions pro store - wenn vorhanden sticht description block in hauptarray
                    'products_startpageList' => array('name' => 'products_startpageList', 'type' => 'tns:products_startpageList'), // Container für startpages pro store - wenn vorhanden sticht startpage block in hauptarray
                    'productCategoriesList' => array('name' => 'productCategoriesList', 'type' => 'tns:productCategoriesList'), // Container für categories pro store
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - productsItem base
            array(// begin- productsItemList
                'name' => 'productsItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productsItem[]')),
                'arrayType' => 'tns:productsItem'), //end- productsItemList
            array( 
                'name' => 'products_startpageList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_startpageItem[]')),
                'arrayType' => 'tns:products_startpageItem'), 
            array(
                'name' => 'products_startpageItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_store_id' => array('name' => 'products_store_id', 'type' => 'xsd:int'),
                    'products_startpage' => array('name' => 'products_startpage', 'type' => 'xsd:int'),
                    'products_startpage_sort' => array('name' => 'products_startpage_sort', 'type' => 'xsd:int'),
                    
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),          
           array( // Liste und Daten für products descriptions pro shop
                'name' => 'productDescriptionsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productDescriptionsItem[]')),
                'arrayType' => 'tns:productDescriptionsItem'),
            array(
                'name' => 'productDescriptionsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_store_id' => array('name' => 'products_store_id', 'type' => 'xsd:int'),
                    'products_name' => array('name' => 'products_name', 'type' => 'tns:langItem'), 
                    'products_keywords' => array('name' => 'products_keywords', 'type' => 'tns:langItem'), 
                    'products_description' => array('name' => 'products_description', 'type' => 'tns:langItem'),  
                    'products_short_description' => array('name' => 'products_short_description', 'type' => 'tns:langItem'), 
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'seo_url' => array('name' => 'seo_url', 'type' => 'tns:langItem'), 
                    'url' => array('name' => 'url', 'type' => 'tns:langItem'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
           array( // Liste und Daten für products categories mit store_ids
                'name' => 'productCategoriesList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productCategoriesItem[]')),
                'arrayType' => 'tns:productCategoriesItem'), 
            array(
                'name' => 'productCategoriesItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_store_id' => array('name' => 'products_store_id', 'type' => 'xsd:int'),
                    'products_categories' => array('name' => 'products_categories', 'type' => 'tns:arrayOfInt'),
                    'products_external_categories' => array('name' => 'products_categories', 'type' => 'tns:arrayOfString'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array( // Fasst ein Attribut mit Attribute ID und der Parent ID zusamme
                'name' => 'products_attributes_ids',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'attributes_id' => array('name' => 'attributes_id', 'type' => 'xsd:int'), // Attribut ID
                    'attributes_parent_id' => array('name' => 'attributes_parent_id', 'type' => 'xsd:int'), // Attribut Parent ID
                    'external_attributes_id' => array('name' => 'external_attributes_id', 'type' => 'xsd:int'), // externe Attribut ID
                    'external_attributes_parent_id' => array('name' => 'external_attributes_parent_id', 'type' => 'xsd:int'), // externe Attribut Parent ID
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array( // Liste kapselt Array von products_attributes_ids
                'name' => 'products_attributes_idsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_attributes_ids[]')),
                'arrayType' => 'tns:products_attributes_ids'),            
           array( // Individualfelder kapseln in Liste von einzlenen Individualfelder Obj
                'name' => 'indivFieldsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:indivField[]')),
                'arrayType' => 'tns:indivField'), 
            array( // Einzelnes Individualfeld
                'name' => 'indivField',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'dstTable' => array('name' => 'dstTable', 'type' => 'xsd:string'), // Zieltabelle
                    'sqlFieldName' => array('name' => 'sqlFieldName', 'type' => 'xsd:string'), // Name von SQL Feld in Tabelle
                    'sqlFieldType' => array('name' => 'sqlFieldType', 'type' => 'xsd:string'), // Typ von SQL Feld in Tabelle
                    'value' => array('name' => 'value', 'type' => 'xsd:string'), // Wert von Feld als String
                    'langValue' => array('name' => 'langValue', 'type' => 'tns:langItem'), // Wert von Feld als langItem für MultiSprachenfeld
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array( // Sonderpreis Liste
                'name' => 'specialpriceList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:specialprice[]')),
                'arrayType' => 'tns:specialprice'),
            array(
                'name' => 'specialprice',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'special_price' => array('name' => 'price', 'type' => 'xsd:decimal'), // 
                    'status' => array('name' => 'status', 'type' => 'xsd:int'),
                    'date_available' => array('name' => 'date_available', 'type' => 'xsd:dateTime'),
                    'date_expired' => array('name' => 'date_available', 'type' => 'xsd:dateTime'),
                    'group_permissions' => array('name' => 'group_permissions', 'type' => 'tns:arrayOfInt'),
                    'group_permission_all' => array('name' => 'group_permission_all', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),          
            array(// begin- pricesetList
                'name' => 'pricesetList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:priceset[]')),
                'arrayType' => 'tns:priceset'), //end- pricesetList
            array(//begin- customer_dataItem base (BUEROWARE spezifisch)
                'name' => 'priceset',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'price' => array('name' => 'price', 'type' => 'xsd:decimal'), // Preis als String für kompatibl. zur alten Schnittstelle
                    'staffel' => array('name' => 'staffel', 'type' => 'xsd:int'), // Preis-Staffel 1-4
                    'group' => array('name' => 'group', 'type' => 'xsd:string'), // Gruppe zB VK1 - VK6
                    'quantity' => array('name' => 'quantity', 'type' => 'xsd:int'), // Ab Welcher Mege zählt diese Staffel?
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(//begin- productsStockItem base
                'name' => 'productsStockItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'products_quantity' => array('name' => 'products_quantity', 'type' => 'xsd:decimal'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - productsStockItem base
            array(// begin- productsStockList
                'name' => 'productsStockList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productsStockItem[]')),
                'arrayType' => 'tns:productsStockItem'), //end- productsStockList
            array(//begin- products_cross_sellItem base
                'name' => 'products_cross_sellItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' =>
                array(
                    'products_id_cross_sell' => array('name' => 'products_id_cross_sell', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - products_cross_sellItem base
            array(// begin- products_cross_sellList
                'name' => 'products_cross_sellList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_cross_sellItem[]')),
                'arrayType' => 'tns:products_cross_sellItem'), //end- products_cross_sellList
            array(//begin- products_imageItem base
                'name' => 'products_imageItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' =>
                array(
                    'type' => array('name' => 'type', 'type' => 'xsd:string'),
                    'id' => array('name' => 'id', 'type' => 'xsd:string'),
                    'image_name' => array('name' => 'image_name', 'type' => 'xsd:string'),
                    'image_data' => array('name' => 'image_data', 'type' => 'xsd:base64Binary'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - products_imageItem base
            array(//begin- products_priceItem base
                'name' => 'products_priceItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' =>
                array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'group' => array('name' => 'group', 'type' => 'xsd:string'),
                    'discount_quantity' => array('name' => 'discount_quantity', 'type' => 'xsd:int'),
                    'price' => array('name' => 'price', 'type' => 'xsd:decimal'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - products_priceItem base
            array(// begin- products_priceList
                'name' => 'products_priceList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_priceItem[]')),
                'arrayType' => 'tns:products_priceItem'), //end- products_priceList
            array(// begin- products_imageList
                'name' => 'products_imageList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:products_imageItem[]')),
                'arrayType' => 'tns:products_imageItem'), 
            array(//begin- productsImageItem base
                'name' => 'productImages',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' =>
                array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'image_name' => array('name' => 'image_name', 'type' => 'xsd:string'), // name des Bildes
                    'image' => array('name' => 'image', 'type' => 'xsd:base64Binary'), // daten
                    'image_hash' => array('name' => 'image_hash', 'type' => 'xsd:string'), // Hash des gesendeten Bildes
                    'image_url' => array('name' => 'image_path', 'type' => 'xsd:string'), // URL zum Bild
                    'products_images' => array('name' => 'products_images', 'type' => 'tns:productImagesList'), // weitere bilder
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - products_priceItem base
            array(// 
                'name' => 'productImagesList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:productImages[]')),
                'arrayType' => 'tns:productImages'), 
            array(//begin- ordersItem base
                'name' => 'ordersItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'orders_id' => array('name' => 'orders_id', 'type' => 'xsd:int'),
                    'customers_id' => array('name' => 'customers_id', 'type' => 'xsd:int'),
                    'customers_cid' => array('name' => 'customers_cid', 'type' => 'xsd:string'),                    
                    'customers_external_id' => array('name' => 'customers_external_id', 'type' => 'xsd:string'), // 2013-01-07: Neu
                    'customers_vat_id' => array('name' => 'customers_vat_id', 'type' => 'xsd:string'),
                    'customers_status' => array('name' => 'customers_status', 'type' => 'xsd:int'),
                    'customers_status_show_price_tax' => array('name' => 'customers_status_show_price_tax', 'type' => 'xsd:int'),
                    'customers_status_add_tax_ot' => array('name' => 'customers_status_add_tax_ot', 'type' => 'xsd:int'),
                    'customers_email_address' => array('name' => 'customers_email_address', 'type' => 'xsd:string'),
                    'delivery_gender' => array('name' => 'delivery_gender', 'type' => 'xsd:string'),
                    'delivery_title' => array('name' => 'delivery_title', 'type' => 'xsd:string'), // 2018-07-12 Neu
                    'delivery_phone' => array('name' => 'delivery_phone', 'type' => 'xsd:string'),
                    'delivery_mobile_phone' => array('name' => 'delivery_mobile_phone', 'type' => 'xsd:string'),
                    'delivery_fax' => array('name' => 'delivery_fax', 'type' => 'xsd:string'),
                    'delivery_firstname' => array('name' => 'delivery_firstname', 'type' => 'xsd:string'),
                    'delivery_lastname' => array('name' => 'delivery_lastname', 'type' => 'xsd:string'),
                    'delivery_company' => array('name' => 'delivery_company', 'type' => 'xsd:string'),
                    'delivery_company_2' => array('name' => 'delivery_company_2', 'type' => 'xsd:string'),
                    'delivery_company_3' => array('name' => 'delivery_company_3', 'type' => 'xsd:string'),
                    'delivery_street_address' => array('name' => 'delivery_street_address', 'type' => 'xsd:string'),
                    'delivery_address_addition' => array('name' => 'delivery_address_addition', 'type' => 'xsd:string'), // 2018-07-12 NEU
                    'delivery_suburb' => array('name' => 'delivery_suburb', 'type' => 'xsd:string'),
                    'delivery_city' => array('name' => 'delivery_city', 'type' => 'xsd:string'),
                    'delivery_postcode' => array('name' => 'delivery_postcode', 'type' => 'xsd:string'),
                    'delivery_zone' => array('name' => 'delivery_zone', 'type' => 'xsd:string'),
                    'delivery_zone_code' => array('name' => 'delivery_zone_code', 'type' => 'xsd:string'),
                    'delivery_country' => array('name' => 'delivery_country', 'type' => 'xsd:string'),
                    'delivery_country_code' => array('name' => 'delivery_country_code', 'type' => 'xsd:string'),
                    'delivery_federal_state_code' => array('name' => 'delivery_federal_state_code', 'type' => 'xsd:int'), // 2017-03-03: Neu
                    'delivery_federal_state_code_iso' => array('name' => 'delivery_federal_state_code_iso', 'type' => 'xsd:string'), // 2017-03-03: Neu
                    'billing_gender' => array('name' => 'billing_gender', 'type' => 'xsd:string'),
                    'billing_title' => array('name' => 'billing_title', 'type' => 'xsd:string'), // 2018-07-12 Neu
                    'billing_phone' => array('name' => 'billing_phone', 'type' => 'xsd:string'),
                    'billing_mobile_phone' => array('name' => 'billing_mobile_phone', 'type' => 'xsd:string'),
                    'billing_fax' => array('name' => 'billing_fax', 'type' => 'xsd:string'),
                    'billing_firstname' => array('name' => 'billing_firstname', 'type' => 'xsd:string'),
                    'billing_lastname' => array('name' => 'billing_lastname', 'type' => 'xsd:string'),
                    'billing_company' => array('name' => 'billing_company', 'type' => 'xsd:string'),
                    'billing_company_2' => array('name' => 'billing_company_2', 'type' => 'xsd:string'),
                    'billing_company_3' => array('name' => 'billing_company_3', 'type' => 'xsd:string'),
                    'billing_street_address' => array('name' => 'billing_street_address', 'type' => 'xsd:string'),
                    'billing_address_addition' => array('name' => 'billing_address_addition', 'type' => 'xsd:string'), // 2018-07-12 NEU
                    'billing_suburb' => array('name' => 'billing_suburb', 'type' => 'xsd:string'),
                    'billing_city' => array('name' => 'billing_city', 'type' => 'xsd:string'),
                    'billing_postcode' => array('name' => 'billing_postcode', 'type' => 'xsd:string'),
                    'billing_zone' => array('name' => 'billing_zone', 'type' => 'xsd:string'),
                    'billing_zone_code' => array('name' => 'billing_zone_code', 'type' => 'xsd:string'),
                    'billing_country' => array('name' => 'billing_country', 'type' => 'xsd:string'),
                    'billing_country_code' => array('name' => 'billing_country_code', 'type' => 'xsd:string'),
                    'billing_federal_state_code' => array('name' => 'billing_federal_state_code', 'type' => 'xsd:int'), // 2017-03-03: Neu
                    'billing_federal_state_code_iso' => array('name' => 'billing_federal_state_code_iso', 'type' => 'xsd:string'), // 2017-03-03: Neu
                    'payment_code' => array('name' => 'payment_code', 'type' => 'xsd:string'),
                    'subpayment_code' => array('name' => 'subpayment_code', 'type' => 'xsd:string'),
                    'shipping_code' => array('name' => 'shipping_code', 'type' => 'xsd:string'),
                    'currency_code' => array('name' => 'currency_code', 'type' => 'xsd:string'),
                    'currency_value' => array('name' => 'currency_value', 'type' => 'xsd:decimal'),
                    'language_code' => array('name' => 'language_code', 'type' => 'xsd:string'),
                    'comments' => array('name' => 'comments', 'type' => 'xsd:string'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'order_date' => array('name' => 'order_date', 'type' => 'xsd:dateTime'),
                    'orders_status' => array('name' => 'orders_status', 'type' => 'xsd:int'),
                    'account_type' => array('name' => 'account_type', 'type' => 'xsd:int'),
                    'allow_tax' => array('name' => 'allow_tax', 'type' => 'xsd:int'),
                    'tax_included' => array('name' => 'tax_included', 'type' => 'xsd:int'), 
                    'tax_free' => array('name' => 'tax_free', 'type' => 'xsd:int'),
                    'customers_ip' => array('name' => 'customers_ip', 'type' => 'xsd:string'),
                    'shop_id' => array('name' => 'shop_id', 'type' => 'xsd:int'),
                    'orders_data' => array('name' => 'orders_data', 'type' => 'xsd:string'),
                    'campaign_id' => array('name' => 'campaign_id', 'type' => 'xsd:int'),
                    'source_id' => array('name' => 'source_id', 'type' => 'xsd:int'), // 2017-03-03: Neu
                    'orders_source_external_id' => array('name' => 'orders_source_external_id', 'type' => 'xsd:string'), // 2017-03-03: Neu
                    'PPP_PAYMENTID' => array('name' => 'PPP_PAYMENTID', 'type' => 'xsd:string'), // 2017-03-03: Neu
                    'PPP_SALEID' => array('name' => 'PPP_SALEID', 'type' => 'xsd:string'), // 2017-03-03: Neu
                    'count_items' => array('name' => 'count_items', 'type' => 'xsd:int'),
                    'orders_total' => array('name' => 'orders_total', 'type' => 'tns:orders_totalList'),
                    'customer_data' => array('name' => 'customer_data', 'type' => 'tns:customer_dataBase'),
                    'delivery_data' => array('name' => 'delivery_data', 'type' => 'tns:customer_dataBase'),
                    'payment_data' => array('name' => 'shipping_data', 'type' => 'tns:payment_dataBase'),
                    'shipping_data' => array('name' => 'shipping_data', 'type' => 'tns:shipping_dataBase'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// 2016-08-18: Neu
                    'items' => array('name' => 'items', 'type' => 'tns:itemsList'),
                    'total_tax' => array('name' => 'total_tax', 'type' => 'tns:taxList'),
                    'total_price_formated' => array('name' => 'total_formated', 'type' => 'xsd:string'),
                    'total_price' => array('name' => 'orders_data', 'type' => 'xsd:decimal'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - ordersItem base
            array(//begin- ordersItem base
                'name' => 'xtstatus',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'status_id' => array('name' => 'status_id', 'type' => 'xsd:int'),
                    'status_name' => array('name' => 'status_name', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - ordersItem base            
             array(// order Staus Liste
                'name' => 'xtstatusList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:xtstatus[]')),
                'arrayType' => 'tns:xtstatus'), //end- order Staus Liste
            array(//begin- xtcustomersStatus
                'name' => 'xtcustomersStatus',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'customers_status_id' => array('name' => 'customers_status_id', 'type' => 'xsd:int'),
                    'customers_status_name' => array('name' => 'customers_status_name', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - xtcustomersStatus
             array(// order Staus Liste
                'name' => 'xtcustomersStatusList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:xtcustomersStatus[]')),
                'arrayType' => 'tns:xtcustomersStatus'), //end- xtcustomersStatusList
            array(//begin- permissionItem
                'name' => 'permissionItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'permission' => array('name' => 'permission', 'type' => 'xsd:boolean'),
                    'pgroup' => array('name' => 'pgroup', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - permissionItem
             array(// permissionItemList
                'name' => 'permissionItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:permissionItem[]')),
                'arrayType' => 'tns:permissionItem'), //end- permissionItemList
            array( // Image Daten für getImage
                'name' => 'xtimage',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'imageBase64' => array('imageBase64' => 'imageBase64', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''),                  
            array(//begin- xtVersionInfos
                'name' => 'xtVersionInfos',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'productName' => array('name' => 'productName', 'type' => 'xsd:string'),
                    'productVersionMajor' => array('name' => 'productVersionMajor', 'type' => 'xsd:int'),
                    'productVersionMinor' => array('name' => 'productVersionMinor', 'type' => 'xsd:int'),
                    'productVersionPatchLevel' => array('name' => 'productVersionPatchLevel', 'type' => 'xsd:int'),
                    'productVersionRevision' => array('name' => 'productVersionRevision', 'type' => 'xsd:int'), // 2018-06-29: Neu
                    'ShopVersion' => array('name' => 'ShopVersion', 'type' => 'xsd:string'), // 2017-03-03: Neu
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - xtVersionInfos           
            array(// begin- taxList
                'name' => 'taxList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:taxBase[]')),
                'arrayType' => 'tns:taxBase'), //end- products_cross_sellList

            array(//begin- customer_dataItem base (BUEROWARE spezifisch)
                'name' => 'taxBase',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'value' => array('name' => 'value', 'type' => 'xsd:decimal'),
                    'tax_rate' => array('name' => 'tax_rate', 'type' => 'xsd:string'),
                    'value_formated' => array('name' => 'value_formated', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(// begin- products_cross_sellList
                'name' => 'itemsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:itemsBase[]')),
                'arrayType' => 'tns:itemsBase'), //end- products_cross_sellList
            array(//begin- customer_dataItem base (BUEROWARE spezifisch)
                'name' => 'itemsBase',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'price_sum' => array('name' => 'price_sum', 'type' => 'xsd:decimal'),
                    'name' => array('name' => 'name', 'type' => 'xsd:string'),
                    'item_data' => array('name' => 'name', 'type' => 'xsd:string'),
                    'options_full' => array('name' => 'name', 'type' => 'tns:OptionsList'), // optionsplugin
                    'quantity' => array('name' => 'quantity', 'type' => 'xsd:decimal'),
                    'tax' => array('name' => 'tax', 'type' => 'xsd:decimal'),
                    'tax_class' => array('name' => 'tax_class', 'type' => 'xsd:int'),
                    'weight' => array('name' => 'weight', 'type' => 'xsd:decimal'),
                    'price' => array('name' => 'price', 'type' => 'xsd:decimal'),
                    'price_otax' => array('name' => 'price_otax', 'type' => 'xsd:decimal'),
                    'applied_discount_percent' => array('name' => 'applied_discount_percent', 'type' => 'xsd:decimal'),
                    'original_price' => array('name' => 'original_price', 'type' => 'xsd:decimal'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// 2016-03-01: Neu
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(// begin- OptionsList
                'name' => 'OptionsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:optionItem[]')),
                'arrayType' => 'tns:optionItem'), //end- OptionsList        


            array(//begin- optionItem base  , options plugin
                'name' => 'optionItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'option_group_id' => array('name' => 'option_group_id', 'type' => 'xsd:int'),
                    'option_group_field' => array('name' => 'option_group_field', 'type' => 'xsd:string'),
                    'option_group_name' => array('name' => 'option_group_name', 'type' => 'xsd:string'),
                    'option_group_desc' => array('name' => 'option_group_desc', 'type' => 'xsd:string'),
                    'option_value_id' => array('name' => 'option_value_id', 'type' => 'xsd:string'),
                    'option_value_parent' => array('name' => 'option_value_parent', 'type' => 'xsd:string'),
                    'option_value_image' => array('name' => 'option_value_image', 'type' => 'xsd:string'),
                    'option_value_field' => array('name' => 'option_value_field', 'type' => 'xsd:string'),
                    'option_value_default_model' => array('name' => 'option_value_default_model', 'type' => 'xsd:string'),
                    'option_value_default_price' => array('name' => 'option_value_default_price', 'type' => 'xsd:string'),
                    'option_value_default_p_prefix' => array('name' => 'option_value_default_p_prefix', 'type' => 'xsd:string'),
                    'option_value_default_weight' => array('name' => 'option_value_default_weight', 'type' => 'xsd:string'),
                    'option_value_default_w_prefix' => array('name' => 'option_value_default_w_prefix', 'type' => 'xsd:string'),
                    'option_value_name' => array('name' => 'option_value_name', 'type' => 'xsd:string'),
                    'option_value_value' => array('name' => 'option_value_value', 'type' => 'xsd:string'),
                    'option_value_desc' => array('name' => 'option_value_desc', 'type' => 'xsd:string'),
                    'pto_id' => array('name' => 'pto_id', 'type' => 'xsd:string'),
                    'option_model' => array('name' => 'option_model', 'type' => 'xsd:string'),
                    'option_price' => array('name' => 'option_price', 'type' => 'xsd:decimal'),
                    'option_p_prefix' => array('name' => 'option_p_prefix', 'type' => 'xsd:string'),
                    'option_weight' => array('name' => 'option_weight', 'type' => 'xsd:decimal'),
                    'option_w_prefix' => array('name' => 'option_w_prefix', 'type' => 'xsd:string'),
                    'option_calc_type' => array('name' => 'option_calc_type', 'type' => 'xsd:string'),
                    'option_count' => array('name' => 'option_count', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - optionItem base

            array(//begin- customer_dataItem base (BUEROWARE spezifisch)
                'name' => 'customer_dataBase',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'address_id' => array('name' => 'address_id', 'type' => 'xsd:string'),
                    'firstname' => array('name' => 'firstname', 'type' => 'xsd:string'),
                    
                    'dob' => array('name' => 'dob', 'type' => 'xsd:dateTime'),
                    
                    //'dob' => array('name' => 'dob', 'type' => 'xsd:dateTime',  'nillable' => 'true'),
                    'name' => array('name' => 'name', 'type' => 'xsd:string'),
                    'street' => array('name' => 'street', 'type' => 'xsd:string'),
                    'address_addition' => array('name' => 'address_addition', 'type' => 'xsd:string'), // 2018-07-12 NEU
                    'gender' => array('name' => 'gender', 'type' => 'xsd:string'),
                    'title' => array('name' => 'title', 'type' => 'xsd:string'), // 2018-07-12 NEU
                    'email' => array('name' => 'email', 'type' => 'xsd:string'),
                    'city' => array('name' => 'city', 'type' => 'xsd:string'),
                    'suburb' => array('name' => 'suburb', 'type' => 'xsd:string'),
                    'password' => array('name' => 'password', 'type' => 'xsd:string'),
                    'company' => array('name' => 'company', 'type' => 'xsd:string'),
                    'company_2' => array('name' => 'company_2', 'type' => 'xsd:string'),
                    'company_3' => array('name' => 'company_3', 'type' => 'xsd:string'),
                    'country' => array('name' => 'country', 'type' => 'xsd:string'),
                    'country_code' => array('name' => 'country_code', 'type' => 'xsd:string'),
                    'federal_state_code' => array('name' => 'federal_state_code', 'type' => 'xsd:int'),
                    'federal_state_code_iso' => array('name' => 'federal_state_code_iso', 'type' => 'xsd:string'),
                    'phone' => array('name' => 'phone', 'type' => 'xsd:string'),
                    'mobile_phone' => array('name' => 'mobile_phone', 'type' => 'xsd:string'), //2015-11-05: Neu
                    'fax' => array('name' => 'fax', 'type' => 'xsd:string'),
                    'zip' => array('name' => 'zip', 'type' => 'xsd:string'),
                    'customers_cid' => array('name' => 'customers_cid', 'type' => 'xsd:string'), 
                    'vat_id' => array('name' => 'vat_id', 'type' => 'xsd:string'), // umbeannt von umstid
                    'address_id' => array('name' => 'address_id', 'type' => 'xsd:int'),
                    'group' => array('name' => 'group', 'type' => 'xsd:int'),
                    'banktransfer_owner' => array('name' => 'banktransfer_owner', 'type' => 'xsd:string'),
                    'banktransfer_blz' => array('name' => 'banktransfer_blz', 'type' => 'xsd:string'),
                    'banktransfer_bank_name' => array('name' => 'banktransfer_bank_name', 'type' => 'xsd:string'),
                    'banktransfer_number' => array('name' => 'banktransfer_number', 'type' => 'xsd:string'),
                    'banktransfer_iban' => array('name' => 'banktransfer_iban', 'type' => 'xsd:string'),
                    'banktransfer_bic' => array('name' => 'banktransfer_bic', 'type' => 'xsd:string'),
                    'packstation_post' => array('name' => 'packstation_post', 'type' => 'xsd:string'),
                    'packstation_nr' => array('name' => 'packstation_nr', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(//begin- payment_dataItem base (BUEROWARE spezifisch)
                'name' => 'payment_dataBase',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'cost' => array('name' => 'cost', 'type' => 'xsd:decimal'),
                    //	'tax_amount' => array('name'=>'tax_amount','type'=>'xsd:string'),
                    'id' => array('name' => 'id', 'type' => 'xsd:int'),
                    'type' => array('name' => 'type', 'type' => 'xsd:string'),
                    'tax' => array('name' => 'tax', 'type' => 'xsd:decimal'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(//begin- shipping_dataItem base (BUEROWARE spezifisch)
                'name' => 'shipping_dataBase',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'cost' => array('name' => 'cost', 'type' => 'xsd:decimal'),
                    //	'tax_amount' => array('name'=>'tax_amount','type'=>'xsd:string'),
                    'id' => array('name' => 'id', 'type' => 'xsd:int'),
                    'type' => array('name' => 'type', 'type' => 'xsd:string'),
                    'tax' => array('name' => 'tax', 'type' => 'xsd:decimal'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(//begin- orders_productsItem base
                'name' => 'orders_productsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'products_price' => array('name' => 'products_price', 'type' => 'xsd:decimal'),
                    'products_tax_class' => array('name' => 'products_tax_class', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - orders_productsItem base

            array(// begin- ordersList
                'name' => 'ordersList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:ordersItem[]')),
                'arrayType' => 'tns:ordersItem'), //end- ordersList

            array(//begin- orders_productsItem base
                'name' => 'orders_productsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'orders_products_id' => array('name' => 'orders_products_id', 'type' => 'xsd:int'),
                    //	'orders_id' => array('name'=>'orders_id','type'=>'xsd:int'),
                    'products_id' => array('name' => 'products_id', 'type' => 'xsd:int'),
                    'products_model' => array('name' => 'products_model', 'type' => 'xsd:string'),
                    'products_name' => array('name' => 'products_name', 'type' => 'xsd:string'),
                    'products_price' => array('name' => 'products_price', 'type' => 'xsd:decimal'),
                    'products_discount' => array('name' => 'products_discount', 'type' => 'xsd:decimal'),
                    'products_tax' => array('name' => 'products_tax', 'type' => 'xsd:decimal'),
                    'products_tax_class' => array('name' => 'products_tax_class', 'type' => 'xsd:int'),
                    'products_quantity' => array('name' => 'products_quantity', 'type' => 'xsd:decimal'),
                    'products_data' => array('name' => 'products_data', 'type' => 'xsd:string'),
                    'allow_tax' => array('name' => 'allow_tax', 'type' => 'xsd:int'),
                    'products_shipping_time' => array('name' => 'products_shipping_time', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - orders_productsItem base

            array(// begin- orders_productsList
                'name' => 'orders_productsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:orders_productsItem[]')),
                'arrayType' => 'tns:orders_productsItem'), //end- products_cross_sellList
            array(//begin- orders_totalItem base
                'name' => 'orders_totalItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'orders_total_id' => array('name' => 'orders_total_id', 'type' => 'xsd:int'),
                    'orders_total_key' => array('name' => 'orders_total_key', 'type' => 'xsd:string'),
                    'orders_total_model' => array('name' => 'orders_total_model', 'type' => 'xsd:string'),
                    'orders_total_name' => array('name' => 'orders_total_name', 'type' => 'xsd:string'),
                    'orders_total_price' => array('name' => 'orders_total_price', 'type' => 'xsd:decimal'),
                    'orders_total_tax' => array('name' => 'orders_total_tax', 'type' => 'xsd:decimal'),
                    'orders_total_tax_class' => array('name' => 'orders_total_tax_class', 'type' => 'xsd:int'),
                    'orders_total_quantity' => array('name' => 'orders_total_quantity', 'type' => 'xsd:decimal'),
                    'allow_tax' => array('name' => 'allow_tax', 'type' => 'xsd:int'),
                    'info_code' => array('name' => 'info_code', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - orders_totalItem base
            array(// begin- orders_totalList
                'name' => 'orders_totalList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:orders_totalItem[]')),
                'arrayType' => 'tns:orders_totalItem'), //end- orders_totalList
            array( // # delivery times
                'name' => 'deliveryItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'id' => array('name' => 'status_id', 'type' => 'xsd:int'),
                    'name' => array('name' => 'status_name', 'type' => 'tns:langItem'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //
            array(// begin- deliveryList
                'name' => 'deliveryList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:deliveryItem[]')),
                'arrayType' => 'tns:deliveryItem'), //end- storesList		
            array(//begin- paymentItem 
                'name' => 'paymentItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'payment_id' => array('name' => 'payment_id', 'type' => 'xsd:int'),
                    'payment_name' => array('name' => 'payment_name', 'type' => 'tns:langItem'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - paymentItem        
            array(// begin- paymentList
                'name' => 'paymentList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:paymentItem[]')),
                'arrayType' => 'tns:paymentItem'), //end- paymentList   
            array(//begin- shippingItem 
                'name' => 'shippingItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'shipping_id' => array('name' => 'shipping_id', 'type' => 'xsd:int'),
                    'shipping_code' => array('name' => 'shipping_code', 'type' => 'xsd:string'),
                    'shipping_name' => array('name' => 'shipping_name', 'type' => 'tns:langItem'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - shippingItem        
            array(// begin- shippingList
                'name' => 'shippingList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:shippingItem[]')),
                'arrayType' => 'tns:shippingItem'), //end- shippingList
            array(// begin- CategoryList
                'name' => 'categoryList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:categoryItemExport[]')),
                'arrayType' => 'tns:categoryItemExport'), //end- storesList		
            array(//begin- categoryItem base
                'name' => 'categoryItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),
                    'categories_parent_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),  
                    //'parent_id' => array('name' => 'parent_id', 'type' => 'xsd:string'), 2017-03-20 Entfernt, da deprecated seit 4.2
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'external_parent_id' => array('name' => 'external_parent_id', 'type' => 'xsd:string'),
                    'permission_id' => array('name' => 'permission_id', 'type' => 'xsd:int'),
                    'categories_status' => array('name' => 'categories_status', 'type' => 'xsd:int'),
                    'sort_order' => array('name' => 'sort_order', 'type' => 'xsd:int'),
                    'top_category' => array('name' => 'top_category', 'type' => 'xsd:int'),
                    'categories_image' => array('name' => 'categories_image', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'categories_template' => array('name' => 'categories_template', 'type' => 'xsd:string'),
                    'listing_template' => array('name' => 'listing_template', 'type' => 'xsd:string'),
                    'categories_name' => array('name' => 'categories_name', 'type' => 'tns:langItem'),
                    'categories_heading_title' => array('name' => 'categories_heading_title', 'type' => 'tns:langItem'),
                    'categories_description' => array('name' => 'categories_description', 'type' => 'tns:langItem'),
                    'categories_description_bottom' => array('name' => 'categories_description_bottom', 'type' => 'tns:langItem'),
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'seo_url' => array('name' => 'url', 'type' => 'tns:langItem'),
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'),
                    'products_sorting' => array('name' => 'products_sorting', 'type' => 'xsd:string'),
                    'products_sorting2' => array('name' => 'products_sorting2', 'type' => 'xsd:string'),
                    'google_product_cat' => array('name' => 'google_product_cat', 'type' => 'xsd:int'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// Garcia Neu: 2013-02-05: Kapselt Inidividualfelder / Optional
                    'categoryDescriptionsList' => array('name' => 'categoryDescriptionsList', 'type' => 'tns:categoryDescriptionsList'),// 2014-12-01 Neu: Anpassung an multiShop Desc Texte
                    'category_custom_link' => array('name' => 'category_custom_link', 'type' => 'xsd:int'), // 2015-03-06 Neu: Neue Felder XT42
                    'category_custom_link_type' => array('name' => 'category_custom_link_type', 'type' => 'xsd:string'),// 2015-03-06 Neu: Neue Felder XT42
                    'category_custom_link_id' => array('name' => 'category_custom_link_id', 'type' => 'xsd:int'), // 2015-03-06 Neu: Neue Felder XT42
                    'category_content' => array('name' => 'category_content', 'type' => 'xsd:int'), // 2017-03-01 Neue: Neue Felder XT50
                    'category_content_detail' => array('name' => 'category_content_detail', 'type' => 'xsd:int'), // 2017-03-01 Neue: Neue Felder XT50
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - categoryItem base
           array( // 2015-12-01 Neu: Liste und Daten für category descriptions pro shop
                'name' => 'categoryDescriptionsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:categoryDescriptionsItem[]')),
                'arrayType' => 'tns:categoryDescriptionsItem'), 

            array(
                'name' => 'categoryDescriptionsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_store_id' => array('name' => 'categories_store_id', 'type' => 'xsd:int'),
                    'categories_name' => array('name' => 'categories_name', 'type' => 'tns:langItem'),
                    'categories_heading_title' => array('name' => 'categories_heading_title', 'type' => 'tns:langItem'),
                    'categories_description' => array('name' => 'categories_description', 'type' => 'tns:langItem'),
                    'categories_description_bottom' => array('name' => 'categories_description_bottom', 'type' => 'tns:langItem'),
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'seo_url' => array('name' => 'seo_url', 'type' => 'tns:langItem'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),
                    'link_url' => array('name' => 'link_url', 'type' => 'tns:langItem'),//2015-03-06: Neue Tabelle xt_categories_custom_link_url
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(//begin- categoryItemExport base
                'name' => 'categoryItemExport',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'permission_id' => array('name' => 'permission_id', 'type' => 'xsd:int'),
                    'parent_id' => array('name' => 'parent_id', 'type' => 'xsd:string'),
                    'external_parent_id' => array('name' => 'parent_id', 'type' => 'xsd:string'),
                    'categories_status' => array('name' => 'categories_status', 'type' => 'xsd:int'),
                    'sort_order' => array('name' => 'sort_order', 'type' => 'xsd:int'),
                    'top_category' => array('name' => 'top_category', 'type' => 'xsd:int'),
                    'categories_image' => array('name' => 'products_image', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'categories_template' => array('name' => 'categories_template', 'type' => 'xsd:string'),
                    'listing_template' => array('name' => 'listing_template', 'type' => 'xsd:string'),
                    'categories_name' => array('name' => 'categories_name', 'type' => 'tns:langItem'),
                    'categories_heading_title' => array('name' => 'categories_heading_title', 'type' => 'tns:langItem'),
                    'categories_description' => array('name' => 'categories_description', 'type' => 'tns:langItem'),
                    'categories_description_bottom' => array('name' => 'categories_description_bottom', 'type' => 'tns:langItem'),
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'url_text' => array('name' => 'url_text', 'type' => 'tns:langItem'),
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'),
                    'products_sorting' => array('name' => 'products_sorting', 'type' => 'xsd:string'),
                    'products_sorting2' => array('name' => 'products_sorting2', 'type' => 'xsd:string'),
                    'google_product_cat' => array('name' => 'google_product_cat', 'type' => 'xsd:int' ), // 2017-03-01 type von string in int
                    'categoryDescriptionsList' => array('name' => 'categoryDescriptionsList', 'type' => 'tns:categoryDescriptionsList'), // 2014-12-01 Neu: Anpassung an multiShop Desc Texte
                    'category_custom_link' => array('name' => 'category_custom_link', 'type' => 'xsd:int'), // 2015-03-06: Neue Felder XT42
                    'category_custom_link_type' => array('name' => 'category_custom_link_type', 'type' => 'xsd:string'), // 2015-03-06: Neue Felder XT42
                    'category_custom_link_id' => array('name' => 'category_custom_link_id', 'type' => 'xsd:int'), // 2015-03-06: Neue Felder XT42
                    'category_content' => array('name' => 'category_content', 'type' => 'xsd:int'), // 2017-03-01 Neu: Neue Felder XT50
                    'category_content_detail' => array('name' => 'category_content_detail', 'type' => 'xsd:int'), // 2017-03-01 Neu: Neue Felder XT50
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - categoryItemExport base         



            array(// begin- categoriesItemList
                'name' => 'categoriesItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:categoryItem[]')),
                'arrayType' => 'tns:categoryItem'), //end- categoriesItemList
            array(// begin- categoriesResultItemList
                'name' => 'categoriesResultItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:categoryResultItem[]')),
                'arrayType' => 'tns:categoryResultItem'), //end- categoriesResultItemList

            array(//begin- categoryResultItem base
                'name' => 'categoryResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - categoryResultItem base
                
            
            // Neu 2012-11-07
            array(// begin- manufacturersResultItemList
                'name' => 'manufacturersResultItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:manufacturerResultItem[]')),
                'arrayType' => 'tns:manufacturerResultItem'), //end- categoriesResultItemList
            // 2017-02-28: manufacturers_id type von string in int
            array(//begin- manufacturerResultItem base
                'name' => 'manufacturerResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'manufacturers_id' => array('name' => 'manufacturers_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - ManufacturerResultItem base            
            array(//
                'name' => 'attributeItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'attributes_id' => array('name' => 'attributes_id', 'type' => 'xsd:int'),
                    'attributes_parent' => array('name' => 'attributes_parent', 'type' => 'xsd:int'),
                    'attributes_ext_id' => array('name' => 'attributes_ext_id', 'type' => 'xsd:int'),
                    'attributes_ext_parent_id' => array('name' => 'attributes_ext_parent_id', 'type' => 'xsd:int'),
                    'attributes_model' => array('name' => 'attributes_model', 'type' => 'xsd:string'),
                    'attributes_image' => array('name' => 'attributes_image', 'type' => 'xsd:string'),
                    'attributes_color' => array('name' => 'attributes_image', 'type' => 'xsd:string'),//2017-03-01 Neues Feld attributes_color
                    'sort_order' => array('name' => 'sort_order', 'type' => 'xsd:int'),
                    'status' => array('name' => 'status', 'type' => 'xsd:int'),
                    'attributes_name' => array('name' => 'attributes_name', 'type' => 'tns:langItem'),
                    'attributes_desc' => array('name' => 'attributes_desc', 'type' => 'tns:langItem'),
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),
                    'attributes_templates_id' => array('name' => 'attributes_templates_id', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - attribute base         
            
            
            array(// Liste von attrbuteItems
                'name' => 'attributeItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:attributeItem[]')),
                'arrayType' => 'tns:attributeItem'), //
            // Datenmodell für Antwort von deleteAttribute
             array(
                'name' => 'attributeResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'attributes_id' => array('name' => 'attributes_id', 'type' => 'xsd:int'),
                    'attributes_ext_id' => array('name' => 'attributes_ext_id', 'type' => 'xsd:int'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),                   
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - attribute base                     
            
            
            
            
            // Garcia neu 2012-11-19: Kapselt ein mediaItem (=Bild oder Dokument)
            array(//
                'name' => 'mediaItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'filename' => array('name' => 'filename', 'type' => 'xsd:string'),
                    'filedata' => array('name' => 'filedata', 'type' => 'xsd:base64Binary'),
                    'mediatype' => array('name' => 'mediatype', 'type' => 'xsd:string'),
                    
                ),
                'attrs' => array(),
                'arrayType' => ''), // 
                       
            
            
            // Garcia neu 2014-07-18: Kapselt ein media XT Item (=Bild oder Dokument)
            array(//
                'name' => 'mediaItemXT',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'id' => array('name' => 'id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'file' => array('name' => 'file', 'type' => 'xsd:string'),
                    'filedata' => array('name' => 'filedata', 'type' => 'xsd:base64Binary'),
                    'type' => array('name' => 'type', 'type' => 'xsd:string'),
                    'class' => array('name' => 'class', 'type' => 'xsd:string'),
                    'download_status' => array('name' => 'download_status', 'type' => 'xsd:string'),//enum('free', 'order')
                    'status' => array('name' => 'status', 'type' => 'xsd:string'),//enum('true', 'false')
                    'max_dl_count' => array('name' => 'max_dl_count', 'type' => 'xsd:int'),
                    'max_dl_days' => array('name' => 'max_dl_days', 'type' => 'xsd:int'),
                    'media_name' => array('name' => 'media_name', 'type' => 'tns:langItem'), // typ geändert von string[] zu langItem!
                    'media_description' => array('name' => 'media_description', 'type' => 'tns:langItem'),  // typ geändert von string[] zu langItem!
                    'media_languages' => array('name' => 'media_languages', 'type' => 'tns:arrayOfString'), 
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'),
                ),
                'attrs' => array(),
                'arrayType' => ''), // 
            array(// Kapselt ein media XT Item Metadatensatz (=Daten OHNE file Daten filedata als base64!)
                'name' => 'mediaItemXTMeta',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'id' => array('name' => 'id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'file' => array('name' => 'file', 'type' => 'xsd:string'),
                    'type' => array('name' => 'type', 'type' => 'xsd:string'),
                    'class' => array('name' => 'class', 'type' => 'xsd:string'),
                    'download_status' => array('name' => 'download_status', 'type' => 'xsd:string'),
                    'status' => array('name' => 'status', 'type' => 'xsd:string'),
                    'max_dl_count' => array('name' => 'max_dl_count', 'type' => 'xsd:int'),
                    'max_dl_days' => array('name' => 'max_dl_days', 'type' => 'xsd:int'),
              ),
                'attrs' => array(),
                'arrayType' => ''), // 
             array(
                'name' => 'mediaItemXTMetaList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:mediaItemXTMeta[]')),
                'arrayType' => 'tns:mediaItemXTMeta'),               
            array( // Kapselt einzelnen Link von Artikel auf mediaItem per id oder external_id
                'name' => 'mediaItemXTLink',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'id' => array('name' => 'id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
              ),
                'attrs' => array(),
                'arrayType' => ''), // 
             array( // Eine Liste von mediaItemXTLink
                'name' => 'mediaItemXTLinkList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:mediaItemXTLink[]')),
                'arrayType' => 'tns:mediaItemXTLink'),                     
            array(//begin- storeItem 
                'name' => 'storeItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'shop_id' => array('name' => 'store_id', 'type' => 'xsd:int'),
                    'shop_status' => array('name' => 'store_status', 'type' => 'xsd:int'),
                    'shop_title' => array('name' => 'shop_title', 'type' => 'xsd:string'),
                    'shop_domain' => array('name' => 'shop_domain', 'type' => 'xsd:string'),
                    'shop_ssl_domain' => array('name' => 'shop_ssl_domain', 'type' => 'xsd:string'),
                    'shop_http' => array('name' => 'shop_http', 'type' => 'xsd:string'),
                    'shop_https' => array('name' => 'shop_https', 'type' => 'xsd:string'),
                    'shop_ssl' => array('name' => 'shop_ssl', 'type' => 'xsd:string'),
                    'admin_ssl' => array('name' => 'admin_ssl', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - storeItem
            array(// begin- manufacturerList
                'name' => 'manufacturerList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:manufacturerListItem[]')),
                'arrayType' => 'tns:manufacturerListItem'), //end- storesList        
            array(//begin- manufacturerListItem base
                'name' => 'manufacturerListItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'manufacturers_id' => array('name' => 'manufacturers_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'manufacturers_name' => array('name' => 'manufacturers_name', 'type' => 'xsd:string'),
                    'manufacturers_image' => array('name' => 'manufacturers_image', 'type' => 'xsd:string'),
                    'manufacturers_status' => array('name' => 'manufacturers_status', 'type' => 'xsd:int'),
                    'manufacturers_sort' => array('name' => 'manufacturers_sort', 'type' => 'xsd:int'),
                    'products_sorting' => array('name' => 'products_sorting', 'type' => 'xsd:string'),
                    'products_sorting2' => array('name' => 'products_sorting2', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'manufacturers_description' => array('name' => 'manufacturers_description', 'type' => 'tns:langItem'),
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'manufacturers_url' => array('name' => 'manufacturers_url', 'type' => 'tns:langItem'),
                    'url_text' => array('name' => 'url', 'type' => 'tns:langItem'),
                    'permissionList' => array('name' => 'permissionList', 'type' => 'tns:permissionItemList'),
                    'manufacturerDescriptionsList' => array('name' => 'manufacturerDescriptionsList', 'type' => 'tns:manufacturerDescriptionsList'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - manufacturerListItem base
           array( // Liste und Daten für manufacturer descriptions pro shop
                'name' => 'manufacturerDescriptionsList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:manufacturerDescriptionsItem[]')),
                'arrayType' => 'tns:manufacturerDescriptionsItem'), 
            array(
                'name' => 'manufacturerDescriptionsItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'manufacturers_store_id' => array('name' => 'categories_store_id', 'type' => 'xsd:int'),
                    'manufacturers_description' => array('name' => 'manufacturers_description', 'type' => 'tns:langItem'),
                    'meta_description' => array('name' => 'meta_description', 'type' => 'tns:langItem'),
                    'meta_title' => array('name' => 'meta_title', 'type' => 'tns:langItem'),
                    'meta_keywords' => array('name' => 'meta_keywords', 'type' => 'tns:langItem'),
                    'manufacturers_url' => array('name' => 'manufacturers_url', 'type' => 'tns:langItem'),
                    'url_text' => array('name' => 'url', 'type' => 'tns:langItem'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),                      
            array(// begin- storesList
                'name' => 'storesList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:storeItem[]')),
                'arrayType' => 'tns:storeItem'), //end- storesList		
            array(//begin- customers_addressesItem base
                'name' => 'customers_addressesItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'address_book_id' => array('name' => 'address_book_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'customers_id' => array('name' => 'customers_id', 'type' => 'xsd:int'),
                    'customers_gender' => array('name' => 'customers_gender', 'type' => 'xsd:string'),
                    'customers_dob' => array('name' => 'customers_dob', 'type' => 'xsd:dateTime'),
                    'customers_phone' => array('name' => 'customers_phone', 'type' => 'xsd:string'),
                    'customers_fax' => array('name' => 'customers_fax', 'type' => 'xsd:string'),
                    'customers_company' => array('name' => 'customers_company', 'type' => 'xsd:string'),
                    'customers_company_2' => array('name' => 'customers_company_2', 'type' => 'xsd:string'),
                    'customers_company_3' => array('name' => 'customers_company_3', 'type' => 'xsd:string'),
                    'customers_firstname' => array('name' => 'customers_firstname', 'type' => 'xsd:string'),
                    'customers_lastname' => array('name' => 'customers_lastname', 'type' => 'xsd:string'),
                    'customers_street_address' => array('name' => 'customers_street_address', 'type' => 'xsd:string'),
                    'customers_suburb' => array('name' => 'customers_suburb', 'type' => 'xsd:string'),
                    'customers_postcode' => array('name' => 'customers_postcode', 'type' => 'xsd:string'),
                    'customers_city' => array('name' => 'customers_city', 'type' => 'xsd:string'),
                    'customers_country_code' => array('name' => 'customers_country_code', 'type' => 'xsd:string'),
                    'address_class' => array('name' => 'address_class', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'customers_mobile_phone' => array('name' => 'customers_mobile_phone', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - customers_addressesItem base
            array(// begin- customers_addressesList
                'name' => 'customers_addressesList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:customers_addressesItem[]')),
                'arrayType' => 'tns:customers_addressesItem'), //end- customers_addressesList
            array(//begin- categoriesItem base
                'name' => 'categoriesItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'permission_id' => array('name' => 'permission_id', 'type' => 'xsd:int'),
                    'categories_owner' => array('name' => 'categories_owner', 'type' => 'xsd:int'),
                    'categories_image' => array('name' => 'categories_image', 'type' => 'xsd:string'),
                    'parent_id' => array('name' => 'parent_id', 'type' => 'xsd:int'),
                    'categories_status' => array('name' => 'categories_status', 'type' => 'xsd:int'),
                    'categories_template' => array('name' => 'categories_template', 'type' => 'xsd:string'),
                    'listing_template' => array('name' => 'listing_template', 'type' => 'xsd:string'),
                    'sort_order' => array('name' => 'sort_order', 'type' => 'xsd:int'),
                    'products_sorting' => array('name' => 'products_sorting', 'type' => 'xsd:string'),
                    'products_sorting2' => array('name' => 'products_sorting2', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'categories_descriptions' => array('name' => 'last_modified', 'type' => 'tns:categories_descriptionList'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - categoriesItem base

            array(//begin- categories_descriptionItem base
                'name' => 'categories_descriptionItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'categories_id' => array('name' => 'categories_id', 'type' => 'xsd:int'),
                    'language_code' => array('name' => 'language_code', 'type' => 'xsd:string'),
                    'categories_name' => array('name' => 'categories_name', 'type' => 'xsd:string'),
                    'categories_heading_title' => array('name' => 'categories_heading_title', 'type' => 'xsd:string'),
                    'categories_description' => array('name' => 'categories_description', 'type' => 'tns:categories_descriptionList'),
                ),
                'attrs' => array(),
                'arrayType' => ''), //end - categories_descriptionItem base


            array(// begin- customers_addressesList
                'name' => 'categories_descriptionList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:categories_descriptionItem[]')),
                'arrayType' => 'tns:categories_descriptionItem'), //end- customers_addressesList
            array(//begin- customersItem base
                'name' => 'customersItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'customers_cid' => array('name' => 'customers_cid', 'type' => 'xsd:string'),
                    'shop_id' => array('name' => 'shop_id', 'type' => 'xsd:string'),
                    'gender' => array('name' => 'gender', 'type' => 'xsd:string'), //m / f / c
                    'customers_title' => array('name' => 'customers_title', 'type' => 'xsd:string'), // 2018-06-26 Edwin Klause: Neues Feld
                    'lastname' => array('name' => 'lastname', 'type' => 'xsd:string'),
                    'firstname' => array('name' => 'firstname', 'type' => 'xsd:string'),
                    'dob' => array('name' => 'dob', 'type' => 'xsd:dateTime'),
                    'mail' => array('name' => 'mail', 'type' => 'xsd:string'),
                    'PPPLUS_EMAIL' => array('name' => 'PPPLUS_EMAIL', 'type' => 'xsd:string'), // 2017-03-22 Edwin Klause: Neues Feld
                    'phone' => array('name' => 'phone', 'type' => 'xsd:string'),
                    'mobile_phone' => array('name' => 'mobile_phone', 'type' => 'xsd:string'), //2015-11-05 Edwin Klause: Neues Feld
                    'fax' => array('name' => 'fax', 'type' => 'xsd:string'),
                    'company' => array('name' => 'company', 'type' => 'xsd:string'),
                    'company_2' => array('name' => 'company_2', 'type' => 'xsd:string'),
                    'company_3' => array('name' => 'company_3', 'type' => 'xsd:string'),
                    'street_address' => array('name' => 'street_address', 'type' => 'xsd:string'),
                    'customers_address_addition' => array('name' => 'customers_address_addition', 'type' => 'xsd:string'), // 2018-06-26 Edwin Klause: Neues Feld
                    'postcode' => array('name' => 'postcode', 'type' => 'xsd:string'),
                    'city' => array('name' => 'city', 'type' => 'xsd:string'),
                    'customers_suburb' => array('name' => 'customers_suburb', 'type' => 'xsd:string'), // 2017-03-22 Edwin Klause: Neues Feld
                    'federal_state_code' => array('name' => 'federal_state_code', 'type' => 'xsd:int'),
                    'default_language' => array('name' => 'default_language', 'type' => 'xsd:string'),
                    'default_currency' => array('name' => 'default_currency', 'type' => 'xsd:string'),
                    'country_code' => array('name' => 'country_code', 'type' => 'xsd:string'), // (AT/DE)
                    'vat_id' => array('name' => 'vat_id', 'type' => 'xsd:string'),
                    'customers_status' => array('name' => 'customers_status', 'type' => 'xsd:int'),
                    'password' => array('name' => 'password', 'type' => 'xsd:string'),
                    'account_type' => array('name' => 'account_type', 'type' => 'xsd:int'), // 2017-03-22 Edwin Klause: Neues Feld
                    'payment_unallowed' => array('name' => 'payment_unallowed', 'type' => 'xsd:string'), // 2017-03-22 Edwin Klause: Neues Feld
                    'shipping_unallowed' => array('name' => 'shipping_unallowed', 'type' => 'xsd:string'), // 2017-03-22 Edwin Klause: Neues Feld
                    'campaign_id' => array('name' => 'campaign_id', 'type' => 'xsd:int'), // 2017-03-22 Edwin Klause: Neues Feld
                    'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// 2013-04-02: Kapselt Inidividualfelder / Optional                    
                    
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),
            array(// neues customers_item: nutzt die internen XT classen und soweit möglich dieselben Datenstrukturen   
                'name' => 'customersItemXT',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'customers_id' => array('name' => 'customers_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'customers_cid' => array('name' => 'customers_cid', 'type' => 'xsd:string'),
                    'customers_vat_id' => array('name' => 'customers_vat_id', 'type' => 'xsd:string'),
                    'customers_vat_id_status' => array('name' => 'customers_vat_id_status', 'type' => 'xsd:int'),
                    'customers_status' => array('name' => 'customers_status', 'type' => 'xsd:int'),
                    'customers_email_address' => array('name' => 'customers_email_address', 'type' => 'xsd:string'),
                    'account_type' => array('name' => 'account_type', 'type' => 'xsd:int'),
                    'payment_unallowed' => array('name' => 'payment_unallowed', 'type' => 'xsd:string'),
                    'shipping_unallowed' => array('name' => 'shipping_unallowed', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'shop_id' => array('name' => 'shop_id', 'type' => 'xsd:string'),
                    'customers_default_currency' => array('name' => 'customers_default_currency', 'type' => 'xsd:string'),
                    'customers_default_language' => array('name' => 'customers_default_language', 'type' => 'xsd:string'),
                    'campaign_id' => array('name' => 'campaign_id', 'type' => 'xsd:int'),
                    'customer_addresses' => array('name' => 'customer_addresses', 'type' => 'tns:customers_addressXTList'),
                    //'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// Todo   
                ),               
                
                'attrs' => array(),
                'arrayType' => ''
            ),
            array( // neues customers_item: nutzt die internen XT Klassen und soweit möglich dieselben Datenstrukturen  
                'name' => 'customers_addressXT',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'customers_id' => array('name' => 'customers_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'address_book_id' => array('name' => 'address_book_id', 'type' => 'xsd:int'),
                    'customers_gender' => array('name' => 'customers_gender', 'type' => 'xsd:string'),
                    'customers_title' => array('name' => 'customers_title', 'type' => 'xsd:string'), // 2018-06-26 Edwin Klause: Neues Feld
                    'customers_dob' => array('name' => 'customers_dob', 'type' => 'xsd:dateTime'),
                    'customers_phone' => array('name' => 'customers_phone', 'type' => 'xsd:string'),
                    'customers_fax' => array('name' => 'customers_fax', 'type' => 'xsd:string'),
                    'customers_company' => array('name' => 'customers_company', 'type' => 'xsd:string'),
                    'customers_company_2' => array('name' => 'customers_company_2', 'type' => 'xsd:string'),
                    'customers_company_3' => array('name' => 'customers_company_3', 'type' => 'xsd:string'),
                    'customers_firstname' => array('name' => 'customers_firstname', 'type' => 'xsd:string'),
                    'customers_lastname' => array('name' => 'customers_lastname', 'type' => 'xsd:string'),
                    'customers_street_address' => array('name' => 'customers_street_address', 'type' => 'xsd:string'),
                    'customers_address_addition' => array('name' => 'customers_address_addition', 'type' => 'xsd:string'), // 2018-06-26 Edwin Klause: Neues Feld
                    'customers_suburb' => array('name' => 'customers_suburb', 'type' => 'xsd:string'),
                    'customers_postcode' => array('name' => 'customers_postcode', 'type' => 'xsd:string'),
                    'customers_city' => array('name' => 'customers_city', 'type' => 'xsd:string'),
                    'customers_country_code' => array('name' => 'customers_country_code', 'type' => 'xsd:string'),
                    'customers_federal_state_code' => array('name' => 'customers_federal_state_code', 'type' => 'xsd:string'),
                    'address_class' => array('name' => 'address_class', 'type' => 'xsd:string'),
                    'date_added' => array('name' => 'date_added', 'type' => 'xsd:dateTime'),
                    'last_modified' => array('name' => 'last_modified', 'type' => 'xsd:dateTime'),
                    'customers_mobile_phone' => array('name' => 'customers_mobile_phone', 'type' => 'xsd:string'), //2015-11-05 Edwin Klause: Neues Feld
                    'PPPLUS_EMAIL' => array('name' => 'PPPLUS_EMAIL', 'type' => 'xsd:string'),
                    //'indivFieldsList' => array('name' => 'indivFieldsList', 'type' => 'tns:indivFieldsList'),// Todo
                ),
                'attrs' => array(),
                'arrayType' => ''
            ), 
            array(
                'name' => 'customers_addressXTList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:customers_addressXT[]')),
                'arrayType' => 'tns:customers_addressXT'
            ), 
            
            // Rückgabe von getCustomers
            array(
                'name' => 'getCustomersResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'customersItemXT_List' => array('name' => 'customersItemXT_List', 'type' => 'tns:customersItemXT_List'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''),
            
            /* getCustomers */
            array(
                'name' => 'customersItemXT_List',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:customersItemXT[]')),
                'arrayType' => 'tns:customersItemXT'), 
            
            
            
            
            
            // Content
            array(
                'name' => 'contentItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'content_id' => array('name' => 'content_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'content_parent' => array('name' => 'content_parent', 'type' => 'xsd:int'),
                    'content_status' => array('name' => 'content_status', 'type' => 'xsd:int'),
                    'content_hook' => array('name' => 'content_hook', 'type' => 'xsd:int'),
                    'content_form' => array('name' => 'content_form', 'type' => 'xsd:string'),
                    'content_image' => array('name' => 'content_image', 'type' => 'xsd:string'),
                    'link_ssl' => array('name' => 'link_ssl', 'type' => 'xsd:int'),
                    'content_sort' => array('name' => 'content_sort', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),

            array(
                'name' => 'contentItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentItem[]')
                ),
                'arrayType' => 'tns:contentItem'
            ),

            array(
                'name' => 'contentResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'content_id' => array('name' => 'content_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),

            array(
                'name' => 'contentResultItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentResultItem[]')
                ),
                'arrayType' => 'tns:contentResultItem'
            ),

            // Content Blocks
            array(
                'name' => 'contentBlockItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'block_id' => array('name' => 'block_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'block_tag' => array('name' => 'block_tag', 'type' => 'xsd:string'),
                    'content_status' => array('name' => 'content_status', 'type' => 'xsd:int'),
                    'block_status' => array('name' => 'block_status', 'type' => 'xsd:int'),
                    'block_protected' => array('name' => 'block_protected', 'type' => 'xsd:int'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),


            array(
                'name' => 'contentBlocksItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentBlockItem[]')
                ),
                'arrayType' => 'tns:contentBlockItem'
            ),

            array(
                'name' => 'contentBlockResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'block_id' => array('name' => 'content_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),

            array(
                'name' => 'contentBlocksResultItemList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentBlockResultItem[]')
                ),
                'arrayType' => 'tns:contentBlockResultItem'
            ),




            // Content Images
            array(//begin- productsImageItem base
                'name' => 'contentImages',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' =>
                    array(
                        'content_id' => array('name' => 'content_id', 'type' => 'xsd:int'),
                        'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                        'image_name' => array('name' => 'image_name', 'type' => 'xsd:string'), // name des Bildes
                        'image' => array('name' => 'image', 'type' => 'xsd:base64Binary'), // daten
                        //2015-11-05: Blackbit Anpassung
                        'image_hash' => array('name' => 'image_hash', 'type' => 'xsd:string'), // Hash des gesendeten Bildes
                        'image_url' => array('name' => 'image_path', 'type' => 'xsd:string'), // URL zum Bild
                        'content_images' => array('name' => 'content_images', 'type' => 'tns:contentImagesList'),
                    ),
                'attrs' => array(),
                'arrayType' => ''
            ),

            array(//
                'name' => 'contentImagesList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentImages[]')
                ),
                'arrayType' => 'tns:contentImages'
            ),


            array(//begin- productResultItem base
                'name' => 'contentImagesResultItem',
                'typeClass' => 'complexType',
                'phpType' => 'struct',
                'compositor' => 'all',
                'restrictionBase' => '',
                'elements' => array(
                    'content_id' => array('name' => 'content_id', 'type' => 'xsd:int'),
                    'external_id' => array('name' => 'external_id', 'type' => 'xsd:string'),
                    'result' => array('name' => 'result', 'type' => 'xsd:boolean'),
                    'message' => array('name' => 'message', 'type' => 'xsd:string'),
                ),
                'attrs' => array(),
                'arrayType' => ''
            ),


            array(
                'name' => 'contentImagesResultList',
                'typeClass' => 'complexType',
                'phpType' => 'array',
                'compositor' => '',
                'restrictionBase' => 'SOAP-ENC:Array',
                'elements' => array(),
                'attrs' => array(
                    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:contentImagesResultItem[]')
                ),
                'arrayType' => 'tns:contentImagesResultItem'
            ),


            
          
        ); // end main array

        ($plugin_code = $xtPlugin->PluginCode('SoapTypes.class.php:getTypesArray_bottom')) ? eval($plugin_code) : false;

        return $aTypesArray;
    }

// end method
}

// end class Soap Types
?>