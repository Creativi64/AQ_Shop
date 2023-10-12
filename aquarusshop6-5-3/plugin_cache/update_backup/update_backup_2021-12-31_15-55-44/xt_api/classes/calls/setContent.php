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

/**
 * 
 * @global type $db
 * @global type $language
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param type $contentItem
 * @return string
 */
function setContent($user = '', $pass = '', $contentItem = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }
    
    // Ist $contentItem KEIN array
    if (!is_array($contentItem)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " ContentItem no array";
        return $result;
    }
    
    // globale Variablen $db, $language nutzen
    global $db, $language, $xtPlugin;

    ($plugin_code = $xtPlugin->PluginCode('setContent.php:setContent_top')) ? eval($plugin_code) : false;

    $data = array();

    // Content ID ermitteln ist external_id gesetzt?
    if(isset($contentItem['external_id']) && strlen($contentItem['external_id']) > 0 ){

        $contentID = SoapHelper::getContentIDByexternalID($contentItem['external_id']);

        if( $contentID === false ){

            // Content in DB einfügen
            $type = 'insert';
            // anlegen in DB mit external_id
            $db->Execute("INSERT INTO " . TABLE_CONTENT . " (external_id) VALUES ('" . $contentItem['external_id'] . "')");
            // content_id setzen anhand von neuer ID aus DB
            $data['content_id'] = $db->Insert_ID();
            $contentID = $data['content_id'];
        }
        else{
            $data['content_id'] = $contentID;
            $type = 'update';
        }
    }
    else{
        // interne Content ID nutzen! Überhaupt gesetzt?
        if(  isset($contentItem['content_id']) && strlen($contentItem['content_id']) > 0 ){
            $contentID = $contentItem['content_id'];
            $data['content_id'] = $contentID;
            $type = 'update';
        }
        else{
            // ContentBlock in DB einfügen
            $type = 'insert';
            // anlegen in DB mit external_id
            $db->Execute("INSERT INTO " . TABLE_CONTENT . " (external_id) VALUES ('')");
            // content_id setzen anhand von neuer ID aus DB
            $data['content_id'] = $db->Insert_ID();
            $contentID = $data['content_id'];

        }
    }

    // Externe Parent Content ID ermitteln, ist external_parent_id gesetzt?
    if(  isset($contentItem['external_parent_id']) && strlen($contentItem['external_parent_id']) > 0 ){

        if( $contentItem['external_parent_id'] == 0 ){
            $parentID = 0;
        }
        else{

            $parentID = SoapHelper::getContentIDByexternalID($contentItem['external_parent_id']);

            if( $parentID === false ){
                $result['result'] = false;
                $result['external_parent_id'] = $contentItem['external_parent_id'];
                $result['message'] = SoapHelper::MessageFail . " content_id not found for external_parent_id: " . $contentItem['external_parent_id'];
                return $result;
            }
        }
    }
    else{
        $parentID = $contentItem['categories_parent_id'];
    }

    $data['content_parent'] = $parentID;
    $data['content_status'] = $contentItem['content_status'];
    $data['link_ssl'] = $contentItem['link_ssl'];
    $data['content_sort'] = $contentItem['content_sort'];
    $data['external_id'] = $contentItem['external_id'];

    if ($contentItem['contentDescriptionsList'] && sizeof($contentItem['contentDescriptionsList']) > 0) {
        // über alle einzelnen descritionItems laufen
        foreach ($contentItem['contentDescriptionsList'] as $contentDescriptionsItem) {
            reorgLangFieldStores($contentDescriptionsItem, "content_title", $data, "content_title", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "content_heading", $data, "content_heading", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "content_body", $data, "content_body", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "content_body_short", $data, "content_body_short", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "seo_url", $data, "url_text", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "meta_title", $data, "meta_title", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "meta_keywords", $data, "meta_keywords", $contentDescriptionsItem['content_store_id']);
            reorgLangFieldStores($contentDescriptionsItem, "meta_description", $data, "meta_description", $contentDescriptionsItem['content_store_id']);
        }

        // Liste raus aus struktur
        unset($contentItem['productDescriptionsList']);
    }

    // Permissions in $data mappen, damit sie per Object $category in db gemappt werden
    // Aufbau zb $date["shop_1"] = "1"
    if (is_array($contentItem["permissionList"])) {
        // Über permission Items laufen
        foreach ($contentItem["permissionList"] as $key => $pItem) {
            // in $data mappen
            $data[$pItem["pgroup"]] = "1";
        }
    }

    $blockIds = array();
    if(  isset($contentItem['blockList']) && strlen($contentItem['blockList']) > 0 ) {
        $blockIds = $contentItem['blockList'];
    }

    if(  isset($contentItem['blockListExternal']) && count($contentItem['blockListExternal']) > 0 ) {
        foreach ($contentItem['blockListExternal'] as $blockexternalId) {
            $blockId = SoapHelper::getContentBlockIDByexternalID($blockexternalId);
            $blockIds[] = $blockId;
        }
    }

    // content block relation
    foreach ($blockIds as $contentBlockId) {
            $data['block_' . $contentBlockId] = '1';
    }


    // Neues Objekt stdClass anlegen
    $obj = new stdClass;
    // Neues content Obejkt anlegen
    $content = new content;
    $content->store_field_exists = true;

    $obj = $content->_set($data);

    ($plugin_code = $xtPlugin->PluginCode('setContent.php:setContent_bottom')) ? eval($plugin_code) : false;


    // Hat das setzen des Contents funktioniert?
    if ($obj->success) {
        // Erfolgsmeldung setzen
        $result['result'] = true;
        $result['content_id'] = $data['content_id'];
        $result['external_id'] = $contentItem['external_id'];
        $result['message'] = SoapHelper::MessageSuccess . " external_id:" . $contentItem['external_id'] ;
        // Rückgabe mit Erfolgsmeldung
        return $result;
    } else {
        // Setzen des Contents hat NICHT funktioniert, also Fehlermeldung schreiben
        $Exception.="Failed on external_id " . $contentItem['external_id'];
    }

    // Ist eine Fehlermeldung gesetzt worden in $Exception
    if (strlen($Exception) > 0) {
        // Fehlermeldung in $result setzen
        $result['result'] = false;
        $result['external_id'] = $contentItem['external_id'];
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception;
        // Raus mit Fehlermeldung
        return $result;
    }

    // Setzen des Contents hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    $result['content_id'] = $data['content_id'];
    $result['external_id'] = $contentItem['external_id'];
    $result['message'] = SoapHelper::MessageSuccess . " external_id:" . $contentItem['external_id'];

    // Rückgabe
    return $result;
}
?>
