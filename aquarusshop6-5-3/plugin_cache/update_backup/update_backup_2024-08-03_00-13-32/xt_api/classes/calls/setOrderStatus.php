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
 * @global type $db
 * @param type $user
 * @param type $pass
 * @param type $order_id BestellID
 * @param type $newStatus Neuer Status der Bestellung
 * @param type $comments
 * @param type $sendmail Soll eine Infomail verschickt werden
 * @param type $indivFieldsList
 * @param type $sendcomments Soll der Kommentar mit verschickt werden
 * @param type $shippings Trackingdaten
 * @return string
 */
function setOrderStatus($user, $pass, $order_id, $newStatus, $comments = '', $sendmail = '0', $indivFieldsList='', $sendcomments = '1',$shippings='') {
    global $xtPlugin;
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

    // set shipments, xt ship and track
    $updated = false;
    if (isset($xtPlugin->active_modules['xt_ship_and_track']) && is_array($shippings) && XT_API_UPDATE_TRACKING=='true') {
      foreach($shippings as $id => $tracking_arr) {
        if (is_array($tracking_arr) && count ($tracking_arr)==2 && strlen($tracking_arr['tracking_id'])>4) {
            if (_updateTrackingInformation($order_id,$tracking_arr['tracking_id'],$tracking_arr['carrier_id'])===true) {
              $updated = true;
            }
        }
      }
    }

    // Bestellstatus neu setzen mit XT Systemfunktion _updateOrderStatus
    $order->_updateOrderStatus($newStatus, $comments, $send_mail, $send_comments, 'SOAP', '');

    // tracking email versenden ?
    if (isset($xtPlugin->active_modules['xt_ship_and_track']) && $updated == true && XT_API_AUTO_SEND_TRACKING=='true') {
      _sendAPITrackingEmail($order_id);
    }


    // Wurden Inidvidualfelder mit übergeben?
    if( is_array( $indivFieldsList ) ){
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach( $indivFieldsList as $key => $indivField ){

            // Update ausführen, wenn Zieltabelle = "TABLE_PRODUCTS"
            if( $indivField["dstTable"] === "TABLE_ORDERS"){

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

function _sendAPITrackingEmail($orders_id) {

  require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.tracking.php';

  $tracking = new tracking();
  // send e-imap_mail
  $tracking->sendTrackingMail($orders_id);

}

/**
Update tracking information for an order, push email in case of new tracking-id
*/
function _updateTrackingInformation($order_id,$tracking_id,$carrier_id) {
  global $db;

  if (_checkTrackingShipperId($carrier_id)==false) return;
  $rs = $db->Execute("SELECT * FROM xt_tracking WHERE tracking_order_id=? AND tracking_code=?",array($order_id,$tracking_id));
  if ($rs->RecordCount()==0) {
    $rrs = $db->Execute("SELECT id FROM xt_tracking_status WHERE tracking_status_code=0 and tracking_shipper_id=?",array($carrier_id));
    if ($rrs->RecordCount()==1) {
      $db->AutoExecute('xt_tracking',array('tracking_code'=>$tracking_id,'tracking_order_id'=>$order_id,'tracking_shipper_id'=>$carrier_id,'tracking_status_id'=>$rrs->fields['id']),'INSERT');
      return true;
    }
  }
}

function _checkTrackingShipperId($carrier_id) {
  global $db;

  $shipper=$db->getOne("SELECT * FROM xt_shipper WHERE id=?",array($carrier_id));
  if ($shipper== -1) return false;
  return true;

}

?>
