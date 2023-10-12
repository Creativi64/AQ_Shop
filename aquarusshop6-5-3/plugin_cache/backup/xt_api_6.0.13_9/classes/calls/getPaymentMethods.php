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
 * Alle Zahlungsmethoden / PaymentMethods zurückliefern
 * @global type $store_handler
 * @global type $db
 * @param type $user
 * @param type $pass
 * @return type
 */
function getPaymentMethods($user, $pass){
    global $store_handler,$db;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if(!$hasAccess){
        $result['result']     = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    $result['message']     = "";
    
    // array für Zahlungsmethoden
    $list = array();
    
    try {
        // Hilfsarray für Zahlungsmethoden
        $data = array();
        
        // DB Abfrage: Alles aus Tabelle TABLE_PAYMENT abfragen
        $rs = $db->Execute("SELECT * FROM " . TABLE_PAYMENT);
        
        // Über alle Treffer laufen
        while (!$rs->EOF) {
            // Alle Felder einer Zeile in $arr
            $arr = $rs->fields;
            
            // Alle Beschreibungen (mehrsprachig) für eine Zahlungsart anhand von payment_id aus Tabelle TABLE_PAYMENT_DESCRIPTION laden
            $ls = $db->Execute("SELECT * FROM ".TABLE_PAYMENT_DESCRIPTION." WHERE payment_id='".$rs->fields['payment_id']."'");
            
            // Über alle Beschreibungen laufen
            while (!$ls->EOF) {
                // Beschreibungen pro Sprache in $arr schreiben
                $arr['payment_name'][$ls->fields['language_code']]=$ls->fields['payment_name']; 
                // zum nächsten Treffer der Beschreibungen
                $ls->MoveNext();
            }
            // Query schliessen
            $ls->Close();
            
            // $arr als neuen Eintrag in $data
            $data[] = $arr;
            
            // zum nächsten Treffer in Tabelle TABLE_PAYMENT
            $rs->MoveNext();
        }
        // Abfrage beenden
        $rs->Close();

        // $data in $list
        $list = $data;
        
        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    }catch (Exception $ex){
        // Exception gefangen, also Fehlermeldung schreiben
        $result['message'].= SoapHelper::MessageFail." Execption Message:".$ex->getMessage()." in ".$ex->getFile()."::".$ex->getLine().") ";
    }

    // Alle Zahlungsmethoden in $result mappen
    $result['result'] = $list;
    
    // Rückgabe
    return $result;
}
?>