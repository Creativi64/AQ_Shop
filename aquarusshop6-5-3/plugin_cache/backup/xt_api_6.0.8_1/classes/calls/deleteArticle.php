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
 * Einen Artikel löschen
 * @param type $user
 * @param type $pass
 * @param type $products_id
 * @param type $external_id
 * @return string|boolean
 */
function deleteArticle($user = '', $pass = '', $products_id = '', $external_id = '') {

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
    $result['products_id'] = (int)$products_id;
    $result['external_id'] = $external_id;
    
    // wenn $products_id leer, dann muss $external_id gefüllt sein, um die catId aufzulösen
    if( strlen( $products_id ) == 0 && strlen( $external_id ) == 0 ){
        $result['result'] = false;
        $result['message'] = "products_id OR external_id have to be set!";
        return $result;
    }
    
    // $products_id ist leer oder $products_id == 0, dann auflösen per $external_id
    if( strlen( $products_id ) == 0 ||  $products_id == 0){
        
        // Gibt es Produkt schon in XT? Anhand von external_id suchen
        $products_id = SoapHelper::getProductID($external_id);
        
        // ID konnte NICHT ermittelt werden anhand external_id...Fehler melden und raus
        if( $products_id === false ){
            $result['result'] = false;
            $result['message'] = SoapHelper::MessageFail . " unable to resolve products_id by external_id: " . $external_id;
            return $result;
        }
    }
    
    // Neues product Objekt anlegen
    $product = new product;
    
    // Product anhand products_id löschen
    $unsetResp = $product->_delete($products_id);
    
    if( $unsetResp === false ){
        $result['message'] = SoapHelper::MessageFail . " Unable to delete article.";
        // Erfolgsmeldung setzen
        $result['result'] = false;
    }
    else{
        $result['message'] = SoapHelper::MessageSuccess . " article: ". $products_id ." deleted";
        // Products_ID in Antwort zurückgeben
        $result['products_id'] = (int)$products_id;
        // Erfolgsmeldung setzen
        $result['result'] = true;
    }
     
    return $result;
}
?>