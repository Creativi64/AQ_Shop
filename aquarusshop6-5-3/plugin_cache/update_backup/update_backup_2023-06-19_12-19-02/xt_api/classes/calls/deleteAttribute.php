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
 * Ein Attribut löschen
 * @param type $user
 * @param type $pass
 * @param type $attributes_id
 * @param type $attributes_ext_id
 * @return string
 */
function deleteAttribute($user = '', $pass = '', $attributes_id = '', $attributes_ext_id = '') {

    // externe PHP Datei einbinden
    require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';   
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // wenn $attributes_id leer, dann muss $external_id gefüllt sein, um die Attribute ID aufzulösen
    if( strlen( $attributes_id ) == 0 && strlen( $attributes_ext_id ) == 0 ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " attributes_id OR attributes_ext_id have to be set!";
        return $result;
    }
    
    // $attributes_id ist leer, dann auflösen per $external_id
    if( strlen( $attributes_id ) == 0 ){
        // Gibt es Attribut schon in XT? Anhand von external_id suchen
        $attributes_id = SoapHelper::getAttributesIDByexternalID($attributes_ext_id);
        
        // ID konnte NICHT ermittelt werden anhand external_id...Fehler melden und raus
        if( $attributes_id === false ){
            $result['result'] = false;
            $result['message'] = SoapHelper::MessageFail . " unable to resolve attributes_id by attributes_ext_id: " . $attributes_ext_id;
            return $result;      
        }       
    }
    
    // Neues xt_master_slave Objekt erzeugen
    $xt_master_slave = new xt_master_slave;

    $unsetResp = $xt_master_slave->_unset($attributes_id);    

    if( $unsetResp === false ){
        $result['message'] = SoapHelper::MessageFail . " Unable to delete attribute.";
        $result['result'] = false;
    }
    else{
        $result['result'] = true;
        $result['message'] = SoapHelper::MessageSuccess;
        // Antwort enthält die IDs
        $result['attributes_id'] = (int)$attributes_id;
        $result['attributes_ext_id'] = (int)$attributes_ext_id;
    }
    
    return $result;
}
?>