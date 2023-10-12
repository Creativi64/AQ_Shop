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
 * Eine Liste von Artikeln in XT eintragen
 * @param type $user
 * @param type $pass
 * @param type $list Liste von Artikeln
 * @return string
 */
function setArticles($user = '', $pass = '', $list = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result[0]['result'] = false;
        $result[0]['message'] = SoapHelper::MessageLoginFailed;
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

    // $result array anlegen
    $result = array();
    
    // Über einzelne Produkte in $list laufen
    foreach ((array) $list as $index => $productItem) {
        try {
            // Einzelnes Produkt per setArticle eintragen
            $itemResult = setArticle($user, $pass, $productItem);
        } catch (Exception $ex) {
            // Es wurde eine exception gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = "Update setArticles Item Failed: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        // Neuen Eintrag in $result mit Meldungen für setArticle für einzelnes Produkt
        $result[] = array(
            'products_model' => $productItem['products_model'],
            'products_id' => $itemResult['products_id'],
            'external_id' => $itemResult['external_id'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // Ist $result leer? Also es wurke kein Produkt eingetragen
    if (count($result) == 0) {
        // Fehlermeldung in $result eintragen
        $result[] = array(
            'products_model' => "null",
            'result' => false,
            'message' => "no input specified",);
    }

    // Rückgabe
    return $result;
}

?>