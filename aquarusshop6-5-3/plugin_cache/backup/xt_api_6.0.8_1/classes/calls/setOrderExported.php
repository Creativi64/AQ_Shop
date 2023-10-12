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
 * Bestellstatus für Export / orders_exported für eine Bestellung auf 1 = true setzen
 * @global type $db
 * @param type $user
 * @param type $pass
 * @param type $order_id ID der Bestellung
 * @return string
 */
function setOrderExported($user, $pass, $order_id) {
    global $db;
    
    // order_id zu int casten
    $order_id = (int) $order_id;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }


    // gibt es die bestell id?
    // In Tabelle TABLE_ORDERS suchen nach der Bestellung
    $rs = $db->Execute("select customers_id from " . TABLE_ORDERS . " where orders_id='" . $order_id . "'");
    
    // Bestellung NICHt in DB vorhanden,
    if ($rs->RecordCount() == 0) {
        // mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = "#FAIL 1001# orders id: " . $order_id . " does not exists";
        return $result;
    }

    // orders_exported in Tabelle TABLE_ORDERS für die Bestellung auf "1" setzen
    $updateSQL = "UPDATE " . TABLE_ORDERS . " set orders_exported='1' where orders_id='" . $order_id . "' ";
    $db->Execute($updateSQL);

    // Erfolgsmeldung setzen
    $result['result'] = true;
    $result['message'] = "#SUCCESS# orders id: " . $order_id . " set export flag";
    
    // Rückgabe
    return $result;
}

?>