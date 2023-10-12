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

// Include einer externen php Datei
include_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';
require_once("xtFramework/classes/class.content_blocks.php");

/**
 * 
 * @global type $db
 * @global type $language
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $contentBlockItem
 * @return string
 */
function setContentBlock($user = '', $pass = '', $contentBlockItem = '') {

// Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // Ist $contentBlockItem KEIN array
    if (!is_array($contentBlockItem)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " ContentItem no array";
        return $result;
    }
    
    // globale Variablen $db, $language nutzen
    global $db, $language, $xtPlugin;

    ($plugin_code = $xtPlugin->PluginCode('setContentBlock.php:setContentBlock_top')) ? eval($plugin_code) : false;

    $data = array();

    // ContentBlock ID ermitteln
    // Ist external_id gesetzt?
    if( isset($contentBlockItem['external_id']) && strlen($contentBlockItem['external_id']) > 0 ){
        $contentBlockID = SoapHelper::getContentBlockIDByexternalID($contentBlockItem['external_id']);

        if( $contentBlockID === false ){
            // ContentBlock in DB einfügen
            $type = 'insert';
            // anlegen in DB mit external_id
            $db->Execute("INSERT INTO " . TABLE_CONTENT_BLOCK . " (external_id) VALUES ('" . $contentBlockItem['external_id'] . "')");
            // block_id setzen anhand von neuer ID aus DB
            $data['block_id'] = $db->Insert_ID();
            $contentBlockID = $data['block_id'];
        }
        else{
            $data['block_id'] = $contentBlockID;
            $type = 'update';
        }
    }
    else{
        // interne Content ID nutzen! Überhaupt gesetzt?
        if(  isset($contentBlockItem['block_id']) && strlen($contentBlockItem['block_id']) > 0 ){
            $contentBlockID = $contentBlockItem['block_id'];
            $data['block_id'] = $contentBlockID;
            $type = 'update';
        }
        else{
            // ContentBlock in DB einfügen
            $type = 'insert';
            // anlegen in DB mit external_id
            $db->Execute("INSERT INTO " . TABLE_CONTENT_BLOCK . " (external_id) VALUES ('')");
            // block_id setzen anhand von neuer ID aus DB
            $data['block_id'] = $db->Insert_ID();
            $contentBlockID = $data['block_id'];

        }
    }

    $data['block_status'] = $contentBlockItem['block_status'];
    $data['external_id'] = $contentBlockItem['external_id'];
    $data['block_tag'] = $contentBlockItem['block_tag'];

    // Neues Objekt stdClass anlegen
    $obj = new stdClass;
    
    // Neues contentBlock Objekt anlegen
    $contentBlock = new content_blocks;
    $contentBlock->store_field_exists = true;

    $obj = $contentBlock->_set($data);

    ($plugin_code = $xtPlugin->PluginCode('setContentBlock.php:setContentBlock_bottom')) ? eval($plugin_code) : false;

    // Hat das setzen des ContentBlocks funktioniert?
    if ($obj->success) {
        // Erfolgsmeldung setzen
        $result['result'] = true;
        $result['block_id'] = $data['block_id'];
        $result['external_id'] = $contentBlockItem['external_id'];
        $result['message'] = SoapHelper::MessageSuccess . " external_id:" . $contentBlockItem['external_id'] ;
        // Rückgabe mit Erfolgsmeldung
        return $result;
    } else {
        // Setzen des ContentBlocks hat NICHT funktioniert, also Fehlermeldung schreiben
        $Exception.="Failed on external_id " . $contentBlockItem['external_id'];
    }

    // Ist eine Fehlermeldung gesetzt worden in $Exception
    if (strlen($Exception) > 0) {
        // Fehlermeldung in $result setzen
        $result['result'] = false;
        $result['external_id'] = $contentBlockItem['external_id'];
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception;
        // Raus mit Fehlermeldung
        return $result;
    }

    // Setzen des ContentBlocks hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    $result['block_id'] = $data['block_id'];
    $result['external_id'] = $contentBlockItem['external_id'];
    $result['message'] = SoapHelper::MessageSuccess . " xt external_id:" . $contentBlockItem['external_id'];

    // Rückgabe
    return $result;
}
?>