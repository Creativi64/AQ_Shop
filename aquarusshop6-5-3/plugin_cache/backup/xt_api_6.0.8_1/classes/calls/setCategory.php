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

// externe PHP Dateien einbinden
include_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';
include_once _SRV_WEBROOT . 'xtFramework/classes/class.custom_link.php';

/**
 * 
 * @global type $db
 * @global type $language
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $categoryItem
 * @param type $buildNestedSet
 * @return string
 */
function setCategory($user = '', $pass = '', $categoryItem = '', $buildNestedSet = true) {
    global $store_handler;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Ist $categoryItem KEIN array
    if (!is_array($categoryItem)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail. " CategoryItem no array";
        return $result;
    }

    // globale Variablen $db, $language und $xtPlugin nutzen
    global $db, $language,$xtPlugin;
    
    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setCategory.php:setCategory_top')) ? eval($plugin_code) : false;
    
    // Array $data für Kategoriedaten bauen
    $data = array();
    
    // Kategorie ID ermitteln
    if(  isset($categoryItem['external_id']) && strlen($categoryItem['external_id']) > 0 ){
        
        $categoryID = SoapHelper::getCategoriesIDByexternalID($categoryItem['external_id']);

        if( $categoryID === false ){
            
            // Kategorie in DB einfügen
            $type = 'insert';
            
            // anlegen in DB mit external_id
            // kein default value für: category_custom_link, category_custom_link_type und category_custom_link_id in MySQL und MySQL Strict Mode aktiviert ... daher MySQL Error
            $db->Execute("INSERT INTO " . TABLE_CATEGORIES . " (external_id,category_custom_link,category_custom_link_type,category_custom_link_id) VALUES ('" . $categoryItem['external_id'] . "',0,'',0)");
            
            // categories_id setzen anhand von neuer ID aus DB
            $data['categories_id'] = $db->Insert_ID();
            $categoryID = $data['categories_id'];            
        }
        else{
            $type = 'update';
        }
    }
    else{
        // interne Kat ID nutzen! Überhaupt gesetzt?
        if(  isset($categoryItem['categories_id']) && strlen($categoryItem['categories_id']) > 0 ){
           $categoryID = $categoryItem['categories_id']; 
           $type = 'update';
        }
        else{
            // Kategorie in DB einfügen
            $type = 'insert';
            // anlegen in DB mit external_id
            $db->Execute("INSERT INTO " . TABLE_CATEGORIES . " (external_id) VALUES ('')");
            // categories_id setzen anhand von neuer ID aus DB
            $data['categories_id'] = $db->Insert_ID();
            $categoryID = $data['categories_id'];                    
        }
    }  
    
    // Externe Parent Kategorie ID ermitteln, ist external_parent_id gesetzt?
    if(  isset($categoryItem['external_parent_id']) && strlen($categoryItem['external_parent_id']) > 0 ){
        if( $categoryItem['external_parent_id'] == 0 ){
            $parentID = 0;
        }
        else{
            $parentID = SoapHelper::getCategoriesIDByexternalID($categoryItem['external_parent_id']);

            if( $categoryParentID === false ){
                $result['result'] = false;
                $result['external_parent_id'] = $categoryItem['external_parent_id'];
                $result['message'] = SoapHelper::MessageFail . " categorie_id not found for external_parent_id: " . $categoryItem['external_parent_id'];
                return $result;
            }
        }
    }
    else{
        $parentID = $categoryItem['categories_parent_id'];
    }     
    
    
    // 2015-03-26: Update benötigt categories_id in data Objekt
    if( $type === "update"  ){
        $data["categories_id"] = $categoryID;
    }
    
    // Standard Kategoriedaten mappen in $data
    $data['parent_id'] = $parentID;
    $data['external_id'] = $categoryItem['external_id'];
    $data['sort_order'] = $categoryItem['sort_order'];
    $data['categories_status'] = $categoryItem['categories_status'];
    $data['products_sorting'] = $categoryItem['products_sorting'];
    $data['products_sorting2'] = $categoryItem['products_sorting2'];
    $data['google_product_cat'] = $categoryItem['google_product_cat'];
    $data['categories_template'] = $categoryItem['categories_template'];
    $data['listing_template'] = $categoryItem['listing_template'];
    $data['permission_id'] = $categoryItem['permission_id'];
    $data['top_category'] = $categoryItem['top_category'];

    if( $categoryItem['category_custom_link'] != null ){
        $data['category_custom_link'] = $categoryItem['category_custom_link'];
    }
    else{
        $data['category_custom_link'] = 0;
    }
   
    if( $categoryItem['category_custom_link_type'] != null ){
        $data['category_custom_link_type'] = $categoryItem['category_custom_link_type'];
    }
    else{
        $data['category_custom_link_type'] = null;
    }

    if( $categoryItem['category_custom_link_id'] != null ){
        $data['category_custom_link_id'] = $categoryItem['category_custom_link_id'];
    }
    else{
        $data['category_custom_link_id'] = 0;
    }
    
    
    
    //  Neue Sprachmapping Fassung analog zu setArticle
    // Hier die Mehrsprachenfelder umsetzen in die XT Struktur - > einmappen in $datat und dann alte Sprachenstruktur entfernen!
    // Anpassung auf multiStore Beschreibungen mit Fallback auf alte WSDL   
    // Neues array 'categoryDescriptionsList' für Beschreibungen wird genutzt   
    if ($categoryItem['categoryDescriptionsList'] && sizeof($categoryItem['categoryDescriptionsList']) > 0) {

        // über alle einzelnen descritionItems laufen
        foreach ($categoryItem['categoryDescriptionsList'] as $categoryDescriptionsItem) {
            reorgLangFieldStores($categoryDescriptionsItem, "categories_description", $data, "categories_description", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "categories_description_bottom", $data, "categories_description_bottom", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "categories_name", $data, "categories_name", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "meta_description", $data, "meta_description", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "meta_keywords", $data, "meta_keywords", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "meta_title", $data, "meta_title", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "seo_url", $data, "url_text", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "categories_heading_title", $data, "categories_heading_title", $categoryDescriptionsItem['categories_store_id']);
            reorgLangFieldStores($categoryDescriptionsItem, "link_url", $data, "link_url", $categoryDescriptionsItem['categories_store_id']);
        }
    } else {
        // Fallback auf alte WSDL mit Beschreibungen in allen Sprachen aber nicht pro shop
        // Dann für alle Shops die gleichen Texte setzen
        // über alle stores laufen
        $stores = $store_handler->getStores();
        foreach ($stores as $store) {
            reorgLangFieldStores($categoryItem, "categories_description", $data, "categories_description", $store['id']);
            reorgLangFieldStores($categoryItem, "categories_description_bottom", $data, "categories_description_bottom", $store['id']);
            reorgLangFieldStores($categoryItem, "categories_name", $data, "categories_name", $store['id']);
            reorgLangFieldStores($categoryItem, "meta_description", $data, "meta_description", $store['id']);
            reorgLangFieldStores($categoryItem, "meta_keywords", $data, "meta_keywords", $store['id']);
            reorgLangFieldStores($categoryItem, "meta_title", $data, "meta_title", $store['id']);
            reorgLangFieldStores($categoryItem, "meta_keywords", $data, "meta_keywords", $store['id']);
            reorgLangFieldStores($categoryItem, "seo_url", $data, "url_text", $store['id']);
            reorgLangFieldStores($categoryItem, "categories_heading_title", $data, "categories_heading_title", $store['id']);
            reorgLangFieldStores($categoryItem, "link_url", $data, "link_url", $store['id']);
        }
    }
    
    // Permissions in $data mappen, damit sie per Object $category in db gemappt werden
    // Aufbau zb $date["shop_1"] = "1"
    if (is_array($categoryItem["permissionList"])) {
        // Über permission Items laufen
        foreach ($categoryItem["permissionList"] as $key => $pItem) {
            // in $data mappen
            $data[$pItem["pgroup"]] = "1";
        }
    }

    // Gibt es Individualfelder für Kategorie Haupttabelle? Mit in $data mappen.
    if (is_array($categoryItem["indivFieldsList"])) {
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach ($categoryItem["indivFieldsList"] as $key => $indivField) {
            // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS"
            if ($indivField["dstTable"] = "TABLE_CATEGORIES") {
                // value mappen in Product
                $data[$indivField["sqlFieldName"]] = SoapHelper::utf8helper($indivField["value"]);
            }
        }
    }

    // Soll "alte" SEO URL gerettet werden?
    if (XT_API_REBUILD_SEO_URLS == 'false' && $type == 'update') {
        $record = $db->Execute("SELECT link_id,language_code,url_text,store_id FROM " . TABLE_SEO_URL . " WHERE link_type=2 AND link_id=" . $categoryID);
        while (!$record->EOF) {
            $data['url_text_store' . $record->fields["store_id"] . "_" . $record->fields["language_code"]] = $record->fields["url_text"];
            $record->MoveNext();
        }$record->Close();
    }

    // Neues Objekt stdClass anlegen
    $obj = new stdClass;
    // Neues category Obejkt anlegen
    $category = new category;

    // In $set_perm Methode setPosition auf "admin" setzen
    $category->setPosition('admin');
    
    
    // Neu: $category->position = "admin";$store_field_exists setzen
    $category->store_field_exists = true;

    // 2013-02-05: Achtung: Das Kategoriebild kann nur mit Methode  _setImage($id, $file) gesetzt werden!
    // ist ein Bild gesetzt?
    if (strlen($categoryItem['categories_image']) > 1) {
        // ACHTUNG: HACK! position MUSS "admin" sein, warum auch immer.....
        $category->position = "admin";

        $setImageResult = $category->_setImage($categoryID, $categoryItem['categories_image']);
    }

    $category->permission->_where = "";

    // Kategoriedaten in Obj $category setzen, Antwort der Funktion in $obj setzen    
    $obj = $category->_set($data);

    $nested_set = new nested_set();
    $nested_set->setTable(TABLE_CATEGORIES);
    $nested_set->setTableDescription(TABLE_CATEGORIES_DESCRIPTION);
   
    // NESTED SET NEU BERECHNEN LASSEN
    if( $buildNestedSet === true ){  
      cat_buildNestedSet( $nested_set );
    }

    list($left, $right) = $nested_set->getCategoryLeftRight($data['parent_id']);
    $data['categories_left'] = $left;
    $data['categories_right'] = $right;
        

    // Neues custom_link Obejkt anlegen
    // // ACHTUNG! NUR WENN
    if(  $data['category_custom_link_type'] != null && $data['category_custom_link_type'] != "New" && $data['category_custom_link_type'] != "" ){    

        $custom_link = new custom_link();
    
        // Neu: $store_field_exists setzen
        $custom_link->store_field_exists = true;
     
        $setResp = $custom_link->_set($data);
    }
     
    //Hookpoint Bottom
    ($plugin_code = $xtPlugin->PluginCode('setCategory.php:setCategory_bottom')) ? eval($plugin_code) : false;

    // Hat das setzen der Kategorie funktioniert?
    if ($obj->success) {
        // Erfolgsmeldung setzen
        $result['result'] = true;
        $result['categories_id'] = $categoryID;
        $result['external_id'] = $categoryItem['external_id'];
        $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt external_id:" . $categoryItem['external_id'] ;
        // Rückgabe mit Erfolgsmeldung
        return $result;
    } else {
        // Setzen der Kategorie hat NICHT funktioniert, also Fehlermeldung schreiben
        $Exception.="Failed on external_id " . $categoryItem['external_id'];
    }

    // Ist eine Fehlermeldung gesetzt worden in $Exception
    if (strlen($Exception) > 0) {
        // Fehlermeldung in $result setzen
        $result['result'] = false;
        $result['external_id'] = $categoryItem['external_id'];
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception;
        // Raus mit Fehlermeldung
        return $result;
    }

    // Setzen der Kategorie hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    $result['categories_id'] = $categoryID;
    $result['external_id'] = $categoryItem['external_id'];
    $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt external_id:" . $categoryItem['external_id'] . " " . $output . $output2;

    // Rückgabe
    return $result;
}

/**
 * 
 * @param type $nested_set
 */
function cat_buildNestedSet( $nested_set ) {
    $nested_set->buildNestedSet();
}
?>