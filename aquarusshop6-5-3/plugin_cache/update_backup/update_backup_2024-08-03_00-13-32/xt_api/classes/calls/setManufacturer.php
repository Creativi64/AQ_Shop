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

// Include einer externen php Datei
include_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';

/**
 * 
 * @global type $db
 * @global type $language
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $ManufacturerItem
 * @return string
 */
function setManufacturer($user = '', $pass = '', $ManufacturerItem = '') {
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

    // Ist $ManufacturerItem KEIN array
    if (!is_array($ManufacturerItem)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " ManufacturerItem no array";
        return $result;
    }

    // globale Variablen $db, $language und $xtPlugin nutzen
    global $db, $language, $xtPlugin;

    // wenn Einträge external_id in $ManufacturerItem nicht gesetzt, dann
    if (!isset($ManufacturerItem['external_id']) ) {
        // mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception . $productID;
        return $result;
    }

    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setManufacturer.php:setManufacturer_top')) ? eval($plugin_code) : false;

    // Gibt es Hersteller schon in XT? Anhand von external_id suchen
    $manuID = SoapHelper::getManufacturersIDByexternalID($ManufacturerItem['external_id']);

    
    // Array $data für Herstellerdaten bauen
    $data = array();

    // Gab es Hersteller schon in XT? Wenn $HerstellerID = false, also leer
    if ($manuID === false) {
        // Hersteller in DB einfügen
        $type = 'insert';
        // anlegen in DB mit external_id
        $db->Execute("INSERT INTO " . TABLE_MANUFACTURERS . " (external_id) VALUES ('" . $ManufacturerItem['external_id'] . "')");
        // manufacturers_id setzen anhand von neuer ID aus DB
        $data['manufacturers_id'] = $db->Insert_ID();
    } else {
        // Es gibt Hersteller schon in DB
        $type = 'update';
        $data['manufacturers_id'] = $manuID;
    }

    // Standard Herstellerdaten mappen in $data
    $data['manufacturers_name'] = $ManufacturerItem['manufacturers_name'];
    $data['manufacturers_image'] = $ManufacturerItem['manufacturers_image'];
    $data['external_id'] = $ManufacturerItem['external_id'];
    $data['manufacturers_sort'] = $ManufacturerItem['manufacturers_sort'];
    $data['products_sorting'] = $ManufacturerItem['products_sorting'];
    $data['products_sorting2'] = $ManufacturerItem['products_sorting2'];
    $data['manufacturers_status'] = $ManufacturerItem['manufacturers_status'];
    
    // Neues array 'manufacturerDescriptionsList' für Beschreibungen wird genutzt   
    if( $ManufacturerItem['manufacturerDescriptionsList'] && sizeof($ManufacturerItem['manufacturerDescriptionsList']) > 0 ){
        // über alle einzelnen descritionItems laufen
        foreach( $ManufacturerItem['manufacturerDescriptionsList'] as $manufacturerDescriptionsItem ){
            reorgLangFieldStores($manufacturerDescriptionsItem, "manufacturers_description", $data, "manufacturers_description",$manufacturerDescriptionsItem['manufacturers_store_id']);
            reorgLangFieldStores($manufacturerDescriptionsItem, "meta_description", $data, "meta_description",$manufacturerDescriptionsItem['manufacturers_store_id']);
            reorgLangFieldStores($manufacturerDescriptionsItem, "meta_title", $data, "meta_title",$manufacturerDescriptionsItem['manufacturers_store_id']);
            reorgLangFieldStores($manufacturerDescriptionsItem, "meta_keywords", $data, "meta_keywords",$manufacturerDescriptionsItem['manufacturers_store_id']);
            reorgLangFieldStores($manufacturerDescriptionsItem, "manufacturers_url", $data, "manufacturers_url",$manufacturerDescriptionsItem['manufacturers_store_id']);
            reorgLangFieldStores($manufacturerDescriptionsItem, "url_text", $data, "url_text",$manufacturerDescriptionsItem['manufacturers_store_id']);
        }  
     }
     else{
         // Fallback auf alte WSDL mit Beschreibungen in allen Sprachen aber nicht pro shop
         // Dann für alle Shops die gleichen Texte setzen
         
         // über alle stores laufen
         $stores = $store_handler->getStores();
         foreach ($stores as $store) {

            //
            // Sprachfelder Struktur umbauen pro store
            //
            reorgLangFieldStores($ManufacturerItem, "manufacturers_description", $data, "manufacturers_description",$store['id']);
            reorgLangFieldStores($ManufacturerItem, "meta_description", $data, "meta_description",$store['id']);
            reorgLangFieldStores($ManufacturerItem, "meta_title", $data, "meta_title",$store['id']);
            reorgLangFieldStores($ManufacturerItem, "meta_keywords", $data, "meta_keywords",$store['id']);
            reorgLangFieldStores($ManufacturerItem, "manufacturers_url", $data, "manufacturers_url",$store['id']);
            reorgLangFieldStores($ManufacturerItem, "url_text", $data, "url_text",$store['id']);
         }
     }

    // Permissions in $data mappen, damit sie per Object $category in db gemappt werden
    // Aufbau zb $data["shop_1"] = "1"
    if( is_array( $ManufacturerItem["permissionList"] ) ){
        // Über permission Items laufen
        foreach( $ManufacturerItem["permissionList"] as $key => $pItem ){
            // in $data mappen
            $data[ $pItem["pgroup"] ] = "1";
        }
    }

    // Soll "alte" SEO URL gerettet werden?
    if( XT_API_REBUILD_SEO_URLS === 'false' && $type === 'update') {
        $record = $db->Execute("SELECT link_id,language_code,url_text,store_id FROM " . TABLE_SEO_URL . " WHERE link_type=4 AND link_id=".$manuID);
        while(!$record->EOF){
            $data['url_text_store' . $record->fields["store_id"] . "_" . $record->fields["language_code"]] = $record->fields["url_text"];
            $record->MoveNext();
        }
        $record->Close();
    }
    
    // Neues Objekt stdClass anlegen
    $obj = new stdClass;
    // Neues manufacturer Objekt anlegen
    $manu = new manufacturer;

    $manu->position = 'admin';
    $manu->store_field_exists = true;
    
    // Herstellerdaten in Obj $manu setzen, Antwort der Funktion in $obj setzen
    $obj = $manu->_set($data);

    // Hier auch noch image setzen!
    $manu->_setImage($data['manufacturers_id'], $ManufacturerItem['manufacturers_image']);
    
    // Status setzen - ERST AM SCHLUSS WEIL _setImage den status plattmacht!
    $setStatusResp = setStatus( $data['manufacturers_id'], $data['manufacturers_status']);

    
    //Hookpoint Bottom
    ($plugin_code = $xtPlugin->PluginCode('setManufacturer.php:setManufacturer_bottom')) ? eval($plugin_code) : false;

        
    // Hat das setzen des Herstellers funktioniert?
    if ($obj->success) {
        // Erfolgsmeldung setzen
        $result['result'] = true;
        $result['manufacturers_id'] = $data['manufacturers_id'];
        $result['external_id'] = $ManufacturerItem['external_id'];
        $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt external_id:" . $ManufacturerItem['external_id'];
        // Rückgabe mit Erfolgsmeldung
        return $result;
    } else {
        // Setzen des Herstellers hat NICHT funktioniert, also Fehlermeldung schreiben
        $Exception.="Failed on external_id " . $ManufacturerItem['external_id'];
    }

    // Ist eine Fehlermeldung gesetzt worden in $Exception
    if (strlen($Exception) > 0) {
        // Fehlermeldung in $result setzen
        $result['result'] = false;
        $result['manufacturers_id'] = $data['manufacturers_id'];
        $result['external_id'] = $ManufacturerItem['external_id'];
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception;
        // Raus mit Fehlermeldung
        return $result;
    }

    // Setzen des Herstellers hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    $result['manufacturers_id'] = $data['manufacturers_id'];
    $result['external_id'] = $ManufacturerItem['external_id'];
    $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt external_id:" . $ManufacturerItem['external_id'] . " " . $output . $output2;

    // Rückgabe
    return $result;
}

/**
 * 
 * @global type $db
 * @global type $xtPlugin
 * @param type $id
 * @param type $status
 * @return boolean
 */
function setStatus($id, $status) {
        global $db,$xtPlugin;

        $id = (int)$id;
        if (!is_int($id)) return false;

        $db->Execute("UPDATE " . TABLE_MANUFACTURERS. " SET manufacturers_status = ".$status." WHERE manufacturers_id = '" . $id . "'");

        // activate/deactivate relating products
        if( false ){
            $db->Execute("UPDATE ".TABLE_PRODUCTS." SET products_status = '".$status."' WHERE manufacturers_id = '" . $id . "'");
        }
}
?>