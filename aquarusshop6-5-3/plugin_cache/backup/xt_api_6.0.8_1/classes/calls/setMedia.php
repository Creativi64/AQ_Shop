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
 * @param type $link_id
 * @param type $isLinkExternal
 * @param type $className
 * @param type $mediaItem
 * @return string
 */
function setMedia($user = '', $pass = '', $link_id, $isLinkExternal = true, $className, $mediaItem = '') {
    global $xtPlugin;
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setMedia.php:setMedia_top')) ? eval($plugin_code) : false;

    // ISt Link ID eine externe ID? Dann auflösen zu interner ID
    if( $isLinkExternal == true ){
        
        $internalId;
        
        switch( $className ){
            case "category":
                $internalId = SoapHelper::getCategoriesIDByexternalID( $link_id );
                break;
            
            case "manufacturer":
                $internalId = SoapHelper::getManufacturersIDByexternalID( $link_id );
                break;
            
            case "attribute":
                $internalId = SoapHelper::getAttributesIDByexternalID( $link_id );
                break;
        }
        
        // Konnte ID aufgelöst werden?
        if( $link_id === false ){
            // Fehlermeldung ausgeben und raus
            $result['result'] = false;
            $result['external_id'] = $link_id;
            $result['message'] = SoapHelper::MessageFail . " Can not resolve external id: " . $link_id . " class: " . $className;
            return $result;
            
            
        }
        
        // interne ID einmappen
        $link_id = $internalId;
    }

    // String für evlt. Exceptionmeldungen
    $Exception = "";
    
    
    try { // 
             
        // Was für ein medium (Bild, Dokument) wird hochgeladen?
        
        if( $mediaItem["mediatype"] == "image" ){
            // Ein Bild wird hochgeladen
            
            switch( $className ){
                // Ein Kategorie Bild hochladen
                case "category":
                    processMediaImage($mediaItem["filedata"], $mediaItem["filename"], $link_id, "category", true, $mediaItem['image_url'], $mediaItem['image_hash']);
                    break;
                
                // Ein Hersteller Bild hochladen
                case "manufacturer":
                    processMediaImage($mediaItem["filedata"], $mediaItem["filename"], $link_id, "manufacturer", true, $mediaItem['image_url'], $mediaItem['image_hash']);
                    break;
                
                // Ein Attribut Bild hochladen
                case "attribute":
                    processMediaImage($mediaItem["filedata"], $mediaItem["filename"], $link_id, "xt_master_slave", true, $mediaItem['image_url'], $mediaItem['image_hash']);
                    break;
                
                
                
            }
            
            // ACHTUNG: TODO: wenn es mehrere Bilder sind, dann muss noch SoapHelper::clearMediaImageLinkFromProduct($productID); aufgerufen werden!
         
        }
        
        //Hookpoint Bottom
        ($plugin_code = $xtPlugin->PluginCode('setMedia.php:setMedia_bottom')) ? eval($plugin_code) : false;
        
    } catch (Exception $ex) {
        // Eine Exception wurde gefangen, also Fehlermeldung schriben und raus
        $Exception.=SoapHelper::MessageFail . " setMedia: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
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


/**
 * Ein Bild einfügen
 * Funktion übernommen und erweitert von SoapHelper::processImage
 * @global type $db
 * @param type $imageBase64
 * @param type $name
 * @param type $link_id
 * @param type $class
 * @param type $firstimage
 * @param type $imageUrl
 * @param type $imageHash
 * @return type
 * @throws Exception
 */
function processMediaImage($imageBase64 = '', $name = '', $link_id, $class, $firstimage = true, $imageUrl, $imageHash) {

    // Keine Base64 Bilddaten = raus
    if (strlen($imageBase64) < 20 && empty($imageUrl)){
        return;   
    }
    // Kein Bildname = raus 
    if ($name == ''){
        return;
    }


    // Dateipfad auf Ordner für Original-Grafiken (=unskaliert) bauen
    $imageFolder = _SRV_WEBROOT . _SRV_WEB_IMAGES . 'org/';

    // Wenn $imageFolder ein Ordner und beschreibbar ist
    if (is_dir($imageFolder) && is_writable($imageFolder)) {

        // Garfikdaten ( Base64) in Datei schreiben in imageFolder mit Dateinamen $name
        //file_put_contents($imageFolder . $name, $imageBase64);

        $filePath = $imageFolder . $name;
        $fileHasChanged = false;

        if (empty($imageHash)) {
            // Ohne Hash kann kein Check erfolgen, deshalb später Datei immer schreiben
            $fileHasChanged = true;
        } else {
            if (!file_exists($filePath)) {
                $fileHasChanged = true;
            } else if (is_file($filePath) && is_readable($filePath) && is_writable($filePath)) {
                // Prüfen, ob sich vorhandene und gelieferte Datei unterscheiden
                $hash = md5_file($filePath);
                if (strtolower($hash) !== strtolower($imageHash)) {
                    $fileHasChanged = true;
                }
            }
        }

        if ($fileHasChanged) {
            if (!empty($imageBase64)) {
                // Base64-Daten verwenden falls vorhanden
                file_put_contents($filePath, $imageBase64);
            } else if (!empty($imageUrl)) {
                // Ansonsten von URL laden wenn möglich
                $resource = fopen($imageUrl, 'r');
                if ($resource === false) {
                    // Falls das Bild nicht geladen werden kann: Verarbeitung hier abbrechen
                    return;
                }
                file_put_contents($filePath, $resource);
                fclose($resource);
            }
        }

        // resizen und einsortieren in die entsprechenden verzeichnisse
        $md = new MediaImages;
        // Klasse setzen
        $md->class = $class;


        $md->processImage($name, true);

        global $db;

        // media id für diese Datei anhand von $name aus Tabelle TABLE_MEDIA holen
        $sql = "select id from " . TABLE_MEDIA . " where file='" . $name . "' ";
        $media_id = $db->GetOne($sql);

        // Es gibt noch keine Eintrag in TABLE_MEDIA für diese Datei, also anlegen
        if (empty($media_id)) {
            // tabelle media befuellen
            // Neues MediaData Obj bauen
            $md = new MediaData();
            // Dateityp anhand von Dateisuffix ermitteln
            $type = $md->_getFileTypesByExtension($name);
            // Dateiname und Typ setzen in $md und in DB anlegen
            //$md->setMediaData(array('file' => $name, 'type' => $type));

            $md->setMediaData(array('file' => $name, 'type' => $type, 'class' => $class) );


            // nun die media Id aus DB ziehen
            // TODO: Frage: Warum kennt das Obj $md seine ID nicht? Weshalb muss sie hier erneut aus DB abgefragt werden?
            $media_id = $db->getOne("select id from " . TABLE_MEDIA . " where file='" . $name . "' ");
        }

        // wenn $firstimage == false UND von Typ boolean!
        // '===' Operator vergleicht Wert UND Typ!
        // dh Grafik ist NICHT das erste Bild für Kategorie/Produkt etc, so dass ein Eintrag in 
        // Tabelle TABLE_MEDIA_LINK mit Zuweisung zwischen link_id und mediaId angelegt werden
        if (false === $firstimage) {
            // Evtl. Excetion soll gefangen werden
            try {
                // Eintrag für Tabelle TABLE_MEDIA_LINK erzeugen
                // zuordung zum produkt:
                $data = array();
                $data['type'] = "images";
                $data['class'] = $class;
                $data['link_id'] = $link_id; // zB Kategorie ID oder Produkt ID
                $data['m_id'] = $media_id; // media id
                // Daten in Tabelle anlegen
                $oC = new adminDB_DataSave(TABLE_MEDIA_LINK, $data, false, __FILE__); // true == includes language_code
                $objC = $oC->saveDataSet();
            } catch (Exception $ex) {
                // Exception wurde gefangen
                // und eine neue Exception wird geworfen
                throw new Exception('Cant create Image ' . $imageFolder . ' is not writable');
            }
        }
    } else {
        // Hier wird hinverzweigt, wenn Bilderordner NICHT beschreibbar ist
        // Eine neue Exception wird geworfen
        throw new Exception('Cant create Image ' . $imageFolder . ' is not writable');
    }
}
?>