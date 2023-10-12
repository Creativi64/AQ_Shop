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
 * @param type $customers_id
 * @param type $external_id
 * @param type $extNumberRangeCustomerId
 * @param type $extNumberRangeDeliveryAdr
 * @return type
 */
function getCustomer($user, $pass, $customers_id, $external_id, $extNumberRangeCustomerId = 0, $extNumberRangeDeliveryAdr = 0) {
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    $result['message'] = "";
    
    
    // external_id auflösen in customers_id
    if (strlen($external_id)>0){
        $customers_id = SoapHelper::getCustomersIDByexternalID($external_id);
    }
    
    // weder customers_id noch external_id gesetzt
    if (strlen($customers_id) == 0 || $customers_id == 0 || $customers_id == null){
        $result['message'] = SoapHelper::MessageFail . " unable to resolve customers_id";
        $result['result'] = false;
        return $result;
    }
    
    
    
    global $db;
    
    // customer obj bauen 
    $customer = new customer($customers_id);


    if (count($customer->customer_info)==0 or !is_array($customer->customer_info)) {
        $result['message'] = SoapHelper::MessageFail . " customer_id ".$customers_id." not found";
        $result['result'] = false;
        return $result;
    }
    
    // daten einmappen in unser rückgabe array
    $customerData = array();
    $customerData["customer"] = $customer->customer_info;

    
    $getAddressList = $customer->_getAdressList($customers_id);
    if( $getAddressList != false ){
        $customerData["customer"]["customer_addresses"] = $getAddressList;
    }
    
    // Datenkorrekturen
    
    // im Hauptdatensatz
    $customerData["customer"]["date_added"] = SoapHelper::checkIsoDate( $customerData["customer"]["date_added"]);
    $customerData["customer"]["last_modified"] = SoapHelper::checkIsoDate( $customerData["customer"]["last_modified"]);
    
    // prüfen ob der erhaltene customer schon eine external_id hat
    if($customerData["customer"]["external_id"] == null || strlen($customerData["customer"]["external_id"]) == 0 ){
        $external_id = $customers_id + $extNumberRangeCustomerId;
        $customerData["customer"]["external_id"] = $external_id;

        //conf.inc.php prüfen ob Writeback stattfinden soll
        if( XT_API_CUSTOMER_WRITE_BACK_EXT_ID == 'true' ){
            $db->Execute("UPDATE " . TABLE_CUSTOMERS . " SET external_id='" . (string) $external_id . "', customers_cid='". (string) $external_id. "' WHERE customers_id= ". $customers_id );
        }    
    }
    
    // Bereinigung in Adressen
    for( $i = 0; $i < count($customerData["customer"]["customer_addresses"]); $i++){
        //Datumsangabe prüfen und evtl. anpassen
        $customerData["customer"]["customer_addresses"][$i]["date_added"] = SoapHelper::checkIsoDate($customerData["customer"]["customer_addresses"][$i]["date_added"]);
        $customerData["customer"]["customer_addresses"][$i]["last_modified"] = SoapHelper::checkIsoDate( $customerData["customer"]["customer_addresses"][$i]["last_modified"]);
        $customerData["customer"]["customer_addresses"][$i]["customers_dob"] = SoapHelper::checkIsoDate( $customerData["customer"]["customer_addresses"][$i]["customers_dob"]);
        
        if($customerData["customer"]["customer_addresses"][$i]["external_id"] == null || strlen($customerData["customer"]["customer_addresses"][$i]["external_id"]) == 0 ){
            $external_id = $customerData["customer"]["customer_addresses"][$i]["address_book_id"] + $extNumberRangeDeliveryAdr;
            $customerData["customer"]["customer_addresses"][$i]["external_id"] = $external_id;
        }        
    }
    
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess;
    $result['customerData'] = $customerData["customer"];
    return $result;
}
?>
