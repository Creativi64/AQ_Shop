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
 * @param type $id
 * @param type $external_id
 * @return string
 */
function deleteMediaXT($user = '', $pass = '', $id = -1, $external_id = '') {
    
    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Media ID ermitteln über external_id, falls gesetzt
    if( strlen( $external_id ) > 0 ){
        $id = SoapHelper::getMediaIDByexternalID($external_id);
    }
    
    // Media nicht gefunden anhand von IDs
    if( $id === false ){
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " unable to find media by external_id: " . $external_id;
        return $result;
    }
    
    global $db;
    
    $sql = "SELECT file FROM " . TABLE_MEDIA . " WHERE id=" . $id ;
    $filename = $db->getOne($sql);
    
    // Datei löschen
    deleteMediaFile( $filename );
    
    // Neues Media Objekt anlegen
    $md = new MediaData();
    // Alle Einträge in DB löschen
    $unsetResp = $md->unsetMediaData($id);
   
    if( $unsetResp === false ){
        $result['message'] = SoapHelper::MessageFail . " Unable to delete media.";
        $result['result'] = false;
    }
    else{
        $result['message'] = SoapHelper::MessageSuccess . ' media: '. $id . ' deleted';
        $result['result'] = true;
    }
    
    return $result;
}

function deleteMediaFile( $filename ){
    if(strlen($filename) > 0){
        
        $dstFolder = _SRV_WEBROOT._SRV_WEB_MEDIA_FILES;
        $filePath = $dstFolder . $filename;

        // Wenn $imageFolder ein Ordner und beschreibbar ist
        if (is_dir($dstFolder) && is_writable($dstFolder)) {
            if( file_exists($filePath ) ){
                unlink($filePath);
            }   
        }   
    }    
}
?>
