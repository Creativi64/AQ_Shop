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

// Include einer externen php Datei
include_once _SRV_WEBROOT . 'xtFramework/admin/classes/class.adminDB_DataSave.php';

/**
 * 
 * @global type $db
 * @global type $language
 * @param type $user
 * @param type $pass
 * @param type $mediaItemXT
 * @return string
 */
function setMediaXT($user = '', $pass = '', $mediaItemXT = '') {

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Ist $mediaItemXT KEIN array
    if (!is_array($mediaItemXT)) {
        // Mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " mediaItemXT is no array";
        return $result;
    }
    
    
    // Dateipfad auf Ordner für Original-Grafiken (=unskaliert) bauen
    // $dstFolder = _SRV_WEBROOT . _SRV_WEB_IMAGES . 'org/';
    $dstFolder = _SRV_WEBROOT._SRV_WEB_MEDIA_FILES;

    // Wenn $imageFolder ein Ordner und beschreibbar ist
    if (is_dir($dstFolder) && is_writable($dstFolder)) {

        // Garfikdaten ( Base64) in Datei schreiben in imageFolder mit Dateinamen $name
        file_put_contents($dstFolder . $mediaItemXT["file"], base64_decode($mediaItemXT["filedata"]) );
    }
    else{
        // FEHLERMLEDUNG UND RAUS
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " folder: ". $dstFolder. " is no folder or not writeable";
        return $result;
    }

    // globale Variablen $db, $language nutzen
    global $db, $language;

    $mode = "insert"; // oder update
    
    $id = 0;   

    if ( $mediaItemXT['id'] > 0 ) {
        $mode = "update";
        $id = $mediaItemXT['id'];
    }
    else{ 
        if ( strlen($mediaItemXT['external_id'] ) > 0 ) {
                $id = SoapHelper::getMediaIDByexternalID($mediaItemXT['external_id']);

                if($id === false ){
                    $mode = "insert";
                    $id = 0;
                }
                else{
                    $mode = "update";
                }
        } 
        else{
            $mode = "insert";
            $id = 0;   
        }
    }

     
    // Merke: getMediaIDByexternalID durchführen um festzustellen ob INSERT oder UPDAT
    // falls wir festellen dass "update", dann prüfen, ob dateiname in DB = Dateiname in call
    // wenn Dateinamen unterschiedlich!!! Dann per SQL Dateinamen in DB ändern und alte Datei  im Filesystem löschen

     if( $mode == "update" ){
        
        // Hat sich Dateiname geändert? Dann löschen in org
        // Aktuellen Dateinamen aus DB ziehen
        $sql = "SELECT file FROM " . TABLE_MEDIA . " WHERE id=" . $id ;
         
        $oldFile = $db->getOne($sql);
         
        if( $oldFile != $mediaItemXT["file"] ){
            // Dateiname geändert!
            
            // hier Datei löschen!
            deleteMediaFile( $oldFile );
             
            $db->Execute("UPDATE " . TABLE_MEDIA . " SET file='" . $mediaItemXT["file"] . "' WHERE id=".$id);    
        }
    }

    // Hier die Mehrsprachenfelder umsetzen in die XT Struktur - > einmappen in $datat und dann alte Sprachenstruktur entfernen!
    reorgLangField($mediaItemXT, "media_name", "media_name");
    reorgLangField($mediaItemXT, "media_description", "media_description");                     
     
    if( $mode == 'update'){
       $mediaItemXT["id"] = $id;    
    }
    else{
       // mode = insert
        unset( $mediaItemXT["id"] );
    }

    $md = new MediaData();   
    
    // Type anhand von filenamen setzen lassen
    $mediaItemXT["type"] =  $md->_getFileTypesByExtension($mediaItemXT["file"]);
     
    
    // Hack damit setMediaData funktioniert
    $mediaItemXT["language_code"] = true;
    
    
    // core klasse arbeitet nicht gut
    // code aus MediaData.setMediaData und angepasst
    // $m_id = $md->setMediaData( $mediaItemXT );
            
    // if download_status is empty then problems with product images will occur
    if(!$mediaItemXT['download_status']) {
        $mediaItemXT['download_status'] = 'free';
    }
	    
    // Update oder insert?
    if( $mode == 'update'){
        // UPDATE
        $oMD = new adminDB_DataSave(TABLE_MEDIA , $mediaItemXT);
        
        $objMD = $oMD->saveDataSet();

	$oMDD = new adminDB_DataSave(TABLE_MEDIA_DESCRIPTION, $mediaItemXT, true);
	$objMDD = $oMDD->saveDataSet();

	$md->_setMediaGallery($data, $mediaItemXT["id"]);      
    }
    else{
        // INSERT
        $oMD = new adminDB_DataSave(TABLE_MEDIA , $mediaItemXT);
        $objMD = $oMD->saveDataSet();
	$m_id = $objMD->new_id;
		
        $mediaItemXT["id"] = $m_id; 

	$oMDD = new adminDB_DataSave(TABLE_MEDIA_DESCRIPTION, $mediaItemXT, true);
	$objMDD = $oMDD->saveDataSet();
	    
	$md->_setMediaGallery($mediaItemXT, $m_id);
    }
    
    
    //
    // MEDIA LANGUAGES
    // Alle media languages für dieses item löschen
    $db->Execute("DELETE FROM " . TABLE_MEDIA_LANGUAGES . " WHERE m_id = '" . $mediaItemXT["id"] . "'");
    
    
    foreach ($mediaItemXT["media_languages"] as $key => $val) {
        $db->Execute("INSERT INTO " . TABLE_MEDIA_LANGUAGES . "( m_id,language_code ) VALUES('" . $mediaItemXT["id"] . "','" . $val . "')");
    }

    // Kundengruppen Permissions für MediaItem schreiben
    try {

        $med_perm_array =array(
            'group_perm' => array(
                'type'=>'group_permission',
                'key'=>'id',
                'value_type'=>'media_file',
                'pref'=>'m'              
            )
        );

        $med_permission = new item_permission($med_perm_array);

        // Neues array $_item anlegen
        $_item = array();
        // ID setzen
        $_item['id'] = $mediaItemXT["id"];
        
        if (is_array($mediaItemXT['permissionList'])) {
            // Über permission Items laufen
            foreach( $mediaItemXT["permissionList"] as $key => $pItem ){
                // in $data mappen
                $_item[ $pItem["pgroup"] ] = 1;
            }
        }
        
        // In $set_perm Methode setPosition auf "admin" setzen
        $med_permission->setPosition('admin');
        

        // Permissiondaten für dieses Produkt in DB speichern
        $med_permission->_saveData($_item, $mediaItemXT["id"]);
        
               
    } catch (Exception $ex) {
        // Exception gefangen, also Fehlermeldung schreiben
        $Exception.=SoapHelper::MessageFail . " Update Permissions: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    // Setzen des MediaItem hat funktioniert, also Erfolgsmeldung setzen
    $result['result'] = true;
    $result['id'] = $mediaItemXT["id"];
    $result['file'] = $mediaItemXT['file'];
    $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt external_id:" . $mediaItemXT['external_id'];

    // Rückgabe
    return $result;
}
?>