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
 * @global type $language
 * @global type $db
 * @global type $language
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $attributeItem
 * @return string
 */
function setAttribute($user = '', $pass = '', $attributeItem = '') {

    // Globale Variablen $db, $language und $xtPlugin nutzen
    global $db, $language, $xtPlugin;

    // Nur einbinden, wenn PlugIn xt_master_slave installiert und aktiviert
    if (isset($xtPlugin->active_modules['xt_master_slave'])) {
        // externe PHP Datei einbinden
        require_once(_SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php');
    }
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // wenn PlugIn xt_master_slave nicht installiert oder deaktiviert
    if (!isset($xtPlugin->active_modules['xt_master_slave'])) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " Plugin xt_master_slave has to be installed or set to be active!";
        return $result;
    }
    
    // Ist $attributeItem KEIN array
    if (!is_array($attributeItem)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail. " AttributeItem no array";
        return $result;
    }    

    // Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setAttribute.php:setAttribute_top')) ? eval($plugin_code) : false;
    
     // get parent ID
    // ist Shop parent_id direkt gesetzt?
    if( (int)$attributeItem['attributes_parent'] > -1 ){
        $parentID = $attributeItem['attributes_parent'];
    }
    else{ 
        // Parent ID anhand von external_id (bw_id) auflösen
        
        // Ist Eintrag $attributeItem['parent_id'] gefüllt?
        if ((int)$attributeItem['attributes_ext_parent_id'] > -1) {

            // Garcia: Angepasst: Wenn externe parent id = 0, dann auch in Shop das übergeordnete Attribut 0 nehmen = Wurzelattribut
            if( $attributeItem['attributes_ext_parent_id'] == '0' ){
                $parentID = '0';
            }
            else{
                // Interne parentId holen
                $parentID = SoapHelper::getAttributesIDByexternalID($attributeItem['attributes_ext_parent_id']);

                // parentId wurde NICHT gefunden, dann raus mit Fehlermeldung!
                if (!$parentID) {
                    $result['result'] = false;
                    $result['message'] = SoapHelper::MessageFail . " Fehler: Attributparent BW ID: " . $attributeItem['attributes_ext_parent_id']. " nicht gefunden!";
                    // Raus mit Fehlermeldung
                    return $result;
                }
            }
        }  
    }

    
    // Shop attributes_id gesetzt
    if( (int)$attributeItem['attributes_id'] > -1 ){
        $attrID = $attributeItem['attributes_id'];
    }
    else{
        // ID anhand external_id (bw_id) auflösen
        $attrID = SoapHelper::getAttributesIDByexternalID($attributeItem['attributes_ext_id']);
 
        // Attribut ID noch nicht gesetzt, also einfügen
        if( ! $attrID ){
             // Attribut ID ist leer, also ein neues Attribut in Shop anlegen
             // anlegen in DB mit attributes_ext_id
             $db->Execute("INSERT INTO " . TABLE_PRODUCTS_ATTRIBUTES . " (bw_id) VALUES ('" . $attributeItem['attributes_ext_id'] . "')");
             // attributes_id setzen anhand von neuer ID aus DB
             $attrID = $db->Insert_ID();
        }        
    }

    // Array $data für Attributdaten bauen
    $data = array();

    // Neues array für Attribut bauen und mit Daten füllen
    $merkmal = array();
    $merkmal['attributes_id'] = (int) $attrID;
    $merkmal['attributes_parent'] =  $parentID;
    $merkmal['bw_id'] = (int) $attributeItem['attributes_ext_id'];
    $merkmal['sort_order'] = $attributeItem['sort_order'];
    $merkmal['attributes_templates_id'] = $attributeItem['attributes_templates_id'];
    $merkmal['attributes_model'] = SoapHelper::utf8helper($attributeItem['attributes_model']);
    $merkmal['attributes_image'] = $attributeItem['attributes_image'];

    // Über alle Sprachen laufen
    foreach (SoapHelper::getLanguageList() as $key => $val) {    
        // Attributname in bestimmter Sprache einfügen (nach Umwandlung zu UTF-8)
        $merkmal['attributes_name_' . $val['code']] = SoapHelper::utf8helper($attributeItem['attributes_name'][$val['code']] );

        // Attributbeschreibung in bestimmter Sprache einfügen (nach Umwandlung zu UTF-8)
        $merkmal['attributes_desc_' . $val['code']] = SoapHelper::utf8helper($attributeItem['attributes_desc'][$val['code']] );
    }

    // Gibt es Individualfelder für Attribut Haupttabelle = TABLE_PRODUCTS_ATTRIBUTES ?
    if( is_array( $attributeItem["indivFieldsList"]) ){
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach( $attributeItem["indivFieldsList"] as $key => $indivField ){
            // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS_ATTRIBUTES"
            if( $indivField["dstTable"] = "TABLE_PRODUCTS_ATTRIBUTES"){
                // value mappen in Product
                $merkmal[ $indivField["sqlFieldName"] ] = SoapHelper::utf8helper( $indivField["value"] );
            }
        }
    }

    // Neues xt_master_slave Objekt erzeugen
    $xt_master_slave = new xt_master_slave();
     
    $xt_master_slave->setPosition("admin");
    
    // Bild seperat setzen
    $xt_master_slave->_setImage( $merkmal['attributes_id'], $attributeItem['attributes_image'] );
     
    // Merkmal setzen 
    $setResp = $xt_master_slave->_set($merkmal);
    
    // Status setzen - ERST AM SCHLUSS!!! WEIL _setImage den status plattmacht!
    $setStatusResp = $xt_master_slave->_setStatus( $merkmal['attributes_id' ], $attributeItem['status']);
    
    // AttributeParent per SQL nachtragen
    $db->Execute("UPDATE " . TABLE_PRODUCTS_ATTRIBUTES . " SET attributes_parent=" . $merkmal['attributes_parent'] . " WHERE attributes_id=".$merkmal['attributes_id'] );
                   
    // Rückgabe der IDs 
    $result['attributes_id'] = $merkmal['attributes_id'];
    $result['attributes_parent'] = $merkmal['attributes_parent'];
    $result['external_id'] = $merkmal['bw_id']; 
    
    //Hookpoint Bottom
    ($plugin_code = $xtPlugin->PluginCode('setAttribute.php:setAttribute_bottom')) ? eval($plugin_code) : false;
    
    // Ist eine Fehlermeldung gesetzt worden in $Exception
    if (strlen($Exception) > 0) {
        // Fehlermeldung in $result setzen
        $result['result'] = false;
        $result['external_id'] = $attributeItem['external_id'];
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception;
        // Raus mit Fehlermeldung
        return $result;
    }

    // Setzen der Kategorie hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    
    $result['message'] = SoapHelper::MessageSuccess . " " . " attributes_id:". $merkmal['attributes_id'] . " external_id:" .  $merkmal['bw_id'];

    // Rückgabe
    return $result;
}
?>