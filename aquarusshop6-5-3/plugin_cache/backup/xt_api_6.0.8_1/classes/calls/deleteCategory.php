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
 * Eine Kategorie entfernen
 * @global type $db
 * @global type $language
 * @param type $user
 * @param type $pass
 * @param type $categoryItem
 * @return string
 */
function deleteCategory($user = '', $pass = '', $categories_id = '', $external_id = '', $doRemoveSubCategories = false) {

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
    $result['categories_id'] = $categories_id;
    $result['external_id'] = $external_id;
    
    
    
    // wenn $categories_id leer, dann muss $external_id gefüllt sein, um die catId aufzulösen
    if( strlen( $categories_id ) == 0 && strlen( $external_id ) == 0 ){
        $result['result'] = false;
        $result['message'] = "categories_id OR external_id have to be set!";
        return $result;
        
    }
    
    // $categories_id ist leer oder $categories_id ist 0, dann auflösen per $external_id
    if( strlen( $categories_id ) == 0 ||  $categories_id == 0){
        // Gibt es Kategorie schon in XT? Anhand von external_id suchen
        $categories_id = SoapHelper::getCategoriesIDByexternalID($external_id);
        
        // ID konnte NICHT ermittelt werden anhand external_id...Fehler melden und raus
        if( $categories_id === false ){
            $result['result'] = false;
            $result['message'] = SoapHelper::MessageFail . " unable to resolve categories_id by external_id: " . $external_id;
            return $result;
        }  else {
            $result['categories_id'] = $categories_id;
        }
        
    }
    
    // Neues category Objekt anlegen
    $category = new category;
    
    // Soll nur diese Kategorie gelöscht werden, oder auch alle Kinder davon?
    if( $doRemoveSubCategories ){
        
        // Ja, auch Kinder entfernen
        $unsetResp = $category->removeCategory($categories_id);
        
        if( $unsetResp === false ){
            $result['message'] = SoapHelper::MessageFail . " Unable to delete category.";
            // Erfolgsmeldung setzen
            $result['result'] = false;
        }
        else{
            $result['message'] = SoapHelper::MessageSuccess . " category: ". $categories_id ." and childcategories deleted";
            // Products_ID in Antwort zurückgeben
            $result['categories_id'] = (int)$categories_id;
            // Erfolgsmeldung setzen
            $result['result'] = true;
        }
    }
    else{
        // Nur einzelne Kategorie entfernen
        $unsetResp = $category->_delete($categories_id); 
        if( $unsetResp === false ){
            $result['message'] = SoapHelper::MessageFail . " Unable to delete category.";
            // Erfolgsmeldung setzen
            $result['result'] = false;
        }
        else{
            $result['message'] = SoapHelper::MessageSuccess . " category: ". $categories_id ." deleted";
            // Products_ID in Antwort zurückgeben
            $result['categories_id'] = (int)$categories_id;
            // Erfolgsmeldung setzen
            $result['result'] = true;
        }
    }
    
    return $result;
}
?>