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
 * @param type $start
 * @param type $size
 * @param type $extNumberRange
 * @return type
 */
function getCategories($user, $pass, $start = 0, $size = 1000, $extNumberRange = 0) {
    global $db,$store_handler;

    // Darf der user überhaupt zugreifen?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $result['message'] = "";
    
    // Array für Kategorienrückgabe
    $list = array();
    
    try {

        // Hilfsarray für Kategorien
        $data = array();
        
        // categories_id+extNumberRange in external_id setzen
        $db->Execute("UPDATE " . TABLE_CATEGORIES . " SET external_id=categories_id + " . $extNumberRange . " WHERE (external_id IS NULL OR external_id='TEXT_EXTERNAL_ID' OR external_id='')");
        
        // Startwert und Blocksize auf int casten
        $start = (int) $start;
        $size = (int) $size;

        // Kategorien aus DB abfragen mit Limits von start und size
        $rs = $db->Execute("SELECT * FROM " . TABLE_CATEGORIES . " LIMIT " . (int) $start . "," . (int) $size);
        
        // über alle DB Treffer laufen
        while (!$rs->EOF) {
            // array für eine Kategorie bauen
            $cat = array();
            // Alle Felder einer Kategorie ins array schreiben
            $cat = $rs->fields;

            
            // Ist eine parent_id gesetzt?
            if ($cat['parent_id'] > 0) {
                // Extrene ID der Parent Kategorie aus DB abfragen und als external_parent_id setzen
                $es = $db->Execute("SELECT external_id FROM " . TABLE_CATEGORIES . " WHERE categories_id='" . $cat['parent_id'] . "'");
                $cat['external_parent_id'] = $es->fields['external_id'];
            } else {
                // Keine parent id in Kategorie gesetzt, also external_parent_id auf "0" setzen
                $cat['external_parent_id'] = 0;
            }
            
            // Datumsangaben prüfen und evtl. anpassen
            $cat['date_added'] = SoapHelper::checkIsoDate($cat['date_added']);
            $cat['last_modified'] = SoapHelper::checkIsoDate($cat['last_modified']);
           
            // Permission ID setzen, falls leer
            if ($cat['permission_id'] == NULL || $cat['permission_id'] == ''  ) {
                $cat['permission_id'] = 0;
            }
            
            // Infos über alle stores holen
            $stores = $store_handler->getStores();

            $firstStoreId = $stores[0]['id'];

             //
             // TABLE_PRODUCTS_DESCRIPTION Daten mappen MULTILANG
             // 
           
             // Hilfsarray für die storedaten key = storeId
             $categoryDescriptions = array();

            // Kategoriebeschreibungen für alle XT Sprachen laden aus Tabellen TABLE_CATEGORIES_DESCRIPTION und TABLE_SEO_URL laden
            // seo.link_type=2 == Kategorie
            // Neue Struktur, über alle Sprachen und Stores laufen
            // $ls = $db->Execute("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " AS cd, " . TABLE_SEO_URL . " AS seo WHERE cd.categories_id='" . $rs->fields['categories_id'] . "' and cd.categories_id=seo.link_id and seo.link_type=2 and seo.language_code=cd.language_code");
            $ls = $db->Execute("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " AS cd, " . TABLE_SEO_URL . " AS seo WHERE cd.categories_id='" . $rs->fields['categories_id'] . "' and cd.categories_id=seo.link_id and seo.link_type=2 and seo.language_code=cd.language_code and seo.store_id=cd.categories_store_id");
            
            // Über DB Treffer laufen
            while (!$ls->EOF) {
                // Sprachcode der Beschreibung holen
                $lng = $ls->fields['language_code'];
                $storeId = $ls->fields['categories_store_id'];
                
                $categoryDescriptions[$storeId]["categories_name"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_name']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_heading_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_heading_title']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_description_bottom"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description_bottom']), "xsd:string", "" );

                // SEO
                $categoryDescriptions[$storeId]["meta_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_description']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["meta_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_title']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["meta_keywords"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_keywords']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["url_text"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['url_text']), "xsd:string", "" );
              
                // Die alte Struktur nur mit den Daten des ersten stores füllen
                if( $firstStoreId === $storeId){
                    $cat["categories_name"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_name']), "xsd:string", "" );
                    $cat["categories_heading_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_heading_title']), "xsd:string", "" );
                    $cat["categories_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description']), "xsd:string", "" );
                    $cat["categories_description_bottom"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description_bottom']), "xsd:string", "" );
                    
                    // SEO
                    $cat["meta_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_description']), "xsd:string", "" );
                    $cat["meta_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_title']), "xsd:string", "" );
                    $cat["meta_keywords"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['meta_keywords']), "xsd:string", "" );
                    $cat["url_text"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['url_text']), "xsd:string", "" );
                
                    // link_url gab es damals noch nicht
                 
                }
                
                
                //$cat_lang[$ls->fields['language_code']]=$ls->fields['categories_name'];
                // zum nächsten TReffer in Kategoriebeschreibungen
                $ls->MoveNext();
                
            }
  
            // Kategoriebeschreibung Abfrage schliessen
            $ls->Close();

            // 2015-03-06: Neue SQL Abfrage für categories mit LINKS = KEINE SEO!
            $ls = $db->Execute("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id='" . $rs->fields['categories_id'] . "'");
            
            // Über DB Treffer laufen
            while (!$ls->EOF) {
                // Sprachcode der Beschreibung holen
                $lng = $ls->fields['language_code'];
                $storeId = $ls->fields['categories_store_id'];
                
                $categoryDescriptions[$storeId]["categories_name"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_name']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_heading_title"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_heading_title']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_description"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description']), "xsd:string", "" );
                $categoryDescriptions[$storeId]["categories_description_bottom"][$lng] = getValue( SoapHelper::fixUnvalidChars($ls->fields['categories_description_bottom']), "xsd:string", "" );
               
                // Hier nach link_urls suchen per SQL
                $link_url =  $db->GetOne("SELECT link_url FROM " . TABLE_CATEGORIES_CUSTOM_LINK_URL . "  WHERE categories_id='" . $rs->fields['categories_id'] . "' and language_code='". $lng ."' and store_id=".$storeId);
                
                $categoryDescriptions[$storeId]["link_url"][$lng] = $link_url;

                //$cat_lang[$ls->fields['language_code']]=$ls->fields['categories_name'];
                // zum nächsten TReffer in Kategoriebeschreibungen
                $ls->MoveNext();
            }
  
            // Kategoriebeschreibung Abfrage schliessen
            $ls->Close();

            // Hilfsarraydaten mit Multisprachen für Descriptions und SEO in category Struktur kopieren
            $categoryDescriptionsList = array();

            foreach( $categoryDescriptions as $storeId => $categoryDescriptions ){

                $categoryDescriptions["categories_store_id"] = $storeId;
                array_push( $categoryDescriptionsList, $categoryDescriptions );
            }
            $cat["categoryDescriptionsList"] = $categoryDescriptionsList;            

            
            // Kategorie Permissions holen            
            // Array für Permissions bauen
            $permissionItemList = array();
            
            // Alle permissions abfragen für diese Kategorie
            $ps = $db->Execute("SELECT * FROM " . TABLE_CATEGORIES_PERMISSION . " WHERE pid='" . $cat['categories_id'] . "'");
            
            // Über DB Treffer für permissions laufen
            while (!$ps->EOF) {
                
                // Array für einzlene Permisson bauen
                $permissionItem = array();
                
                // Daten mappen
                $permissionItem["external_id"] = $cat["external_id"];
                $permissionItem["permission"] = $ps->fields["permission"];
                $permissionItem["pgroup"] = $ps->fields["pgroup"];
                
                // in List array
                $permissionItemList[] = $permissionItem;
                
                // zum nächsten TReffer in Permission
                $ps->MoveNext();   
            }            
            
            // in cat mappen
            $cat["permissionList"] = $permissionItemList;
   
            // Abfrage über Tabelle TABLE_CATEGORIES_PERMISSION  schliessen
            $ps->Close();

            // Kategoriedaten aus $cat zu $data hinzufügen
            $data[] = $cat;
            
            //$cat['categories_name']=$cat_lang;
            // zum nächsten Treffer in Kategorietabelle TABLE_CATEGORIES
            $rs->MoveNext();
            
        
            
        }
        // Abfrage über Tabelle TABLE_CATEGORIES schliessen
        $rs->Close();

        // $list bekommt Inhalte von $data = Alle Kategoriedaten
        $list = $data;
               
        
        // wenn XT Soap Cahrset = ISO-8859-1, dann alle Einträge umwandlen zu UTF-8
        if (XT_API_CHARSET == 'ISO-8859-1') {
            $list = xtSoapCharsetConvert($list);
        }

        // Alles ist gut gelaufen, also success message setzen
        $result['message'] = SoapHelper::MessageSuccess;
    
        
    } catch (Exception $ex) {
        // Es ist eine exception aufgetreten, also Fehlermeldung zurückgeben
        $result['message'].= SoapHelper::MessageFail . " Exception Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    // Array mit allen Kategoriedaten in $result packen
    $result['result'] = $list;

    // Ergebnis zurückgeben
    return $result;
}
?>