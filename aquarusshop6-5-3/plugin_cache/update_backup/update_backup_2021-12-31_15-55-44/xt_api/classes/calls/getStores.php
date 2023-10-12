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
 * Alle XT Stores/Mandanten zurückgeben
 * @global type $store_handler
 * @global type $db
 * @param type $user
 * @param type $pass
 * @return type
 */
function getStores($user, $pass) {
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
    
    // Array für stores
    $list = array();
    
    try {

        // Hilfsarray
        $data = array();
        
        // DB Abfrage: Alles aus Tabelle TABLE_MANDANT_CONFIG
        $rs = $db->Execute("SELECT * FROM " . TABLE_MANDANT_CONFIG);
        
        // Über Treffer laufen
        while (!$rs->EOF) {
            // Alle Felder als neuen Eintrag in $data
            $data[] = $rs->fields;
            
            // Zum nächsten Treffer springen
            $rs->MoveNext();
        }
        // Abfrage schliessen
        $rs->Close();

        // $data in $list
        $list = $data;
        
        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    } catch (Exception $ex) {
        // Exception gefnagen, also Fehlermeldung bauen
        $result['message'].= SoapHelper::MessageFail . " Exception Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    // Stores in $result setzen
    $result['result'] = $list;
    
    // Rückgabe
    return $result;
}
?>