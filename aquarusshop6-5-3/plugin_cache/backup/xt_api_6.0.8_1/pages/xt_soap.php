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




/*
 * Mit dem hier schalten wir den ganzen Shopoutput aus
 */
$display_output = false;


include _SRV_WEBROOT.'plugins/xt_api/conf/conf.inc.php';


include_once _SRV_WEBROOT.'plugins/xt_api/classes/class.check.php';
include_once _SRV_WEBROOT.'plugins/xt_api/classes/SoapController.class.php';
include_once _SRV_WEBROOT.'plugins/xt_api/classes/XsdTypeProcessor.class.php';
include_once _SRV_WEBROOT.'plugins/xt_api/classes/SoapTypes.class.php';
include_once _SRV_WEBROOT.'plugins/xt_api/classes/SoapMappings.class.php';
include_once _SRV_WEBROOT.'plugins/xt_api/classes/SoapHelper.php';

include_once _SRV_WEBROOT.'xtFramework/classes/class.image.php';
include_once _SRV_WEBROOT.'xtFramework/classes/class.MediaData.php';
include_once _SRV_WEBROOT.'xtFramework/classes/class.MediaImages.php';

//2017-02-06
include_once _SRV_WEBROOT.'xtFramework/classes/class.uploader.php';
include_once _SRV_WEBROOT.'xtFramework/admin/classes/class.adminDB_DataSave.php';
include_once _SRV_WEBROOT.'xtFramework/classes/class.product_price.php';


foreach (glob(_SRV_WEBROOT."plugins/xt_api/classes/calls/*.php") as $filename)
{
    include_once $filename;
}

($plugin_code = $xtPlugin->PluginCode('xt_soap.php:top')) ? eval($plugin_code) : false;

if(isset($_GET['def_table'])){ SoapHelper::SoapTypeGenerator($_GET['def_table']); }
if(isset($_GET['genarateTest'])){ SoapHelper::genarateInputFields($_GET['genarateTest']); }

define('SERVICE_NAMESPACE' , 'http://www.xt-commerce.com/services#xtconnect');
define('SERVICE_NAME', 'xtCommerce API');


$tmp_link  = $xtLink->_link(array('page'=>'xt_soap'));



// Soll JSON anstatt XML und NuSOAP genutzt werden?

if( XT_API_USE_JSON =='true'){
    return processJSON();

} else {

    $oController = new SoapController(SERVICE_NAME, SERVICE_NAMESPACE,$tmp_link);
    $oController->Init();

    $oController->addTypes(SoapTypes::getTypesArray());
    $oController->bindMappings(SoapMappings::getMappings());


    // Custom Mode
    if(isset($_GET['CUSTOM_MODE'])){

        include_once _SRV_WEBROOT.'plugins/xt_api/classes/custom/CustomSoapTypes.class.php';
        include_once _SRV_WEBROOT.'plugins/xt_api/classes/custom/CustomSoapMappings.class.php';

        $oController->addTypes(CustomSoapTypes::getTypesArray());
        $oController->bindMappings(CustomSoapMappings::getMappings());
    }


    $post_data = file_get_contents("php://input");
    $oController->process($post_data);


}




//
//  JSON Datenformart anstatt von XML nutzen
//
function processJSON(){
    global $xtPlugin;

    $post_data = file_get_contents("php://input");
    ($plugin_code = $xtPlugin->PluginCode('xt:soap.php:processJSON_top')) ? eval($plugin_code) : false;
    
    // JSON Anfrage parsen
    $jsonReq = json_decode($post_data, true);

    if (json_last_error() != JSON_ERROR_NONE) {
        $result['result'] = false;
        $result['message'] = "invalid JSON";
        print json_encode($result);
        return;
    }

    SoapHelper::setRequest($jsonReq,'json');
    
    // Funktion nicht gesetzt
    if(!isset($jsonReq["function"]) or strlen($jsonReq["function"])==0){
        $result['result'] = false;
        $result['message'] = "No function name found in JSON request!";
        print json_encode($result);
        return;
    }

    $jsonFunc = $jsonReq["function"];


    // ist funktion auch gemappt in SoapMappings? Sonst ablehnen!
    $soapMap = SoapMappings::getMappings();

    if( ! is_array( $soapMap[$jsonFunc] ) ){
        $result['result'] = false;
        $result['message'] = "function is not allowed.";
        print json_encode($result);
        return;
        
    }

    // Funktion und Paramter aufrufen
    try {
        $resp = call_user_func_array( $jsonReq["function"], $jsonReq["paras"]);
    } catch (ArgumentCountError $e) {
        $result['result'] = false;
        $result['message'] = 'Too few arguments to function '.$jsonReq["function"];
        print json_encode($result);
        return;
    }

    
    // Header setzen  ;charset=utf-8 
    header('content-type: application/json;charset='.XT_API_CHARSET, true);

    if (isset($resp['http_response_code'])) {
        http_response_code($resp['http_response_code']);
        unset($resp['http_response_code']);
    }
    
    // JSON Ausgabe: json_encode gibt immer UTF-8 aus
    $resp=json_encode($resp);
    SoapHelper::setResponse($resp);
    SoapHelper::logRequest($jsonFunc);

    print $resp;
    
}