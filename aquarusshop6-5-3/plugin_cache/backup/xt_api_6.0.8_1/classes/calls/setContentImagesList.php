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
 * @param type $listOfContentImages
 * @return string
 */
function setContentImagesList($user, $pass, $listOfContentImages) {
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result[0]['content_id'] = "";
        $result[0]['external_id'] = "";
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Ist $listOfProductImages KEIN array?
    if (!is_array($listOfContentImages)) {
        // Raus mit Fehlermeldung
        $result[0]['content_id'] = "";
        $result[0]['external_id'] = "";
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageFail . " Message: item no array";
        return $result;
    }

    // Über array $listOfContentImages laufen und Einträge in $images
    // Dort sind jeweils die Bilderreihen für ein einzelnes Content enthalten
    foreach ($listOfContentImages as $index => $images) {

        try {
            // Methode setContentImages aufrufen
            $itemResult = setContentImages($user, $pass, $images);
            
        } catch (Exception $ex) {
            // Exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['content_id'] = "";
            $itemResult['external_id'] = "";
            $itemResult['result'] = false;
            $itemResult['message'] = SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        
        // Neues array in $result eintragen mit Meldungen für einzelnen Content
        $result[] = array(
            'content_id' => $itemResult['content_id'],
            'external_id' => $itemResult['external_id'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // Ist kein Eintrag in array $result vorhanden?
    if (count($result) == 0) {
        // Fehlermeldung in $result eintragen
        $result[] = array(
            'content_id' => "",
            'external_id' => "",
            'result' => false,
            'message' => SoapHelper::MessageFail . " Message: no input specified",);
    }

    // Rückgabe
    return $result;
}
?>