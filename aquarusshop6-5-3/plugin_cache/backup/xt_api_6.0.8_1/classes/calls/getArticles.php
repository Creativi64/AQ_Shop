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
 * @param type $user
 * @param type $pass
 * @param type $offset
 * @param type $blocksize
 * @param type $filter
 * @param type $indivFieldsList
 * @return string
 */

function getArticles($user, $pass, $offset = "0" , $blocksize = "10", $filter = array(), $indivFieldsList) {

    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Filterausdruck bauen
    $where = buildFilter( $filter );

    global $db;

    // Offset Wert für Abfrage zu int casten
    $offset = (int) $offset;
    $blocksize = (int) $blocksize;

    $sql = "select products_id from " . TABLE_PRODUCTS . " " . $where . " LIMIT " . $offset . "," . $blocksize;

    // SQL Haupttabelle - Alle products_id für filter holen
    $gaRS = $db->Execute($sql );

    // Keine Treffer gefunden, raus
    if ($gaRS->RecordCount() == 0) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail. " no articles found.";
        return $result;
    }

    $exceptions = "";

    // Liste für alle Produktdaten
    $productsItemExportList = array();

    // über Treffer laufen
    while (!$gaRS->EOF) {
        // getArticle pro products_id aufrufen
        $getArticleResultItem = getArticle($user , $pass, $gaRS->fields['products_id'], '', $indivFieldsList);

        if( $getArticleResultItem["result"] ){
            $productsItemExportList[] = $getArticleResultItem["productsItemExport"];
        }
        else{
            $exceptions .= $getArticleResultItem["message"] . "  ";
        }

         // zum nächsten Treffer 
        $gaRS->MoveNext();
    }

    $result['result'] = true;

    if( strlen($exceptions) > 0 ){
        $result['result'] = false;
        $result['message'] = $exceptions;
    }

    $result["productsItemExportList"] = $productsItemExportList;

    return $result;
}



    
/**
 * Filterarray bauen
 * @param type $filter
 */
function buildFilter( $options ){
    
    // Ist $options ein gefülltes array?
    if (is_array($options) && count($options)) {

        // Über Filtereinträge in $options laufen
        foreach ((array) $options as $filterKey => $filterValue) {
            // Array für einzelnen Filter
            $f = array();

            // Backslashes entfernen
            $filterValue = stripslashes($filterValue);

            // $filterValue per regulären Ausdrücken zerlegen und in array $f schreiben
            // ACHTUNG: DIE FUNKTION stripslashes($filterValue) WIRD ERNEUT AUFGERUFEN! WARUM?
            // TODO: klären!
            // Hier soll wohl noch die Funktion filterValueTransformToArray genutzt werden
            preg_match('/^(\w+)(?: *)([>|=|<]+)(?: *)([ A-Za-z0-9\"\':\-@._]+)$/i', stripslashes($filterValue), $f);

            // Ist das array $f gefüllt worden (=hat die Zerlegung funktioniert)
            if (isset($f[1]) && isset($f[2]) && isset($f[3])) {

                // DB Column steht in 1
                $dbColumn = $f[1];
                // Operator sthet in 2; wieder in Slashes einhüllen
                $operator = addslashes($f[2]);
                // Wert für den Operator
                $dbColumnValue = $f[3];


                $firstLetter = substr($dbColumnValue, 0, 1);

                // Wenn erster Buchstabe ein ' oder ", dann dieses entfernen
                if ($firstLetter == "'" || $firstLetter == '"') {
                    $dbColumnValue = substr($dbColumnValue, 1, strlen($dbColumnValue));
                }

                // Letzten Buchstaben von $dbColumnValue holen
                $lastLetter = substr($dbColumnValue, -1);

                // Wenn letzter Buchstabe ein ' oder ", dann dieses entfernen
                if ($lastLetter == "'" || $lastLetter == '"') {
                    $dbColumnValue = substr($dbColumnValue, 0, strlen($dbColumnValue) - 1);
                }

                // switch über COlumnnamen
                switch ($dbColumn) {
                    case 'products_owner':
                        $filter[] = "products_owner  " . $operator . "  " . $dbColumnValue . "";
                        break;

                    case 'products_master_model':
                        $filter[] = "products_master_model  " . $operator . "  '" . $dbColumnValue . "'";
                        break;
                    default:
                        $filter[] = $dbColumn  . $operator . "  '" . $dbColumnValue . "'";
                        break;
                }
            }
        }

        $where = "";

        // Gibt es Abfragefilter?
        if (count($filter) > 0) {
            // WHERE Block um Filterausdrücker erweitern
            $where.= " where ";
            // Alle Filter per " and " verknüpfen und an $where packen
            $where.= implode(' and ', $filter);
        }
        
        // WHERE Filter zurück
        return $where;
        
    } else {
        // Parameter $options ist KEIN array und leer, dann leeren Filter zurück
        return "";
    }    
}
?>