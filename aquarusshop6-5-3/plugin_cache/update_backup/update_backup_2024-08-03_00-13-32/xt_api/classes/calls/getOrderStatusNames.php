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
 * @return type
 */
function getOrderStatusNames($user, $pass) {

    global $db;
    
    // Darf user zugreifen?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    //$filter = array('orders_status=16');
    $result['message'] = "";
    
    // array für alle Bestell-Stati
    $list = array();
    
    try {
        // Hilfsarray
        $data = array();

        $rs = $db->Execute("SELECT sd.status_id,sddesc.status_name FROM " . TABLE_SYSTEM_STATUS . " sd, " . TABLE_SYSTEM_STATUS_DESCRIPTION . " sddesc WHERE sd.status_class='order_status' and sddesc.status_id=sd.status_id and sddesc.language_code='de'");
                
        // Über Treffer laufen
        while (!$rs->EOF) {
            // Alle Felder als neuen Eintrag in $data
            $data[] = $rs->fields;
            
            // Zum nächsten Treffer springen
            $rs->MoveNext();
        }
        // Abfrage schliessen
        $rs->Close();

        // $data in $list
        $list = $data;
        
        
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