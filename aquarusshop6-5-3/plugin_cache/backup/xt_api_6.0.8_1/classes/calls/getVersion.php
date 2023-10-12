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
 * @param type $user
 * @param type $pass
 * @return string
 */
function getVersion($user, $pass ) {
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    $versionInfos = array(); 
     
    // Versionsnummer aufgeteilt: x.x.x.x
    $versionInfos['productVersionMajor'] = 6;
    $versionInfos['productVersionMinor'] = 0;
    $versionInfos['productVersionPatchLevel'] = 0;
    $versionInfos['productVersionRevision'] = 0;
    
    // Versionsinfos rein
    $versionInfos['productName'] = "xt:Commerce API";
    $versionInfos['ShopVersion'] = _SYSTEM_VERSION; // 2017-03-03: Neu
    $versionInfos['ApiVersion'] = '6.0.0';
    
    $result['versionInfos'] = $versionInfos;

    
    // Erfolgsmeldung setzen
    $result['message'] = SoapHelper::MessageSuccess;
    $result['result'] = true;
    
    // Rückgabe 
    return $result;
}
?>