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
 * 
 * @global type $db
 * @param type $user
 * @param type $pass
 * @param type $offset
 * @param type $blocksize
 * @return string
 */
function getMediaXTMetaList($user, $pass, $offset = 0 , $blocksize = 10) {
    
    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
   
    global $db;
    
    // Offset Wert für Abfrage zu int casten
    $offset = (int) $offset;
    $blocksize = (int) $blocksize;
    
    // alle media Einträge mit type='files' holen
    $sql = "select * from " . TABLE_MEDIA . " WHERE type='files' LIMIT " . $offset . "," . $blocksize;
    
    // SQL Haupttabelle - Alle medien einträge
    $gaRS = $db->Execute($sql );
    
    // Keine Treffer gefunden
    if ($gaRS->RecordCount() == 0) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " no media found.";
        return $result;
    }
    
    $exceptions = "";
    
    // Liste für alle MedianDaten Metadaten
    $mediaItemXTMetaList = array();
    
    // über Treffer laufen
    while (!$gaRS->EOF) {
        // einzelnen metadatensatz bauen
        $mediaItemXTMeta = array();
        
        $pTableMedia = $gaRS->fields;
        
        // mappen
        $mediaItemXTMeta["id"] = getValue( $pTableMedia["id"], "xsd:int", 0 );
        $mediaItemXTMeta["external_id"] = getValue( $pTableMedia["external_id"], "xsd:string", "" );
        $mediaItemXTMeta["file"] = getValue( $pTableMedia["file"], "xsd:string", "" );
        $mediaItemXTMeta["type"] = getValue( $pTableMedia["type"], "xsd:string", "" );
        $mediaItemXTMeta["class"] = getValue( $pTableMedia["class"], "xsd:string", "" );
        $mediaItemXTMeta["download_status"] = getValue( $pTableMedia["download_status"], "xsd:string", "" );
        $mediaItemXTMeta["status"] = getValue( $pTableMedia["status"], "xsd:string", "" );
        $mediaItemXTMeta["max_dl_count"] = getValue( $pTableMedia["max_dl_count"], "xsd:int", 0 );
        $mediaItemXTMeta["max_dl_days"] = getValue( $pTableMedia["max_dl_days"], "xsd:int", 0 );
        
        // in Liste
        $mediaItemXTMetaList[] =  $mediaItemXTMeta;
        
         // zum nächsten Treffer 
        $gaRS->MoveNext();
    }
    // Media Abfrage schließen
    $gaRS->Close();
    
    $result['result'] = true;
    $result['mediaItemXTMetaList'] = $mediaItemXTMetaList;
    
    return $result;
}