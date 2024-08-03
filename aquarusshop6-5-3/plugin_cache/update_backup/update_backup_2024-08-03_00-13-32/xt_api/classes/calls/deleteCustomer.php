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
 * Einen Kundendatensatz anhand der ID ODER der externalID löschen
 * @param type $user
 * @param type $pass
 * @param type $products_id
 * @param type $external_id
 * @return string|boolean
 */
function deleteCustomer($user = '', $pass = '', $customers_id = '', $external_id = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    
    // wenn $customers_id leer, dann muss $external_id gefüllt sein, um die customerId zu ermittlen aufzulösen
    if( strlen( $customers_id ) == 0 && strlen( $external_id ) == 0 ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " customers_id OR external_id have to be set!";
        return $result;
        
    }
    
    // $customers_id ist leer, dann auflösen per $external_id
    if( strlen( $customers_id ) == 0 ||  $customers_id == 0){
        // Gibt es Kunde schon in XT? Anhand von external_id suchen
        
        $customers_id = SoapHelper::getCustomersIDByexternalID($external_id);
        
        // ID konnte NICHT ermittelt werden anhand external_id...Fehler melden und raus
        if( $customers_id === false ){
            $result['result'] = false;
            $result['message'] = SoapHelper::MessageFail . " Unable to resolve customers_id by external_id: " . $external_id;
            return $result;
            
        }
    }
    
    // Neues customer Objekt für Kunden 
    $cust = new customer;
    
    // HACK, sonst klappt löschen nicht!
    $cust ->position = "admin";
    $unsetResp = $cust ->_unset( $customers_id );
    
    

    if( $unsetResp === false ){
        $result['message'] = SoapHelper::MessageFail . " Unable to delete customer.";
        $result['result'] = false;
    }
    else{
        $result['message'] = SoapHelper::MessageSuccess . " customer: ". $customers_id ." deleted";
        $result['result'] = true;
    }
   
    return $result;
}
?>