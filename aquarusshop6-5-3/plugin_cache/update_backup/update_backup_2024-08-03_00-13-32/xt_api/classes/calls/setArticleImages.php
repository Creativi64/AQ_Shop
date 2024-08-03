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
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $ProductModel
 * @param type $images
 * @param type $productExternalId
 * @return string
 */
function setArticleImages($user = '', $pass = '', $ProductModel = '', $images = '', $productExternalId ='') {
    

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    if( !empty($productExternalId) ){
         // Produkt ID anhand von ProductModel oder external_id ermitteln
	$productID = SoapHelper::getProductID($productExternalId, $ProductModel);
    }
    else{
        // Produkt ID anhand von ProductModel anziehen
        $productID = SoapHelper::getProductIDByModel($ProductModel);
    }

    // $productID ein boolean UND false
    if (false === $productID) {
        // Fehlermeldung erzeugen und raus
        $result['result'] = false;
        //$result['message'] = SoapHelper::MessageFail . " product not exists externalID:" . $ProductItem['external_id'] . " productsModel:" . $ProductItem['products_model'] . " productsID:" . $ProductItem['products_id'];
        $result['message'] = SoapHelper::MessageFail . " product not exists externalID:" . $productExternalId . " productsModel:" . $ProductModel . " productsID:" . $ProductItem['products_id'];
        
        return $result;
    }
    
    
    global $xtPlugin;
    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setArticleImages.php:setArticleImages_top')) ? eval($plugin_code) : false;

    // String für evlt. Exceptionmeldungen
    $Exception = "";
    
    try { // product images
        
        // Ist das Hauptproduktbild gesetzt? 
        if( strlen( $images["image_name"]  ) > 0 ){
            SoapHelper::processImage($images['image'], $images["image_name"], $productID, true, "product",$images['image_url'], $images['image_hash']);
        }

        // Alle evtl. vorhandenen Bilder zu Produkt entfernen
        SoapHelper::clearMediaImageLinkFromProduct($productID);
        
        // Gibt es zusätzliche Bilder?
        if (is_array($images["products_images"]) && count($images["products_images"])) {
            // Über alle Produktbilder laufen
            foreach ((array) $images["products_images"] as $index => $data) {
                // Ein Priduktbild in DB anlegen per SoapHelper::processImage
                SoapHelper::processImage($data['image'], $data['image_name'], $productID, false, "product",$data['image_url'], $data['image_hash']);
            }
        }
        
        //Hookpoint Bottom
        ($plugin_code = $xtPlugin->PluginCode('setArticleImages.php:setArticleImages_bottom')) ? eval($plugin_code) : false;
        
    } catch (Exception $ex) {
        // Eine Exception wurde gefangen, also Fehlermeldung schriben und raus
        $Exception.=SoapHelper::MessageFail . " Update processImage: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " Message:" . $Exception;
        return $result;
    }

    // Erfolgsmeldung setzen
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess;

    // Rückgabe
    return $result;
}
?>