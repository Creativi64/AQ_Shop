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
 * @global type $store_handler
 * @global type $db
 * @param type $user
 * @param type $pass
 * @return type
 */
function getShippingMethods($user, $pass) {
    global $store_handler, $db;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    $result['message'] = "";
    
    // Array für Liste von shipping methods
    $list = array();
    
    try {

        // Hilfsarray 
        $data = array();
        
        // DB Abfrage: Alles aus Tabelle TABLE_SHIPPING
        $rs = $db->Execute("SELECT * FROM " . TABLE_SHIPPING);
        
        // Über alle Treffer laufen
        while (!$rs->EOF) {

            // Alle Felder in $arr
            $arr = $rs->fields;
            
            // Alle Beschreibungen (mehrsprachig=´) zu einer shipping method anhand von shipping_id aus Tabelle TABLE_SHIPPING_DESCRIPTION holen
            $ls = $db->Execute("SELECT * FROM " . TABLE_SHIPPING_DESCRIPTION . " WHERE shipping_id='" . $rs->fields['shipping_id'] . "'");

            // Über Beschreibungen laufen
            while (!$ls->EOF) {
                // Beschreibung/Name der Liefermethode mit Sprachcode in $arr
                $arr['shipping_name'][$ls->fields['language_code']] = $ls->fields['shipping_name'];
                // zur nächsten Beschreibung springen
                $ls->MoveNext();
            }
            // Garcia: Hinzugefügt
            // Abfrage auf TABLE_SHIPPING_DESCRIPTION schliessen
            $ls->Close();
            
            // Eintrag für eine shipping method als neuen Eintrag in $data
            $data[] = $arr;
            
            // zur nächsten shipping method von Abfrage auf  TABLE_SHIPPING springen
            $rs->MoveNext();
        }$rs->Close();

        // $data in $list
        $list = $data;

        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    } catch (Exception $ex) {
        // Exception gefangen, also daraus Fehlermeldung bauen
        $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    
    // Alle shipping methods in $result
    $result['result'] = $list;
    
    // Rückgabe
    return $result;
}
?>