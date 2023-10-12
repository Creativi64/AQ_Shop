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
 * @global type $db
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $list
 * @return string
 */
function setStocks($user = '', $pass = '', $list = '') {
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // ist $list KEIN array
    if (!is_array($list)) {
        // dann mit Fehlermeldung raus
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageFail . " Item no array";
        return $result;
    }
    
    global $db, $xtPlugin;
    
    // Array für Antwort
    $result = array();
    
    // Über alle Einträge(=Produkte) in $list laufen
    foreach ((array) $list as $index => $data) {
        try {
            
            // Produkt ID ermitteln
            $productID = SoapHelper::getProductID($data['external_id'], $data['products_model'], $data['products_id']);

            // ProduktID konnte nicht ermittelt werden
            if (false === $productID) {
                // Fehlermeldung für dieses Produkt setzen
                $itemResult['result'] = false;
                $itemResult['message'] = SoapHelper::MessageFail . " Update setStocks Item Failed: products not found " . $data['products_model'];
            } else {
                // Hook 1 - _SOAP_set_stock_top
                ($plugin_code = $xtPlugin->PluginCode('setStocks.php:SOAP_set_stock_top')) ? eval($plugin_code) : false;
                
                // Die ProduktID konnte ermittlet werden, also Produktmenge products_quantity in DB updaten auf neuen Wert
                $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET products_quantity='" . (double) $data['products_quantity'] . "' WHERE products_id='" . $productID . "'");
                
                // Hook 2 - _SOAP_set_stock_bottom
                ($plugin_code = $xtPlugin->PluginCode('setStocks.php:SOAP_set_stock_bottom')) ? eval($plugin_code) : false;

                // Erfolgsmeldung für dieses Produkt setzen
                $itemResult['result'] = true;
                $itemResult['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt internal products id:" . $productID . " products model:" . $data['products_model'];
            }
        } catch (Exception $ex) {
            // Eine exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = SoapHelper::MessageFail . " Update setStocks Item Failed: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        
        // In Antwortarray für dieses Produkt die Erfolgs/Fehlermeldung als neuen Eintrag einfügen
        $result[] = array(
            'products_model' => $data['products_model'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // Ist $result leer, dh kein Produkt wurde gefunden/upgedated
    if (count($result) == 0) {
        // Eine Fehlermeldung eintragen
        $result[] = array(
            'products_model' => "null",
            'result' => false,
            'message' => SoapHelper::MessageFail . " no input specified",);
    }

    // Rückgabe
    return $result;
}
?>
