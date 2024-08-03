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
 * 
 * @param type $user
 * @param type $pass
 * @param type $list
 * @return boolean|string
 */
function setCategories($user = '', $pass = '', $list = '') {

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

    // Über alle Kategorien in $list laufen
    foreach ((array) $list as $index => $categoryItem) {
        try {
            // EInzelne Kategorie per setCategory setzen
            // ACHTUNG: Kein nestedSet rebuild!
            $itemResult = setCategory($user, $pass, $categoryItem, false);
        } catch (Exception $ex) {
            // Ein exception wurde gefangen, also Fehlermeldung schreiben
            $itemResult['result'] = false;
            $itemResult['message'] = SoapHelper::MessageFail . " Update setCategories Item Failed: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        }
        // Ergebnis von setCategory für einzelen Kategorie in $result hinzufügen
        $result[] = array(
            'external_id' => $itemResult['external_id'],
            'categories_id' => $itemResult['categories_id'],
            'result' => $itemResult['result'],
            'message' => $itemResult['message'],);
    }

    // Nested sets am Ende berechnen lassen
    $nested_set = new nested_set();
    $nested_set->setTable(TABLE_CATEGORIES);
    $nested_set->setTableDescription(TABLE_CATEGORIES_DESCRIPTION);

    cat_buildNestedSet( $nested_set );

    // Ist $result leer? D.h. keine Kategorie wurde eingetragen
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