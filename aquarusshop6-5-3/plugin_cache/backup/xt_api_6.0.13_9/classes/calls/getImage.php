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
 * Ein bestimmtes Bild zurückgeben
 * @param type $user
 * @param type $pass
 * @param type $image_dir Ordner des Bildes (default ist "org" )
 * @param type $image_name
 * @return string
 */
function getImage($user, $pass, $image_dir = 'org/', $image_name) {
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    $result['message'] = "";
    
   
    
    try {
        
        // Dateipfad auf Ordner für Grafiken  bauen
        $imageFolder = _SRV_WEBROOT . _SRV_WEB_IMAGES . $image_dir ;
        
        // Bilddaten laden
        $imageBase64 = file_get_contents($imageFolder . $image_name);
        
        if( ! $imageBase64){
            $result['message'].= SoapHelper::MessageFail . " Exception Message: Image not found: ". $imageFolder . $image_name;
            return $result;
        }
        else{
            // base64 Konvertierung von Bilddaten
            $imageBase64  = base64_encode( $imageBase64 );            
            
        }
        
        // Hilfsarray für Daten
        $data = array();      
        
        $data['imageBase64'] = $imageBase64;
            
            
        $result['result'] = $data;
        
        
            

        // Erfolgsmeldung setzen
        $result['message'] = SoapHelper::MessageSuccess;
    } catch (Exception $ex) {
        // Eine exception ist aufgetreten, also Fehlermeldung setzen
        $result['message'].= SoapHelper::MessageFail . " Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }
    
        
    // Rückgabe 
    return $result;
}

?>