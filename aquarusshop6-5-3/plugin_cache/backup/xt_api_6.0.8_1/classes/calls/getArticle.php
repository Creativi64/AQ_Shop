<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * 
 * @global type $db
 * @global type $mediaImages
 * @global type $mediaFiles
 * @param type $user
 * @param type $pass
 * @param type $products_id
 * @param type $external_id
 * @param type $indivFieldsList
 * @return string
 */
function getArticle($user = '', $pass = '', $products_id = -1, $external_id = '', $indivFieldsList) {
    global $store_handler;
    
    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Product ID ermitteln über external_id, falls gesetzt
    if( strlen( $external_id ) > 0 ){
        $products_id = SoapHelper::getProductID($external_id, false, $products_id);
    }
    
    // product nicht gefunden anhand von IDs
    if( $products_id === false ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " unable to find article by external_id: " . $external_id;
        return $result;
    }
    
    global $db;

    // Artikeldatensatz anhand products_id laden
    $rs = $db->Execute("select * from " . TABLE_PRODUCTS . " where products_id =" . $products_id );
    
    if ($rs->RecordCount() == 0) {
        // Product NICHT in DB gefunden
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " unable to find article in db";
        return $result;
    }
  
    //$pTableProducts = array();
    $pTableProducts = $rs->fields;
   
    // ArtikelDaten für Rückgabe
    $articleItem = array();
    
    // TABLE_PRODUCTS Daten mappen
    $articleItem["products_id"] = getValue( $pTableProducts["products_id"], "xsd:int", 0 );
    $articleItem["external_id"] = getValue( $pTableProducts["external_id"], "xsd:string", "" );
    $articleItem["permission_id"] = getValue( $pTableProducts["permission_id"], "xsd:int", 0 );
    $articleItem["products_owner"] = getValue( $pTableProducts["products_owner"], "xsd:int", 0 );
    $articleItem["products_ean"] = getValue( $pTableProducts["products_ean"], "xsd:string", "" );
    $articleItem["products_quantity"] = getValue( $pTableProducts["products_quantity"], "xsd:decimal", 0 );
    $articleItem["products_average_quantity"] = getValue( $pTableProducts["products_average_quantity"], "xsd:int", 0 );
    $articleItem["products_shippingtime"] = getValue( $pTableProducts["products_shippingtime"], "xsd:int", 0 );
    $articleItem["products_model"] = getValue( $pTableProducts["products_model"], "xsd:string", "" );
    $articleItem["price_flag_graduated_all"] = getValue( $pTableProducts["price_flag_graduated_all"], "xsd:int", 0 );
    $articleItem["price_flag_graduated_1"] = getValue( $pTableProducts["price_flag_graduated_1"], "xsd:int", 0 );
    $articleItem["price_flag_graduated_2"] = getValue( $pTableProducts["price_flag_graduated_2"], "xsd:int", 0 );
    $articleItem["price_flag_graduated_3"] = getValue( $pTableProducts["price_flag_graduated_3"], "xsd:int", 0 );
    $articleItem["products_sort"] = getValue( $pTableProducts["products_sort"], "xsd:int", 0 );
    $articleItem["products_option_master_price"] = getValue( $pTableProducts["products_option_master_price"], "xsd:string", "" );
    $articleItem["ekomi_allow"] = getValue( $pTableProducts["ekomi_allow"], "xsd:int", 0 );
    $articleItem["products_image"] = getValue( $pTableProducts["products_image"], "xsd:string", "" ); // Hauptbild
    $articleItem["products_price"] = getValue( $pTableProducts["products_price"], "xsd:decimal", 0 );
    $articleItem["date_added"] = getValue( SoapHelper::checkIsoDate($pTableProducts["date_added"]), "xsd:dateTime", SoapHelper::checkIsoDate($pTableProducts["date_added"]));
    $articleItem["last_modified"] = getValue( SoapHelper::checkIsoDate($pTableProducts["last_modified"]), "xsd:dateTime", SoapHelper::checkIsoDate($pTableProducts["last_modified"]));
    $articleItem["date_available"] = getValue( SoapHelper::checkIsoDate($pTableProducts["date_available"]), "xsd:dateTime", SoapHelper::checkIsoDate($pTableProducts["date_available"]));
    $articleItem["products_weight"] = getValue( $pTableProducts["products_weight"], "xsd:decimal", 0 );
    $articleItem["products_status"] = getValue( $pTableProducts["products_status"], "xsd:int", 0 );
    $articleItem["products_tax_class_id"] = getValue( $pTableProducts["products_tax_class_id"], "xsd:int", 0 );
    $articleItem["product_template"] = getValue( $pTableProducts["product_template"], "xsd:string", "" );
    $articleItem["product_list_template"] = getValue( $pTableProducts["product_list_template"], "xsd:string", "" );
    $articleItem["products_option_template"] = getValue( $pTableProducts["products_option_template"], "xsd:string", "" ); // 2017-02-28 : Neu products_option_template
    $articleItem["products_option_list_template"] = getValue( $pTableProducts["products_option_list_template"], "xsd:string", "" ); // 2017-02-28 : Neu products_option_list_template
    $articleItem["manufacturers_id"] = getValue( $pTableProducts["manufacturers_id"], "xsd:int", 0 );
    $articleItem["products_ordered"] = getValue( $pTableProducts["products_ordered"], "xsd:int", 0 );
    $articleItem["products_transactions"] = getValue( $pTableProducts["products_transactions"], "xsd:int", 0 ); // 2017-02-28 : Neu products_transactions
    $articleItem["total_downloads"] = getValue( $pTableProducts["total_downloads"], "xsd:int", 0 ); // 2017-02-28: Neues Feld total_downloads
    $articleItem["products_canonical_master"] = getValue( $pTableProducts["products_canonical_master"], "xsd:int", 0 ); // 2017-02-28: Neues Feld products_canonical_master
    $articleItem["group_discount_allowed"] = getValue( $pTableProducts["group_discount_allowed"], "xsd:int", 0 ); // 2017-02-28: Neues Feld group_discount_allowed
    $articleItem["google_product_cat"] = getValue( $pTableProducts["google_product_cat"], "xsd:int", 0 ); // 2017-02-28: Neues Feld google_product_cat
    $articleItem["products_fsk18"] = getValue( $pTableProducts["products_fsk18"], "xsd:int", 0 );
    $articleItem["products_vpe"] = getValue( $pTableProducts["products_vpe"], "xsd:int", 0 );
    $articleItem["products_unit"] = getValue( $pTableProducts["products_unit"], "xsd:int", 0 );
    $articleItem["products_vpe_status"] = getValue( $pTableProducts["products_vpe_status"], "xsd:int", 0 );
    $articleItem["products_vpe_value"] = getValue( $pTableProducts["products_vpe_value"], "xsd:decimal", 0 );
    $articleItem["products_average_rating"] = getValue( $pTableProducts["products_average_rating"], "xsd:decimal", 0 );
    $articleItem["products_rating_count"] = getValue( $pTableProducts["products_rating_count"], "xsd:int", 0 );
    $articleItem["products_digital"] = getValue( $pTableProducts["products_digital"], "xsd:int", 0 );
    $articleItem["flag_has_specials"] = getValue( $pTableProducts["flag_has_specials"], "xsd:int", 0 );
    $articleItem["products_serials"] = getValue( $pTableProducts["flag_has_specials"], "xsd:int", 0 );
    $articleItem["products_master_flag"] = getValue( $pTableProducts["products_master_flag"], "xsd:int", 0 ); //typ von string auf int geändert
    $articleItem["products_image_from_master"] = getValue( $pTableProducts["products_image_from_master"], "xsd:int", 0 ); 
    $articleItem["products_master_model"] = getValue( $pTableProducts["products_master_model"], "xsd:string", "" );
    $articleItem["products_master_slave_order"] = getValue( $pTableProducts["products_master_slave_order"], "xsd:int", 0 ); 
    
    $articleItem["products_description_from_master"] = getValue( $pTableProducts["products_description_from_master"], "xsd:int", 0 ); // 2018-06-26: Neues Feld products_description_from_master
    $articleItem["products_short_description_from_master"] = getValue( $pTableProducts["products_short_description_from_master"], "xsd:int", 0 ); // 2018-06-26: Neues Feld products_short_description_from_master
    $articleItem["products_keywords_from_master"] = getValue( $pTableProducts["products_keywords_from_master"], "xsd:int", 0 ); // 2018-06-26: Neues Feld products_keywords_from_master
    $articleItem["products_keywords_from_master"] = getValue( $pTableProducts["products_keywords_from_master"], "xsd:int", 0 ); // 2018-06-26: Neues Feld products_keywords_from_master
    $articleItem["ms_load_masters_main_img"] = getValue( $pTableProducts["ms_load_masters_main_img"], "xsd:int", 0 ); // 2018-06-26: Neues Feld ms_load_masters_main_img
    $articleItem["ms_load_masters_free_downloads"] = getValue( $pTableProducts["ms_load_masters_free_downloads"], "xsd:int", 0 ); // 2018-06-26: Neues Feld ms_load_masters_free_downloads
    $articleItem["ms_open_first_slave"] = getValue( $pTableProducts["ms_open_first_slave"], "xsd:int", 0 ); // 2018-06-26: Neues Feld ms_open_first_slave
    $articleItem["ms_show_slave_list"] = getValue( $pTableProducts["ms_show_slave_list"], "xsd:int", 0 ); // 2018-06-26: Neues Feld ms_show_slave_list
    $articleItem["ms_filter_slave_list"] = getValue( $pTableProducts["ms_filter_slave_list"], "xsd:int", 0 ); // 2018-06-26: Neues Feld ms_filter_slave_list
    

    // 2017-02-28: Angeforderte IndivFields in Rückgabe einmappen 
    if (is_array($indivFieldsList)) {
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        $indivFieldsListProduct =  array();
        foreach ($indivFieldsList as $key => $indivField) {
            if ($indivField["dstTable"] == "TABLE_PRODUCTS") {
                $indivFieldClone =  $indivField;
                $indivFieldClone["value"] = getValue( $pTableProducts[$indivField["sqlFieldName"]], "xsd:string", "" );
                $indivFieldsListProduct[] = $indivFieldClone;
            }
        }
        $articleItem["indivFieldsList"] = $indivFieldsListProduct;
    }
  
    // MediaFiles des Artikles holen
    // Tabelle xt_image_type auslesen für Klasse product und default
    $imTypes = array();
    $imTypeRS = $db->Execute("SELECT * FROM " . TABLE_IMAGE_TYPE . " WHERE class='product' or class='default'");
    while (!$imTypeRS->EOF) {
        $imTypes[] = $imTypeRS->fields;
        // zum nächsten Treffer 
        $imTypeRS->MoveNext();
    }
    
    // Alle Bilder eines Artikels anziehen
    global $mediaImages, $mediaFiles;
    $media_images = $mediaImages->get_media_images($articleItem["products_id"], "product");
    
    // Array für alle Bildinfos und Links
    $mediaUrls = array();
    
    // über alle Bilder laufen
    if( is_array( $media_images["images"] )  ){
        foreach( $media_images["images"] as $key => $imageData ){

            // pro Bild über alle Versionen laufen
            foreach( $imTypes as $itKey => $imType ){
                // Gibt es Bild mit Namen im Folder?
                $file = _SRV_WEBROOT._SRV_WEB_IMAGES.$imType['folder'].'/'.$imageData['data']['file'];
                if (file_exists($file)) {
                   // Bild da
                  $mediaUrl = array();
                  $mediaUrl["type"] =  "image";
                  $mediaUrl["file"] =  $imageData['data']['file'];
                  $mediaUrl["width"] =  $imType['width'];
                  $mediaUrl["height"] =  $imType['height'];
                  $mediaUrl["folder"] = $imType['folder'];
                  $mediaUrl["url"] = _SYSTEM_BASE_HTTP._SRV_WEB._SRV_WEB_IMAGES.$imType['folder'].'/'.$imageData['data']['file'];
                  $mediaUrls[] = $mediaUrl;
                }

            }

        }
    
    }
    
    $articleItem["products_media_urlList"] = $mediaUrls; 
    
   // Infos über alle stores holen
   $stores = $store_handler->getStores();
   $firstStoreId = $stores[0]['id'];
   
   // Hilfsarray für die storedaten key = storeId
   $productStartpages = array();
   
   $firstStoreSet = false;
   
   $pageRS = $db->Execute("SELECT * FROM " . DB_PREFIX . "_startpage_products WHERE products_id=" . $products_id);
   while (!$pageRS->EOF) {
       
       $storeId = $pageRS->fields['shop_id'];
       $productStartpages[$storeId]["products_startpage"] = getValue( 1, "xsd:int", 0 );
       $productStartpages[$storeId]["products_startpage_sort"] = getValue( $pageRS->fields['startpage_products_sort'], "xsd:int", 0 );      
       
        // Die alte Struktur nur mit den Daten des ersten stores füllen
        if( $firstStoreId === $storeId){   
            // 2014-12-03: Startpage Items wurden wegen Multishop funktionalität in eigene Tabelle verschoben
            $articleItem["products_startpage"] = getValue( 1, "xsd:int", 0 );
            $articleItem["products_startpage_sort"] = getValue( $pageRS->fields['startpage_products_sort'], "xsd:int", 0 );           
            $firstStoreSet = true;
        }    
        // zum nächsten Treffer 
        $pageRS->MoveNext();
   }
   
   // in Tabelle kein sort Eintrag für Artikel und ersten shop, dann auf False!
   if( ! $firstStoreSet ){
      $articleItem["products_startpage"] = getValue( 0, "xsd:int", 0 );    
   }

    // Hilfsarraydaten mit Multisprachen für startpages in products Struktur kopieren
    $products_startpageList = array();
    
    foreach( $productStartpages as $storeId => $startPage ){
        $startPage["products_store_id"] = $storeId;
        
        array_push( $products_startpageList, $startPage );
    }
    $articleItem["products_startpageList"] = $products_startpageList;  
 
    // TABLE_PRODUCTS_DESCRIPTION Daten mappen MULTILANG
    // Hilfsarray für die storedaten key = storeId
    $productDescriptions = array();
    $descRS = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id=" . $products_id);
    
    // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
    while (!$descRS->EOF) {
        // Sprachcode der Beschreibung holen
        $lng = $descRS->fields['language_code'];
        // Store_ID der Beschreibung holen
        $storeId = $descRS->fields['products_store_id'];
        
        // Hilfsarray befüllen je nach storeId
        $productDescriptions[ $storeId ]["products_name"][$lng] = getValue( $descRS->fields['products_name'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["products_description"][$lng] = getValue( $descRS->fields['products_description'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["products_short_description"][$lng] = getValue( $descRS->fields['products_short_description'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["products_keywords"][$lng] = getValue( $descRS->fields['products_keywords'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["url"][$lng] = getValue( $descRS->fields['products_url'], "xsd:string", "" );
        
        // Die alte Struktur nur mit den Daten des ersten stores füllen
        if( $firstStoreId === $storeId){
            $articleItem["products_name"][$lng] = getValue( $descRS->fields['products_name'], "xsd:string", "" );
            $articleItem["products_description"][$lng] = getValue( $descRS->fields['products_description'], "xsd:string", "" );
            $articleItem["products_short_description"][$lng] = getValue( $descRS->fields['products_short_description'], "xsd:string", "" );
            $articleItem["products_keywords"][$lng] = getValue( $descRS->fields['products_keywords'], "xsd:string", "" );
            $articleItem["url"][$lng] = getValue( $descRS->fields['products_url'], "xsd:string", "" );
        }
         // zum nächsten Treffer 
        $descRS->MoveNext();    
    }
    
    // TABLE_SEO_URL Daten mappen MULTILANG
    $seoRS = $db->Execute("SELECT * FROM " . TABLE_SEO_URL . " WHERE link_id=" . $products_id . " and link_type = 1");
            
    // Über DB Treffer laufen TABLE_SEO_URL
    while (!$seoRS->EOF) {
        // Sprachcode der Beschreibung holen
        $lng = $seoRS->fields['language_code'];
        $storeId = $seoRS->fields['store_id'];
        
         
        // Hilfsarray befüllen je nach storeId
        $productDescriptions[ $storeId ]["meta_description"][$lng] = getValue( $seoRS->fields['meta_description'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["meta_title"][$lng] = getValue( $seoRS->fields['meta_title'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["meta_keywords"][$lng] = getValue( $seoRS->fields['meta_keywords'], "xsd:string", "" );
        $productDescriptions[ $storeId ]["seo_url"][$lng] = getValue( $seoRS->fields['url_text'], "xsd:string", "" );        
        
        
        // Die alte Struktur nur mit den Daten des ersten stores füllen
        if( $firstStoreId === $storeId){
            $articleItem["meta_description"][$lng] = getValue( $seoRS->fields['meta_description'], "xsd:string", "" );
            $articleItem["meta_title"][$lng] = getValue( $seoRS->fields['meta_title'], "xsd:string", "" );
            $articleItem["meta_keywords"][$lng] = getValue( $seoRS->fields['meta_keywords'], "xsd:string", "" );
            $articleItem["seo_url"][$lng] = getValue( $seoRS->fields['url_text'], "xsd:string", "" );
        }
        
        // zum nächsten Treffer 
        $seoRS->MoveNext();

    }
    
    
    // Hilfsarraydaten mit Multisprachen für Descriptions und SEO in products Struktur kopieren
    $productDescriptionsList = array();
    
    foreach( $productDescriptions as $storeId => $storeDescriptions ){
       
        $storeDescriptions["products_store_id"] = $storeId;
        array_push( $productDescriptionsList, $storeDescriptions );
    }
    $articleItem["productDescriptionsList"] = $productDescriptionsList;
    
    // TABLE_CUSTOMERS_STATUS 
    // Alle PRICE GROUPS holen
    $priceGroups = array();
    
    // "all" Gruppe direkt rein
    $priceGroups[] = "all";
    
    $pgRS = $db->Execute("SELECT customers_status_id FROM " . TABLE_CUSTOMERS_STATUS );
    
    // Über DB Treffer laufen TABLE_CUSTOMERS_STATUS
    while (!$pgRS->EOF) {
        $priceGroups[] = $pgRS->fields['customers_status_id'];
        
        // zum nächsten Treffer 
        $pgRS->MoveNext();
    }
 
    // TABLE_PRODUCTS_PRICE_SPECIAL Daten mappen 
    $specialpriceList = array(); // sammelt Liste von SpecialPrices
    
    $psRS = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_PRICE_SPECIAL . " WHERE products_id=" . $products_id );
            
    // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
    while (!$psRS->EOF) {
        
        // Container für einzelen special price
        $specialprice = array();
        
        $specialprice["special_price"] = getValue( $psRS->fields['specials_price'], "xsd:decimal", 0 );
        $specialprice["status"] = getValue( $psRS->fields['status'], "xsd:int", 0 ); 
        $specialprice["date_available"] = getValue( $psRS->fields['date_available'], "xsd:dateTime", $DEF_DATETIME ); 
        $specialprice["date_expired"] = getValue( $psRS->fields['date_expired'], "xsd:dateTime", $DEF_DATETIME ); 
        $specialprice["group_permission_all"] = getValue( $psRS->fields['group_permission_all'], "xsd:int", 0 ); 
    
        
        // Array für alle group_permissiona die auf true sind!
        $gps = array();
        
        // Alle group_permission_ Felder suchen
        foreach( $psRS->fields as $fname => $fvalue ){
            if( startsWith($fname,"group_permission_" ) ){
                // ist permission auf true? dann ID in array
                if( $fvalue = 1){
                    $gps[] = (int) substr( $fname, 17); 
                }
            }   
        }
        
        $specialprice["group_permissions"] = $gps;
        
        // in Liste
        $specialpriceList[] = $specialprice;
        
        // zum nächsten Treffer 
        $psRS->MoveNext();

    }
    
    // Einmappen
    $articleItem["products_special_prices"] = $specialpriceList;
    
    
    // TABLE_PRODUCTS_TO_CATEGORIES Daten mappen 
    $products_categories = array();
    $productCategoriesList = array();
   
    // über alle stores laufen
    $stores = $store_handler->getStores();
    foreach ($stores as $store) {
        // $store['id']
        $catRS = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id=" . $products_id . " AND store_id=" . $store['id'] );
        
        $productCategoriesStoreList = array();
        $productExternalCategoriesStoreList = array();
        
        
        // Über DB Treffer laufen 
        while (!$catRS->EOF) {
            // Die alte Struktur nur mit den Daten des ersten stores füllen
            if( $firstStoreId === $store['id'] ){
                $products_categories[] = getValue( $catRS->fields['categories_id'], "xsd:int", 0 );
            }
            
            
            $productCategoriesStoreList[] = getValue( $catRS->fields['categories_id'], "xsd:int", 0 );
            
            // zu external ID auflösen
            
            
            $external_id = SoapHelper::getCategoriesExternalIDByID( $catRS->fields['categories_id'] ) ;
            
            if( ! $external_id === false ){
                $productExternalCategoriesStoreList[] = $external_id;
            }
            
            // zum nächsten Treffer 
            $catRS->MoveNext();
            
        }    

        $productCategoriesList[ $store['id'] ]["categories_store_id"] = $store['id'];
        $productCategoriesList[ $store['id'] ]["products_categories"] = $productCategoriesStoreList;
        $productCategoriesList[ $store['id'] ]["products_external_categories"] = $productExternalCategoriesStoreList;
    
    }
    
    // Einmappen neue cat struktur
    $articleItem["productCategoriesList"] = $productCategoriesList;
    
    
    // Einmappen
    $articleItem["products_categories"] = $products_categories;
    // ACHTUNG: TODO: Prüfen, ob Feld doppelt! Dann raus!
    $articleItem["categories"] = $products_categories;
   

    // TABLE_PRODUCTS_PRICE_GROUPs (mehrere Tabellen) Daten mappen 
    $pricesetList = array();
    
    // Über alle vorhandene priceGroups laufen zB "all",1,2,4,6 - Daraus wird Tabellenname gebaut
    foreach( $priceGroups as $priceGroup ){
        $pgRS = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_PRICE_GROUP . $priceGroup . " WHERE products_id=" . $products_id );
        
        // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
        while (!$pgRS->EOF) {

            // Neuen priceset anlegen
            $priceset = array();
            
            $priceset["price"] = getValue( $pgRS->fields['price'], "xsd:decimal", 0 );
            $priceset["group"] = $priceGroup;
            $priceset["quantity"] = getValue( $pgRS->fields['discount_quantity'], "xsd:int", 0 );
            
            
            $pricesetList[] = $priceset;
         
            // zum nächsten Treffer 
            $pgRS->MoveNext();

        }           
        
    }    
    // Einmappen
    $articleItem["products_prices"] = $pricesetList;
    
    
    // TABLE_PRODUCTS_CROSS_SELL Daten mappen 
    // ACHTUNG: Hier wird jeweils das ProduktModell als XSell angegeben
    $products_cross_sell = array();   
    $csRS = $db->Execute("SELECT t2.products_id_cross_sell,t1.products_model FROM " . TABLE_PRODUCTS . " AS t1 LEFT JOIN " . TABLE_PRODUCTS_CROSS_SELL . " AS t2 on t1.products_id=t2.products_id_cross_sell WHERE t2.products_id=" . $products_id); 
    
    // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
    while (!$csRS->EOF) {
        $products_cross_sell[] = getValue( $csRS->fields['products_model'], "xsd:string", "");
    
        // zum nächsten Treffer 
        $csRS->MoveNext();
    }         
    // Einmappen
    $articleItem["products_cross_sell"] = $products_cross_sell;
    
    // TABLE_PRODUCTS_TO_ATTRIBUTES Daten mappen 
    $products_attributes_idsList = array();
    
    $paRS = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_TO_ATTRIBUTES . " WHERE products_id=" . $products_id );
        
    // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
    while (!$paRS->EOF) {

        $products_attributes_ids = array();
        
        $products_attributes_ids["attributes_id"] = getValue( $paRS->fields['attributes_id'], "xsd:int", 0 );
        $products_attributes_ids["attributes_parent_id"] = getValue( $paRS->fields['attributes_parent_id'], "xsd:int", 0 );
        
        
        $products_attributes_idsList[] = $products_attributes_ids;
        
        // zum nächsten Treffer 
        $paRS->MoveNext();
    }             
    // Einmappen
    $articleItem["products_attributes"] = $products_attributes_idsList;
    
    
    //
    // xt_products_permission Daten mappen 
    //       
    
    $permissionItemList = array();
    // TABLE_PRODUCTS_PERMISSION
    
    $ppRS = $db->Execute("SELECT * FROM ". TABLE_PRODUCTS_PERMISSION . " WHERE pid=" . $products_id );
        
    // Über DB Treffer laufen TABLE_PRODUCTS_DESCRIPTION
    while (!$ppRS->EOF) {

        $permissionItem = array();
        
        $permissionItem["external_id"] = $articleItem["external_id"];
        $permissionItem["permission"] = getValue( $ppRS->fields['permission'], "xsd:int", 0 );
        $permissionItem["pgroup"] = getValue( $ppRS->fields['pgroup'], "xsd:string", "" );
      
        $permissionItemList[] = $permissionItem;
        
        // zum nächsten Treffer 
        $ppRS->MoveNext();

    }             
            
    $articleItem["permissionList"] = $permissionItemList;
    
    
 
    
    
    
    //
    // TABLE_MEDIA + TABLE_MEDIA_LINK Daten mappen 
    //        
    
    $products_imageList = array();
    
    $meRS = $db->Execute("SELECT t1.link_id, t2.id, t2.file FROM " . TABLE_MEDIA_LINK . " AS t1 LEFT JOIN " . TABLE_MEDIA . " AS t2 ON t1.m_id = t2.id WHERE t1.link_id=" . $products_id . " and t1.class = 'product'" );
    
    while (!$meRS->EOF) {

        $products_imageItem = array();
        
        $products_imageItem["image_name"] = getValue( $meRS->fields['file'], "xsd:string", "" );
        $products_imageItem["type"] = "images";
        $products_imageItem["id"] = getValue( $meRS->fields['id'], "xsd:int", "" );

        $products_imageList[] = $products_imageItem;


        // zum nächsten Treffer 
        $meRS->MoveNext();

    }                 
    
    $articleItem["products_images"] = $products_imageList;        
            
    
    
    
    
    //
    // TABLE_MEDIA_LINK Daten mappen = Zugehörige Medienfiles
    //    
    $medXtRS = $db->Execute("SELECT m_id FROM " . TABLE_MEDIA_LINK . " WHERE link_id=" . $products_id . " and class = 'product' and type='media'");

    
    $mediaItemXTLinkList = array();
    
        
    // Über DB Treffer laufen TABLE_MEDIA_LINK
    while (!$medXtRS->EOF) {
        
        // container für einzelenen link
        $mediaItemXTLink = array();
        
        $mediaItemXTLink["id"] = getValue( $medXtRS->fields['m_id'], "xsd:int", "" );
          
        
        
        // external_id per sql auflösen
        $extsql = "SELECT external_id FROM " . TABLE_MEDIA . " WHERE id=" . $mediaItemXTLink["id"];
        $mediaItemXTLink["external_id"] =  $db->GetOne($extsql);
        
        // in Liste
        $mediaItemXTLinkList[] = $mediaItemXTLink;
        
        // zum nächsten Treffer 
        $medXtRS->MoveNext();

    }   
    
    
    $articleItem["mediaItemXTLinkList"] = $mediaItemXTLinkList;        
   
    
    
    // Rückgabe    
    $result['result'] = true;
    $result['productsItemExport'] = $articleItem;
    return $result;

 }
?>
