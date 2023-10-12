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
 * @param type $order_id BestellID
 * @param type $newStatus Neuer Status der Bestellung
 * @param type $comments
 * @param type $sendmail Soll eine Infomail verschickt werden
 * @param type $indivFieldsList 
 * @param type $sendcomments Soll der Kommentar mit verschickt werden
 * @return string
 */
function setOrderStatus($user, $pass, $order_id, $newStatus, $comments = '', $sendmail = '0', $indivFieldsList='', $sendcomments = '1') {
    // order_id zu int casten
    $order_id = (int) $order_id;
    // $newStatus zu int casten
    $newStatus = (int) $newStatus;
    
    // array für debugmeldungen bauen
    $debug = array();
    $debug['order_id'] = $order_id;
    $debug['new_status'] = $newStatus;
    $debug['comments'] = $comments;
    $debug['sendmail'] = $sendmail;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    global $db;

    // gibt es die bestell id?
    $rs = $db->Execute("select customers_id from " . TABLE_ORDERS . " where orders_id='" . $order_id . "'");
    if ($rs->RecordCount() == 0) {
        // Nein, Bestellung gibt es nicht, also mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " orders id: " . $order_id . " does not exists";
        return $result;
    }

    // Gibt es diesen neuen Bestellstatus $newStatus überhaupt in deutscher Sprache?
    // ACHUTNG! HIER MÜSSTE EIGENTLICH NOCH MEHRSPRACHENUNTERSTÜTZUNG REIN!
    $ok = SoapHelper::getOrderStatus($newStatus, 'de');

    // Es gibt den neuen Status gar nicht
    if (!$ok) {
        // mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " status id: " . $newStatus . " does not exists";
        return $result;
    }

    // Neues Bestellungs Objekt bauen mit Daten aus DB Abfrage
    $order = new order($order_id, $rs->fields['customers_id']);
    
    // Soll Infomail verschickt werden über Statusänderung? Default = nein
    $send_mail = 'false';
    if ($sendmail == '1'){
        $send_mail = 'true';
    }

    // Soll Kommentar bei Infomail verschickt werden über Statusänderung? Default = Ja
    $send_comments = 'true';
    if ($sendcomments == '0'){
        $send_comments = 'false';
    }

    // Encoding von $comments anpassen
    $comments = SoapHelper::utf8helper($comments);
    
    // Bestellstatus neu setzen mit XT Systemfunktion _updateOrderStatus
    $order->_updateOrderStatus($newStatus, $comments, $send_mail, $send_comments, 'SOAP', '');

    // Wurden Inidvidualfelder mit übergeben?
    if( is_array( $indivFieldsList ) ){
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach( $indivFieldsList as $key => $indivField ){
            
            // Update ausführen, wenn Zieltabelle = "TABLE_PRODUCTS"
            if( $indivField["dstTable"] === "TABLE_ORDERS"){
                
                // ACHTUNG: Um SQL injection zu vermeiden, nutzen wir prepared statements
                
                // Statement vorbereiten ? ist Platzhalter für Daten
                $sth = $db->prepare( "update " . TABLE_ORDERS . " set " . $indivField["sqlFieldName"] . " = ? where orders_id = '" . (int)$order_id . "'");
                
                // Statement ausführen
                if( $indivField["value"] != NULL && strlen($indivField["value"]) > 0 ){
                    $db->execute($sth, SoapHelper::utf8helper( $indivField["value"] ) );
                }
                else{
                    // Leerstring rein, sonst Bug: siehe http://stackoverflow.com/questions/266431/empty-string-in-not-null-column-in-mysql
                    $db->execute($sth, " " );
                }
            }
        }
    }
    
    // Eroflgsmeldung setzen
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess . " orders id: " . $order_id . " status updates with new status (" . $newStatus . ")";
    
    // Rückgabe
    return $result;
}
?>