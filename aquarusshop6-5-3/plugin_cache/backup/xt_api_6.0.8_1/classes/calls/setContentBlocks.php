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
 * @return string
 */
function setContentBlocks($user = '', $pass = '', $list = '') {
    
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
        $result[0]['message'] = "item no array";
        return $result;
    }

    // Ergebnis array bauen
    $result = array();

    // Über alle Contents in $list laufen
    foreach ((array) $list as $index => $contentBlockItem) {
        try {
            // EInzelnen Block per setContentBlock setzen
            $itemResult = setContentBlock($user, $pass, $contentBlockItem);
        } catch (Exception $ex) {
            // Ein exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = "Update setContentBlocks Item Failed: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        // Ergebnis von setContent für einzelen Contents in $result hinzufügen
        $result[] = array(
            'external_id' => $itemResult['external_id'],
            'contentBlock_id' => $itemResult['contentBlock_id'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // ISt $result leer? Dh keine Content wurde eingetragen
    if (count($result) == 0) {
        // Fehlermeldung setzen
        $result[] = array(
            'external_id' => "null",
            'result' => false,
            'message' => "no input specified",);
    }

    // Rückgabe
    return $result;
}
?>