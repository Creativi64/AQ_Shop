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
 * Kundendaten in XT setzen
 * @global type $db
 * @global type $filter
 * @param type $user
 * @param type $pass
 * @param type $customer
 * @return string
 */
function setCustomer($user = '', $pass = '', $customer = '') {
    global $db, $filter;
    
    // Hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    if (!$hasAccess) {
        $result[0]['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }

    // Array für Ergebnismeldung
    $result = array();

    // Ist KundenEmailadresse gesetzt?
    if (!isset($customer['mail']) or $customer['mail'] == '') {
        // Keine emailadresse gesetzt, also mit Fehlermeldung raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " FAIL email address missing";
        return $result;
    }

    // Neues Objekt der Klasse check_input (=class.check.php) fürs Validieren der Kundendaten bauen
    $check = new check_input;

    // Hier die Pflichfelder prüfen
    // $customer['land'] muss mind ein Zeichen lang sein
    if (!$check->_checkLenght($customer['country_code'], 1)) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " FAIL country_code missing";
        return $result;
    }
    
    // $customer['strasse'] muss mind 5 Zeichen lang sein
    if (!$check->_checkLenght($customer['street_address'], 5)) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " FAIL street_address missing";
        return $result;
    }
    
    // $customer['shop_id'] muss gefüllt sein
    if (!isset($customer['shop_id']) or $customer['shop_id'] == '') {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " FAIL shop_id missing";
        return $result;
    }
    
    // $customer['customers_status'] muss gefüllt sein
    if ((!isset($customer['customers_status']) or $customer['customers_status'] == '') ) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " FAIL customers_status missing";
        return $result;
    }

    // Gibt es Kunde schon im shop? Anhand der Kunden Emailadresse und shopId im System suchen; Gastkunden (account_type=1) werden trotz gleicher Mail und Shop ID nicht gefunden und somit als neue Kunden angelegt
    $customers_id = SoapHelper::getCustomersIDByEmail($customer['mail'], $customer['shop_id']);
    
    // Es gibt Kunden noch NICHT
    if (false === $customers_id) {
        // Es gibt Kunden noch nicht, also type auf "new"
        $type = 'new';
    } else {
        // Es gibt Kunden schon, also type auf "update"
        $type = 'update';
    }

    try {

        // array für default address daten
        $default_address = array();
             
        $default_address['customers_gender'] = $customer['gender'];
        $default_address['customers_title'] = SoapHelper::utf8helper($customer['customers_title']);
        $default_address['customers_firstname'] = SoapHelper::utf8helper($customer['firstname']);
        $default_address['customers_lastname'] = SoapHelper::utf8helper($customer['lastname']);
        $default_address['customers_dob'] = str_replace('T', ' ', $customer['dob']);  // kommt als iso Datum an 2013-01-01T00:00:00 "T" muss weg
        $default_address['customers_company'] = SoapHelper::utf8helper($customer['company']);
        $default_address['customers_company_2'] = SoapHelper::utf8helper($customer['company_2']);
        $default_address['customers_company_3'] = SoapHelper::utf8helper($customer['company_3']);
        $default_address['customers_street_address'] = SoapHelper::utf8helper($customer['street_address']);
        $default_address['customers_address_addition'] = SoapHelper::utf8helper($customer['customers_address_addition']);
        $default_address['customers_postcode'] = $customer['postcode'];
        $default_address['customers_city'] = SoapHelper::utf8helper($customer['city']);
        $default_address['customers_suburb'] = SoapHelper::utf8helper($customer['customers_suburb']);
        $default_address['customers_federal_state_code'] = SoapHelper::utf8helper($customer['federal_state_code']); 
        $default_address['customers_country_code'] = strtoupper($customer['country_code']);
        $default_address['customers_phone'] = $customer['phone'];
        $default_address['customers_mobile_phone'] = $customer['mobile_phone'];
        $default_address['customers_fax'] = $customer['fax'];
        $default_address['PPPLUS_EMAIL'] = $customer['PPPLUS_EMAIL'];
        $default_address['address_class'] = 'default';

        // Array für Kunden Systemdaten wie status, shop id, Passwort
        $customers_info = array();
        $customers_info['customers_status'] = (int) $customer['customers_status'];
        $customers_info['customers_default_language'] = SoapHelper::utf8helper($customer['default_language']);
        $customers_info['customers_default_currency'] = SoapHelper::utf8helper($customer['default_currency']);
        $customers_info['shop_id'] = $customer['shop_id'];
        $customers_info['external_id'] = $customer['external_id'];
        $customers_info['customers_cid'] = $customer['customers_cid'];
        $customers_info['account_type'] = $customer['account_type']; // 0: normaler Account, 1: Gast-Account
        $customers_info['payment_unallowed'] = $customer['payment_unallowed'];
        $customers_info['shipping_unallowed'] = $customer['shipping_unallowed'];
        $customers_info['campaign_id'] = $customer['campaign_id'];

        // Ist ein Kundenpasswort gesetzt?
        if (isset($customer['password'])){
            $customers_info['customers_password'] = $customer['password'];
        }
        
        // customers_vat_id mappen
        $customers_info['customers_vat_id'] = $customer['vat_id'];
        // customers_email_address mappen
        $customers_info['customers_email_address'] = $customer['mail'];
        
        // Gibt es Individualfelder?
        if( is_array( $customer["indivFieldsList"]) ){
            // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
            foreach( $customer["indivFieldsList"] as $key => $indivField ){
                if( $indivField["dstTable"] == "TABLE_CUSTOMERS"){
                    $customers_info[ $indivField["sqlFieldName"] ] = SoapHelper::utf8helper( $indivField["value"] );
                }
            }
        }
    
        // Ist es ein neuer Kunde?
        if ($type == 'new') {
            // Neues customer Objekt erzeugen
            $cust = new customer;
            // Kundensystemdaten neu in DB eintragen
            $cust->_writeCustomerData($customers_info, 'insert');
            // Dabei wurde eine neue KundenID erzeugt. Diese einmappen in $default_address['customers_id']
            $default_address['customers_id'] = $cust->data_customer_id;
            
            // Kunden ID in Variable setzen. Wird später noch genutzt für Meldung
            $customers_id = $cust->data_customer_id;
            
            // Adressdaten für Kunde in DB eintragen
            $cust->_writeAddressData($default_address, 'insert');

            // Passwort nur neu erzeugen + mail, falls kein passwort übergeben wurde
            if( strlen( $customer['password'] ) == 0 ){
                $_cust = new customer($cust->data_customer_id);
                $_cust->_sendNewPassword();
            }
        } else {
            // Kunde ist schon im System vorhanden

            // Neues customer Objekt bauen
            $cust = new customer;

            // customers_id setzen
            $customers_info['customers_id'] = $customers_id;
            
            // Kundensystemdaten in System updaten
            $cust->_writeCustomerData($customers_info, 'update');

            // Kunden ID mappen
            $default_address['customers_id'] = $cust->data_customer_id;
            
            // address_book_id für Kunden aus Tabelle TABLE_CUSTOMERS_ADDRESSES abfragen
            $sql = "SELECT address_book_id FROM " . TABLE_CUSTOMERS_ADDRESSES . " WHERE customers_id='" . $customers_id . "' and (address_class='default' or address_class='') ";
            // address_book_id mappen in $default_address['address_book_id']
            $default_address['address_book_id'] = $db->getOne($sql);
            
            if( $default_address['address_book_id'] != null && $default_address['address_book_id']>0 ){
                // vorhandenen Adressdatensatz in  TABLE_CUSTOMERS_ADDRESSES updaten
                $cust->_writeAddressData($default_address, 'update');
            }
            else{
                $cust->_writeAddressData($default_address, 'insert');
            }
        }
        
         // custom fields
        if (is_array($data) && count($data)>0) {
            $db->AutoExecute(TABLE_CUSTOMERS, $data, 'UPDATE', "customers_id=".$customers_id);
        }
        
    } catch (Exception $ex) {
        // Exception gefangen, also Fehlermeldung schreiben
        $Exception.=SoapHelper::MessageFail . " Update setCustomer: Exeception Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    // Customer Objekt entfernen
    unset($cust);
    
    // Neues customer Objekt für Kunden per $customers_id bauen
    $cust = new customer($customers_id);
    
    // Ergebnismeldung bauen
    $result['customers_id'] = $customers_id;
    $result['external_id'] = $cust->customer_info['external_id'];
    $result['result'] = true;
    $result['message'] = SoapHelper::MessageSuccess . " customers_id: " . $customers_id . " customers_status: " . $cust->customers_status;
    
    // Rückgabe
    return $result;
}
?>
