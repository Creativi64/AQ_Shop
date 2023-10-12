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
 * Bestimmte Hersteller aus Tabelle TABLE_MANUFACTURERS zurückliefern
 * @global type $store_handler
 * @global type $db
 * @param type $user
 * @param type $pass
 * @param type $start
 * @param type $size
 * @param type $extNumberRange
 * @return type
 */
function getManufacturers($user, $pass, $start = 0, $size = 10, $extNumberRange = 0) {
    global $db,$store_handler;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $result['message'] = "";
    
    // array für Hersteller
    $list = array();
    
    try {

        
        // 2013-02-21: Änderung: Die externe Ids eines Herstellers setzen per Nummernkreis $extNumberRange, da wo leer
        //$db->Execute("UPDATE " . TABLE_MANUFACTURERS . " SET external_id=manufacturers_id + " . $extNumberRange . " WHERE external_id IS NULL");
        // 2013-09-24: Änderung: Zusätzlich wird die external_id auch gesetzt wenn in external_id schon der Wert "TEXT_EXTERNAL_ID" steht.
        $db->Execute("UPDATE " . TABLE_MANUFACTURERS . " SET external_id=manufacturers_id + " . $extNumberRange . " WHERE (external_id IS NULL OR external_id='TEXT_EXTERNAL_ID' OR external_id='')");
        
        
        // Hilfsarray für Daten
        $data = array();
        
        // DB Abfrage: Alles aus TABLE_MANUFACTURERS abfragen
        // 2013-01-31: Garcia: Neu start offset und blocksize rein
        $rs = $db->Execute("SELECT * FROM " . TABLE_MANUFACTURERS. " LIMIT " . (int) $start . "," . (int) $size);
        
        // Über alle Treffer laufen
        while (!$rs->EOF) {
            // array für einen Hersteller bauen
            $manu = array();
            // Alle Felder eines Herstellers ins array schreiben
            $manu = $rs->fields;  
            
            // Datumsangaben prüfen und evtl. anpassen
            $manu['date_added'] = SoapHelper::checkIsoDate($manu['date_added']);
            $manu['last_modified'] = SoapHelper::checkIsoDate($manu['last_modified']);

           // Infos über alle stores holen
            $stores = $store_handler->getStores();

            $firstStoreId = $stores[0]['id'];

             // TABLE_PRODUCTS_DESCRIPTION Daten mappen MULTILANG
             // Hilfsarray für die storedaten key = storeId
             $manuDescriptions = array();   
            
            // Herstellerbeschreibungen für alle XT Sprachen laden aus Tabellen TABLE_MANUFACTURERS_INFO und TABLE_SEO_URL laden
            $ls = $db->Execute("SELECT * FROM " . TABLE_MANUFACTURERS_DESCRIPTION . " cd, " . TABLE_SEO_URL . " seo WHERE cd.manufacturers_id='" . $rs->fields['manufacturers_id'] . "' and cd.manufacturers_id=seo.link_id and seo.link_type=4 and seo.language_code=cd.language_code and seo.store_id=cd.manufacturers_store_id");
            
            // Über DB Treffer laufen
            while (!$ls->EOF) {
                // Sprachcode der Beschreibung holen
                $lng = $ls->fields['language_code'];
                $storeId = $ls->fields['manufacturers_store_id'];
                       
                $manuDescriptions[$storeId]["manufacturers_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['manufacturers_description']), "xsd:string", "" );
                $manuDescriptions[$storeId]["meta_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_description']), "xsd:string", "" );
                $manuDescriptions[$storeId]["meta_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_title']), "xsd:string", "" );
                $manuDescriptions[$storeId]["meta_keywords"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_keywords']), "xsd:string", "" );
                $manuDescriptions[$storeId]["manufacturers_url"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['manufacturers_url']), "xsd:string", "" );
                $manuDescriptions[$storeId]["url_text"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['url_text']), "xsd:string", "" );
           
                // Die alte Struktur nur mit den Daten des ersten stores füllen
                if( $firstStoreId === $storeId){
                    $manu["manufacturers_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['manufacturers_description']), "xsd:string", "" );
                    $manu["meta_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_description']), "xsd:string", "" );
                    $manu["meta_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_title']), "xsd:string", "" );
                    $manu["meta_keywords"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_keywords']), "xsd:string", "" );
                    $manu["manufacturers_url"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['manufacturers_url']), "xsd:string", "" );
                    $manu["url_text"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['url_text']), "xsd:string", "" );         
                }
                // zum nächsten TReffer in Herstellerbeschreibung
                $ls->MoveNext();
            }
 
            // Herstellerbeschreibung Abfrage schliessen
            $ls->Close();
            
            // Hilfsarraydaten mit Multisprachen für Descriptions und SEO in manufacturer Struktur kopieren
            $manufacturerDescriptionsList = array();

            foreach( $manuDescriptions as $storeId => $manufacturerDescriptions ){
                $manufacturerDescriptions["manufacturers_store_id"] = $storeId;
                array_push( $manufacturerDescriptionsList, $manufacturerDescriptions );
            }
            $manu["manufacturerDescriptionsList"] = $manufacturerDescriptionsList;            

            // Herstellerbeschreibung Abfrage schliessen
            $ls->Close();            

            // Array für Permissions bauen
            $permissionItemList = array();
            
            // Alle permissions abfragen für diesen Hersteller
            $ps = $db->Execute("SELECT * FROM " . TABLE_MANUFACTURERS_PERMISSION . " WHERE pid='" . $manu['manufacturers_id'] . "'");
            
            // Über DB Treffer für permissions laufen
            while (!$ps->EOF) {
                
                // Array für einzlene Permisson bauen
                $permissionItem = array();
                
                // Daten mappen
                $permissionItem["external_id"] = $manu["external_id"];
                $permissionItem["permission"] = $ps->fields["permission"];
                $permissionItem["pgroup"] = $ps->fields["pgroup"];
                
                // in List array
                $permissionItemList[] = $permissionItem;
                
                // zum nächsten TReffer in Permission
                $ps->MoveNext();
            }            
            
            // in manu mappen
            $manu["permissionList"] = $permissionItemList;

            // Alle Felder in neuen Eintrag von $data mappen
            $data[] = $manu;
            
            // Zum nächsten Treffer springen
            $rs->MoveNext();
        }
        // Hersteller Abfrage schliessen
        $rs->Close();

        // $data in $list
        $list = $data;

        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    } catch (Exception $ex) {
        // Eine exception ist aufgetreten, also Fehlermeldung setzen
        $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    
    // Hersteller in $result setzen
    $result['result'] = $list;
    
    // Rückgabe 
    return $result;
}
?>