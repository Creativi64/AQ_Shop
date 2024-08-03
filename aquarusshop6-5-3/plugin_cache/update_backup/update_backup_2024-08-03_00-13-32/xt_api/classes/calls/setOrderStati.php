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
 * @param type $order_ids BestellIDs als int Array
 * @param type $newStatus Neuer Status der Bestellung
 * @param type $comments
 * @param type $sendmail Soll eine Infomail verschickt werden
 * @param type $sendcomments Soll der Kommentar mit verschickt werden
 * @return type
 */
function setOrderStati($user, $pass, $order_ids, $newStatus, $comments = '', $sendmail = '0', $sendcomments) {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // hält Fehlermeldungen
    $errMsgs = "";
    $errCount = 0;
    
    // über order_ids laufen
    foreach ((array) $order_ids as $i => $order_id) {
        
        // setOrderStatus aufrufen
        $singleResult = setOrderStatus($user, $pass, $order_id, $newStatus, $comments, $sendmail, "", $sendcomments);
        
        
        // trat ein Fehler auf?
        if( $singleResult['result'] = false ){
            // Dann merken
            $errMsgs += $singleResult['message'] + "\n";
            $errCount ++;
        }
        
    }
    
    // Erfolgsmeldung setzen, wenn keine Fehler aufgelaufen sind!
    if( $errCount == 0 ){
        $result['result'] = true;
        $result['message'] = SoapHelper::MessageSuccess . " stati updates with new status (" . $newStatus . ")";
    }
    else{
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " stati updates with new status (" . $newStatus . ") generated " + $errCount + " errors:\n" + $errMsgs;
        
    }

    // Rückgabe
    return $result;
}
?>