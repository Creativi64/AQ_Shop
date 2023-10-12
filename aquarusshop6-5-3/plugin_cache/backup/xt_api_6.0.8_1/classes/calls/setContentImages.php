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
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $images
 * @param type $contentExternalId
 * @return string
 */
function setContentImages($user = '', $pass = '', $images = '', $contentExternalId = '')
{
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['content_id'] = "";
        $result['external_id'] = "";
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    global $xtPlugin;

    ($plugin_code = $xtPlugin->PluginCode('setContentImages.php:setContentImages_top')) ? eval($plugin_code) : false;

    if (empty($images['content_id'])) {
        // Content ID anhand von external_id ermitteln
        if ($contentExternalId == '' && isset($images['external_id'])) {
            $contentExternalId = $images['external_id'];
        }

        $contentID = SoapHelper::getContentIDByexternalID($contentExternalId);
    }
    else {
        $contentID = $images['content_id'];
    }

	// Ist $contentID ein boolean UND false
    if (false === $contentID) {
        // Fehlermeldung erzeugen und raus
        $result['content_id'] = "";
        $result['external_id'] = "";
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " content not exists externalID:" . $contentExternalId . " contentID:" . $images['content_id'];
        return $result;
    }

    // String für evlt. Exceptionmeldungen
    $Exception = "";
    
    try { // content images

        // Ist das Hauptbild gesetzt? 
        if( strlen( $images["image_name"]  ) > 0 ){
            SoapHelper::processImage($images['image'], $images["image_name"], $contentID, true, "content", $images['image_url'], $images['image_hash']);
        }
    
        // Gibt es weitere Bilder?
        if (is_array($images["content_images"])) {
            // Alle evtl. vorhandenen Bilder zu Content entfernen
            SoapHelper::clearMediaImageLinkFromElement($contentID, "content");
            
            // Über alle Contentbilder laufen
            foreach ((array) $images["content_images"] as $index => $data) {
                // Ein Contentbild in DB anlegen per SoapHelper::processImage
                SoapHelper::processImage($data['image'], $data['image_name'], $contentID, false, "content", $data['image_url'], $data['image_hash']);
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('setContentImages.php:setContentImages_bottom')) ? eval($plugin_code) : false;


    } catch (Exception $ex) {
        // Eine Exception wurde gefangen, also Fehlermeldung schriben und raus
        $Exception.=SoapHelper::MessageFail . " Update processImage: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
        $result['content_id'] = $contentID;
        $result['external_id'] = $contentExternalId;
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " Message:" . $Exception;
        return $result;
    }

    // Erfolgsmeldung setzen
    $result['content_id'] = $contentID;
    $result['external_id'] = $contentExternalId;
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess;

    // Rückgabe
    return $result;
}
?>