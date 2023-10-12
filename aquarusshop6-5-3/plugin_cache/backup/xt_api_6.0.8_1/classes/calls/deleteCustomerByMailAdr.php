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
 * @param type $user
 * @param type $pass
 * @param type $customer_mail
 * @param type $shop_id
 * @return boolean
 */
function deleteCustomerByMailAdr($user = '', $pass = '', $customer_mail = '', $shop_id = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // Antwort enthält die Daten
    $result['customer_mail'] = $customer_mail;
    $result['shop_id'] = $shop_id;
    
    // Gibt es Kunde im shop? Anhand der Kunden Emailadr und shopId im System suchen
    $customers_id = SoapHelper::getCustomersIDByEmail($customer_mail, $shop_id );

    if( $customers_id === false ){
        // Customer ID kann NICHT ermittelt werden
        $result['message'] = SoapHelper::MessageFail . " Unable to find customer.";
        $result['result'] = false;
        return $result;
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