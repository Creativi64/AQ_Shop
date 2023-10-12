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
 * Ein Media Item abholen
 * @global type $db
 * @param type $user
 * @param type $pass
 * @param type $id
 * @param type $external_id
 * @return string
 */
function getMediaXT($user = '', $pass = '', $id = -1, $external_id = '') {
    
    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Media ID ermitteln über external_id, falls gesetzt
    if( strlen( $external_id ) > 0 ){
        $id = SoapHelper::getMediaIDByexternalID($external_id);
    }
    
    // media nicht gefunden anhand von IDs
    if( $id === false ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " unable to find media by external_id: " . $external_id;
        return $result;
    }
    
    global $db;
    
    // SQL Haupttabelle
    $rs = $db->Execute("select * from " . TABLE_MEDIA . " where id =" . $id );
    
    if ($rs->RecordCount() == 0) {
        // media NICHT in DB gefunden
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " unable to find media in db: id: " . $id;
        return $result;
    }
  
    $pTableMedia = $rs->fields;
   
    // mediaItem für Rückgabe
    $mediaItemXT = array();
    
    //
    // TABLE_MEDIA Daten mappen
    //
    $mediaItemXT["id"] = getValue( $pTableMedia["id"], "xsd:int", 0 );
    $mediaItemXT["external_id"] = getValue( $pTableMedia["external_id"], "xsd:string", "" );
    $mediaItemXT["file"] = getValue( $pTableMedia["file"], "xsd:string", "" );
    $mediaItemXT["type"] = getValue( $pTableMedia["type"], "xsd:string", "" );
    $mediaItemXT["class"] = getValue( $pTableMedia["class"], "xsd:string", "" );
    $mediaItemXT["download_status"] = getValue( $pTableMedia["download_status"], "xsd:string", "" );
    $mediaItemXT["status"] = getValue( $pTableMedia["status"], "xsd:string", "" );
    $mediaItemXT["max_dl_count"] = getValue( $pTableMedia["max_dl_count"], "xsd:int", 0 );
    $mediaItemXT["max_dl_days"] = getValue( $pTableMedia["max_dl_days"], "xsd:int", 0 );

    //
    // TABLE_MEDIA_DESCRIPTION Daten mappen MULTILANG
    //    
    $descRS = $db->Execute("SELECT * FROM " . TABLE_MEDIA_DESCRIPTION . " WHERE id=" . $id);
            
    // Über DB Treffer laufen TABLE_MEDIA_DESCRIPTION
    while (!$descRS->EOF) {
        // Sprachcode der Beschreibung holen
        $lng = $descRS->fields['language_code'];

        $mediaItemXT["media_name"][$lng] = getValue( $descRS->fields['media_name'], "xsd:string", "" );
        $mediaItemXT["media_description"][$lng] = getValue( $descRS->fields['media_description'], "xsd:string", "" );
        
        // zum nächsten Treffer 
        $descRS->MoveNext();
    }
    // Mediabeschreibung Abfrage schliessen
    $descRS->Close();
    
    // Datei selbst Base64 rein
    $imageFolder = _SRV_WEBROOT . '/media/files/';

    // Mediendaten laden
    $fileBase64 = file_get_contents($imageFolder . $pTableMedia["file"]);

    if( ! $fileBase64){
        $result['result'] = false;
        $result['message'].= SoapHelper::MessageFail . " Exception Message: Mediafile not found: ". $imageFolder . $pTableMedia["file"];
        return $result;
    }
    else{
        // base64 Konvertierung von Bilddaten und in Antwort
        $mediaItemXT["filedata"]   = base64_encode( $fileBase64 );
    }

    //
    // MEDIA LANGUAGES
    // > $mediaItemXT["media_languages"]
    $langRS = $db->Execute("SELECT language_code FROM " . TABLE_MEDIA_LANGUAGES . " WHERE m_id=" . $mediaItemXT["id"] );
    
    $media_languages = array();
    
    while (!$langRS->EOF) {
        $media_languages[] = $langRS->fields['language_code'];
        
        // zum nächsten Treffer 
        $langRS->MoveNext();
    }             
    // Mediasprachen Abfrage schliessen
    $langRS->Close();
        
    // einmappen
    $mediaItemXT["media_languages"] = $media_languages;
    
    //
    // Permission Daten mappen 
    //       
    $permissionItemList = array();
    
    $ppRS = $db->Execute("SELECT * FROM " . TABLE_CONTENT_PERMISSION . " WHERE pid=" . $id );
        
    // Über DB Treffer laufen TABLE_CONTENT_PERMISSION
    while (!$ppRS->EOF) {
        $permissionItem = array();
        
        $permissionItem["external_id"] = $mediaItemXT["external_id"];
        $permissionItem["permission"] = getValue( $ppRS->fields['permission'], "xsd:int", 0 );
        $permissionItem["pgroup"] = getValue( $ppRS->fields['pgroup'], "xsd:string", "" );
      
        $permissionItemList[] = $permissionItem;
        
        // zum nächsten Treffer 
        $ppRS->MoveNext();
    }
    // Mediapermissions Abfrage schliessen
    $ppRS->Close();

    $mediaItemXT["permissionList"] = $permissionItemList;

    // Rückgabe    
    $result['result'] = true;
    $result['mediaItemXT'] = $mediaItemXT;
    return $result;   
}
?>
