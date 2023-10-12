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
 * @global type $store_handler
 * @global type $db
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $extNumberRange
 * @param type $start
 * @param type $size
 * @return boolean
 */
function getAttributes($user, $pass, $extNumberRange = 0, $start = 0, $size = 100)  {
    global $store_handler, $db, $xtPlugin;

    // Darf der user überhaupt zugreifen?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $result['message'] = "";
    
    // Array für Attributlisterückgabe
    $list = array();
    

    // 2016-04-14 EK: Prüfung ob xt_master_slave PlugIn aktiv
    if (isset($xtPlugin->active_modules['xt_master_slave'])) {
    
        try {

        // Hilfsarray für Attribute
        $data = array();

        ////// Update der Tabelle TABLE_PRODUCTS_ATTRIBUTES: Alle Felder "bw_id" mit Wert NULL auf den Wert von "attributes_id" setzen

        //$db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " SET bw_id=attributes_id + " . $extNumberRange . " WHERE bw_id IS NULL");

        
       
        // bw_id ist bei erster Initialisierung = 0 
        $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " SET bw_id=attributes_id + " . $extNumberRange . " WHERE (bw_id=0 OR bw_id='' OR bw_id IS NULL)");

        // Startwert und Blocksize auf int casten
        $start = (int) $start;
        $size = (int) $size;


        // Attribute aus DB abfragen aus Hauptabelle Attribute
        $rs = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " LIMIT " . (int) $start . "," . (int) $size);

        // über alle DB Treffer laufen
        while (!$rs->EOF) {
            // array für ein Attribut bauen
            $att = array();
            // Alle Felder eines Attributes ins array schreiben
            $att = $rs->fields;

            //attributes_ext_id einmappen steht in feld "bw_id"
            $att["attributes_ext_id"] = $att["bw_id"];

            // attributes_ext_parent_id befüllen, nur wenn attributes_parent > 0
            if($att["attributes_parent"]>0){
                $att["attributes_ext_parent_id"] = SoapHelper::getAttributesExternalIDByID($att["attributes_parent"]);
            }
            // attributes_parent = 0, dann auch attributes_ext_parent_id auf 0 setzen
            if($att["attributes_parent"] == 0){
                $att["attributes_ext_parent_id"] = 0;
            }

            // Attributbeschreibungen für alle XT Sprachen laden aus Tabellen TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION laden
            $ls = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION . " cd WHERE cd.attributes_id='" . $rs->fields['attributes_id'] . "'");

            // Über DB Treffer laufen
            while (!$ls->EOF) {
                // Sprachcode der Beschreibung holen
                $lng = $ls->fields['language_code'];
                // Über alle Felder laufen
                foreach ($ls->fields as $key => $val) {
                    // wenn Feldname NICHT attributes_id und NICHT language_code
                    if ($key != 'attributes_id' && $key != 'language_code'){
                        // Ungültige Steuerzeichen filtern
                        $val = SoapHelper::fixUnvalidChars( $val );

                        // Eintrag in $att array eintragen mit [Spaltenname][SPRACHCODE]
                        $att[$key][$lng] = $val;
                    }
                }
                // zum nächsten TReffer in Attributbeschreibung
                $ls->MoveNext();
            }
            // Attributbeschreibung Abfrage schliessen
            $ls->Close();
            
            // zum nächsten Treffer in Attributtabelle TABLE_ATTRIBUTES
            $rs->MoveNext();

            // Attribut aus $att zu $data hinzufügen
            $data[] = $att;
        }
        // Abfrage über Tabelle TABLE_ATTRIBUTES schliessen
        $rs->Close();

        // $list bekommt Inhalte von $data = Alle Attributdaten
        $list = $data;

        // wenn XT Soap Cahrset = ISO-8859-1, dann alle Einträge umwandlen zu UTF-8
        if (XT_API_CHARSET == 'ISO-8859-1') {
            $list = xtSoapCharsetConvert($list);
        }

        // Alles ist gut gelaufen, also success message setzen
        $result['message'] = SoapHelper::MessageSuccess;


        } catch (Exception $ex) {
            // Es ist eine exception aufgetreten, also Fehlermeldung zurückgeben
            $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        
    }else{
        //xt_master_slave PlugIn ist nicht aktiviert --> raus mit Fehlermeldung
        $result['message'].= SoapHelper::MessageFail . " Execption Message: PlugIn xt_master_slave is not installed or active";
        $result['result'] = false;
        return $result;
    }

    // Array mit allen Attributdaten in $result packen
    $result['result'] = $list;
    
    // Ergebnis zurückgeben
    return $result;
}
?>