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
 * Produktbilder für mehrere Produkte setzen
 * @param type $user
 * @param type $pass
 * @param type $listOfProductImages
 * @return string|boolean
 */
function setArticleImagesList($user, $pass, $listOfProductImages) {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Ist $listOfProductImages KEIN array?
    if (!is_array($listOfProductImages)) {
        // Raus mit Fehlermeldung
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageFail . " Message: item no array";
        return $result;
    }

    // Über array $listOfProductImages laufen und Einträge in $images
    // Dort sind jeweils die Bilderreihen für ein einzelnes Produkt enthalten
    foreach ($listOfProductImages as $index => $images) {

        try {
            // Methode setArticleImages aufrufen
            $itemResult = setArticleImages($user, $pass, $images['products_model'], $images, $images['product_external_id']);
            
            
        } catch (Exception $ex) {
            // Exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        
        // Neues array in $result eintragen mit Meldungen für einzelnes Produktmodell
        $result[] = array(
            'products_model' => $images['products_model'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // Ist kein Eintrag in array $result vorhanden?
    if (count($result) == 0) {
        // Fehlermeldung in $result eintragen
        $result[] = array(
            'products_model' => "null",
            'result' => false,
            'message' => SoapHelper::MessageFail . " Message: no input specified",);
    }

    // Rückgabe
    return $result;
}
?>