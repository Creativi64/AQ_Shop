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
 * @param type $user
 * @param type $pass
 * @param type $ProductModel Products Model
 * @param type $productExternalId External Products ID
 * @param type $customers_id Customers ID
 * @param type $customers_external_id External Customers ID
 * @param type $prices
 * @return string
 */
function setCustomerPrice($user, $pass, $ProductModel='',$productExternalId='',$customers_id='',$customers_external_id='',$prices='') {
    global $xtPlugin,$db;

    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    if (!isset($xtPlugin->active_modules['xt_customer_prices'])) {
      $result['result'] = false;
      $result['message'] = SoapHelper::MessageFail . " plugin xt_customer_prices not installed" ;

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
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " product not exists externalID:" . $productExternalId . " productsModel:" . $ProductModel . " productsID:" . $ProductItem['products_id'];

        return $result;
    }

    // check auf Kunden ID
    // external_id auflösen in customers_id
    if (strlen($customers_external_id)>0){
        $customers_id = SoapHelper::getCustomersIDByexternalID($customers_external_id);
    }

    // weder customers_id noch external_id gesetzt
    if (strlen($customers_id) == 0 || $customers_id == 0 || $customers_id == null){
        $result['message'] = SoapHelper::MessageFail . " unable to resolve customers_id";
        $result['result'] = false;
        return $result;
    }

    // customer obj bauen
    $customer = new customer($customers_id);


    if (count($customer->customer_info)==0 or !is_array($customer->customer_info)) {
        $result['message'] = SoapHelper::MessageFail . " customer_id ".$customers_id." not found";
        $result['result'] = false;
        return $result;
    }


    // CUSTOMER PRICES

    try {
        SoapHelper::processProductCustomerPrices($prices, $productID,$customers_id);      //OK
    } catch (Exception $ex) {
        $Exception.=SoapHelper::MessageFail . " Update processProductCustomerPrices: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    // Alles hat OHNE Exception funktioniert
    $result['result'] = true;

    // Erfolgsmeldung setzen
    $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt internal products id:" . $productID;

    // Rückgabe
    return $result;


}
