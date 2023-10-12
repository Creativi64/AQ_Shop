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
 * @param type $manufacturers_id
 * @param type $external_id
 * @return boolean|string
 */
function deleteManufacturer($user = '', $pass = '', $manufacturers_id = '', $external_id = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // Antwort enthält die IDs
    $result['manufacturers_id'] = $manufacturers_id;
    $result['external_id'] = $external_id;

    // wenn $manufacturers_id leer, dann muss $external_id gefüllt sein, um die manID aufzulösen
    if( strlen( $manufacturers_id ) == 0 && strlen( $external_id ) == 0 ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " manufacturers_id OR external_id have to be set!";
        return $result;
        
    }
    
    // $manufacturers_id ist leer, dann auflösen per $external_id
    if( strlen( $manufacturers_id ) == 0 || $manufacturers_id == 0){
        // Gibt es Produkt schon in XT? Anhand von external_id suchen
        $manufacturers_id = SoapHelper::getManufacturersIDByexternalID($external_id);
        
        // ID konnte NICHT ermittelt werden anhand external_id...Fehler melden und raus
        if( $manufacturers_id === false ){
            $result['result'] = false;
            $result['message'] = SoapHelper::MessageFail . " unable to resolve manufacturers_id by external_id: " . $external_id;
            return $result;
        }
    }
    
    // Neues manufacturer Objekt anlegen
    $manu = new manufacturer;
    $unsetResp = $manu->_unset($manufacturers_id);    

    if( $unsetResp === false ){
        $result['message'] = SoapHelper::MessageFail . " Unable to delete manufacturer.";
        $result['result'] = false;
    }
    else{
        $result['message'] = SoapHelper::MessageSuccess . " manufacturer: ". $manufacturers_id ." deleted";
        $result['result'] = true;
    }
    
    return $result;
}
?>