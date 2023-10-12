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
 * XT ProduktAttribute zurückliefern
 * @global type $store_handler
 * @global type $db
 * @param type $user SOAP Username
 * @param type $pass SOAP Passwort
 * @return type
 */
function setAttributes($user, $pass, $attributeItemList ) {
    global $store_handler, $db;

     // externe PHP Datei einbinden
    require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';
    
    // Darf der user überhaupt zugreifen?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $result['message'] = "";
    

    try {

        // Über alle Attribute in List laufen
        foreach ($attributeItemList as $attrKey => $attributeItem) {
            
            $result = setAttribute( $user, $pass, $attributeItem);
            
            // Rückmappen - Veyton Ids - wenns geklappt hat
            if( $result['result']  ){
                $attributeItemList[$attrKey]["attributes_id"] = $result["attributes_id"];
                $attributeItemList[$attrKey]["attributes_parent"] = $result["attributes_parent"];
            }
            else{
                // Hat nicht funktionier!
                $attributeItemList[$attrKey]["attributes_id"] = -1;
                $attributeItemList[$attrKey]["attributes_parent"] = -1;
            } 
           
        }
        

        // Alles ist gut gelaufen, also success message setzen
        $result['message'] = SoapHelper::MessageSuccess;
    
        
    } catch (Exception $ex) {
        // Es ist eine exception aufgetreten, also Fehlermeldung zurückgeben
        $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    // Array mit allen Attributdaten in $result packen
    $result['result'] = $attributeItemList;
    
    // Ergebnis zurückgebens
    return $result;
}

?>