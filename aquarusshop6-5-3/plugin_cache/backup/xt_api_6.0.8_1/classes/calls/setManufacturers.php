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
 * @param type $user
 * @param type $pass
 * @param type $list
 * @return boolean|string
 */
function setManufacturers($user = '', $pass = '', $list = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Ist $list KEIN array
    if (!is_array($list)) {
        // Mit Fehlermeldung raus
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageFail . " item no array";
        return $result;
    }

    // Ergebnis array bauen
    $result = array();

    // Über alle Hersteller in $list laufen
    foreach ((array) $list as $index => $manufacturerItem) {
        try {
            // Einzelnen Hersteller per setManufacturer setzen
            $itemResult = setManufacturer($user, $pass, $manufacturerItem);
        } catch (Exception $ex) {
            // Ein exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = SoapHelper::MessageFail . " Update setManufacturers Item Failed: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        // Ergebnis von setManufacturer für einzelen Hersteller in $result hinzufügen
        $result[] = array(
            'external_id' => $itemResult['external_id'],
            'manufacturers_id' => $itemResult['manufacturers_id'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // ISt $result leer? Dh kein Hersteller wurde eingetragen
    if (count($result) == 0) {
        // Fehlermeldung setzen
        $result[] = array(
            'external_id' => "null",
            'result' => false,
            'message' => SoapHelper::MessageFail . " no input specified",);
    }

    // Rückgabe
    return $result;
}
?>