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
 * @param type $filter
 * @param type $start
 * @param type $size
 * @param type $extNumberRangeCustomerId
 * @param type $extNumberRangeDeliveryAdr
 * @param type $indivFieldsList
 * @return type
 */
function getOrders($user, $pass, $filter = array(), $start = 0, $size = 1000, $extNumberRangeCustomerId = 0, $extNumberRangeDeliveryAdr = 0, $indivFieldsList = 0) {
    
    // Darf user zugreifen?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $result['message'] = "";
    
    // array für alle Bestellungen
    $list = array();
    
    try {
        // orders list abfragen mit Hilfsfunktion SoapHelper::getOrdersListByStatus
        $list = SoapHelper::getOrdersListByStatus($filter, $start, $size, $extNumberRangeCustomerId, $indivFieldsList );

        // transform list to bueroware list
        $list = SoapHelper::orderListTransform($list, $extNumberRangeDeliveryAdr, $indivFieldsList);

        // Charset bei Bedarf zu UTF-8 umwandeln lassen
        if (XT_API_CHARSET == 'ISO-8859-1') {
            $list = xtSoapCharsetConvert($list);
        }

        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    } catch (Exception $ex) {
        // Exception wurde gefangen, also Fehlermeldung schreiben
        $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    // Orders in $result mappen
    $result['result'] = $list;
    
    // Rückgabe
    return $result;
}
?>