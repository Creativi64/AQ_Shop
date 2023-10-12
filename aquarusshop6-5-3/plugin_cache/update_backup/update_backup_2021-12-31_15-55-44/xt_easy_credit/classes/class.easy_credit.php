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

class easy_credit
{
    const REST_API_VERSION = 'v1';
    protected $REST_CLIENT_ID;
    protected $REST_SECRET_KEY;
    protected $REST_API_ENDPOINT = '';

    static protected $_financing_range = array('min' => 200, 'max'=> 10000);

    function __construct()
    {
        global $xtLink;

        $this->REST_CLIENT_ID       = trim(XT_EASY_CREDIT_CLIENT_ID);
        $this->REST_SECRET_KEY      = trim(XT_EASY_CREDIT_SECRET_KEY);

        $this->REST_API_ENDPOINT    = "https://ratenkauf.easycredit.de/ratenkauf-ws/rest/";

        $restUrl = $this->REST_API_ENDPOINT .self::REST_API_VERSION;

        $this->REST_FINANCING_OPTIONS_URL   = $restUrl. '/modellrechnung/durchfuehren';
        $this->REST_VORGANG_INIT_URL		= $restUrl. '/vorgang';
        $this->REST_VORGANG_DECISION		= $restUrl. '/vorgang/{vorgangskennung}/entscheidung';
        $this->REST_VORGANG		            = $restUrl. '/vorgang/{vorgangskennung}';
        $this->REST_VORGANG_FINANCING		= $restUrl. '/vorgang/{vorgangskennung}/finanzierung';
        $this->REST_VORGANG_CONFIRM 		= $restUrl. '/vorgang/{vorgangskennung}/bestaetigen';
        $this->REST_TEXT 		            = $restUrl. '/texte';
        $this->REST_RESTBETRAG 		        = $restUrl. '/webshop/'.$this->REST_CLIENT_ID.'/restbetragankaufobergrenze';

        $this->INSTALLMENTS_URL		        = 'https://ratenkauf.easycredit.de/ratenkauf/content/intern/einstieg.jsf?vorgangskennung=';

        $this->RETURN_REST_VORGANG_INIT_URL  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'confirmation', 'conn'=>'SSL', 'params'=>'easy_credit_installments=true&'.session_name().'='.session_id()));
        $this->CANCEL_REST_VORGANG_INIT_URL  = $xtLink->_link(array('page'=>'checkout', 'paction'=>'payment', 'conn'=>'SSL'));

        $this->EASY_CREDIT_partnerID = "1.de.6241";
        $this->_module_version = "1.0.0";
    }

    public function getClientID()
    {
        return $this->REST_CLIENT_ID;
    }

    function easy_credit_initVorgang($amount, $logistikDienstleister = '')
    {
        global $order, $language,$xtPlugin, $currency, $store_handler, $db;

        unset($_SESSION['easy_credit']);

        $returnURL =$this->RETURN_REST_VORGANG_INIT_URL;
        $cancelURL =$this->CANCEL_REST_VORGANG_INIT_URL;

        $custInitData = $this->getCustomerInitData();
        $cartInitData = $this->getCartInitData();


        $dob = null;
        try
        {
            $dob = DateTime::createFromFormat('d.m.Y', $_SESSION['customer']->customer_payment_address['customers_dob']);
            $dob = $dob->format('Y-m-d');
        }
        catch(Exception $e)
        {
            error_log('xt_easy_credit - init - could not set dob with given value ['.$_SESSION['customer']->customer_payment_address['customers_dob'].']');
            error_log($e->getMessage());
        }

        $data = array(

            "shopKennung" =>  $this->REST_CLIENT_ID,
            "technischeShopparameter" => array
            (
                "shopSystemHersteller" => 'xtCommerce 5',
                "shopSystemModulversion" => $this->_module_version,
            ),
            "integrationsart" =>  'PAYMENT_PAGE',
            "bestellwert" =>  $cartInitData['total'],
            //"laufzeit" =>  18,
            "personendaten" => array
            (
                "anrede" =>  $custInitData->anrede,
                "vorname" =>  $_SESSION['customer']->customer_payment_address['customers_firstname'],
                "nachname" =>  $_SESSION['customer']->customer_payment_address['customers_lastname'],
                "geburtsdatum" =>  $dob
            ),
            "rechnungsadresse" => array
            (
                "strasseHausNr" =>  $_SESSION['customer']->customer_payment_address['customers_street_address'],
                "adresszusatz" =>  null,
                "plz" =>  $_SESSION['customer']->customer_payment_address['customers_postcode'],
                "ort" =>  $_SESSION['customer']->customer_payment_address['customers_city'],
                "land" =>  $_SESSION['customer']->customer_payment_address['customers_country_code']
            ),
            "lieferAdresse" =>  array
            (
                "vorname" =>  $_SESSION['customer']->customer_shipping_address['customers_firstname'],
                "nachname" =>  $_SESSION['customer']->customer_shipping_address['customers_lastname'],
                "strasseHausNr" =>  $_SESSION['customer']->customer_shipping_address['customers_street_address'],
                "adresszusatz" =>  null,
                "plz" =>  $_SESSION['customer']->customer_shipping_address['customers_postcode'],
                "ort" =>  $_SESSION['customer']->customer_shipping_address['customers_city'],
                "land" =>  $_SESSION['customer']->customer_shipping_address['customers_country_code']
            ),
            /*
            "beschaeftigungsdaten" => array
            (
                'beschaeftigung' => 'SONSTIGES',
                'monatlichesNettoeinkommen' => 2500
            ),
            */
            "kontakt" => array
            (
                "email" =>  $_SESSION['customer']->customer_info['customers_email_address']
            ),
            "bankdatenInput" =>  null,
            "weitereKaeuferangaben" => array
            (
                "telefonnummer" =>  $_SESSION['customer']->customer_payment_address['customers_phone'] ? $_SESSION['customer']->customer_payment_address['customers_phone'] : null,
                "titel" =>  null,
                "geburtsname" =>  null,
                "geburtsort" =>  null
            ),
            "risikorelevanteAngaben" =>  array(
                "kundenstatus" => $custInitData->kundenstatus,
                "kundeSeit" => date('Y-m-d', $custInitData->kundeSeit),
                "bestellungErfolgtUeberLogin" => $custInitData->bestellungErfolgtUeberLogin,
                "anzahlProdukteImWarenkorb" => $custInitData->anzahlProdukteImWarenkorb,
                "anzahlBestellungen" => $custInitData->anzahlBestellungen,
                "negativeZahlungsinformation" => $custInitData->negativeZahlungsinformation,
                //"risikoartikelImWarenkorb" => $custInitData->risikoartikelImWarenkorb,
                "logistikDienstleister" => $logistikDienstleister
            ),
            "vorgangskennungShop" =>  $_SESSION['customer']->customers_id.'_'.time(),

            "ruecksprungadressen" => array
            (
                "urlErfolg" =>  $returnURL,
                "urlAbbruch" =>  $cancelURL,
                "urlAblehnung" =>  $cancelURL.'?declined=true'
            ),
            "warenkorbinfos" =>  $cartInitData['warenkorbinfos']
        );

        $result = $this->ApiJSONCall($data, $this->REST_VORGANG_INIT_URL, 'initVorgang');
        $resArray = json_decode($result,true);

        $ret = new stdClass();
        $ret->error = false;

        if(is_array($resArray) && !empty($resArray['tbVorgangskennung'])){
            // Redirect to easy_credit.com here
            $token = urldecode($resArray["tbVorgangskennung"]);
            $resArray['timestamp'] = time();
            $_SESSION['easy_credit'] = $resArray;
            $ret->url = $this->INSTALLMENTS_URL.$token;
            return $ret;
        }
        else
        {
            unset($_SESSION['easy_credit']);
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_initVorgang';
            $oID = $this->orders_id;
            if (!isset($this->orders_id)) $oID = 0;
            $log_data['orders_id'] = $oID;
            $msg = 'Unbekannter Fehler / Unknown error';
            if(is_array($resArray) && count($resArray['wsMessages']['messages']))
            {
                $msg = $resArray['wsMessages']['messages'][0]['renderedMessage']
                .'#'.$resArray['wsMessages']['messages'][0]['fehlerschluessel']
                    .'#'.$resArray['wsMessages']['messages'][0]['infoFuerEntwickler']
                    .'#'.$resArray['wsMessages']['messages'][0]['details']

                ;
            }
            $log_data['error_msg'] = $msg;
            $log_data['error_data'] = array( 'response' => $result, 'request' => $data);
            $this->_addCallbackLog($log_data);

            $ret->error = true;
            $errors = array();
            if(is_array($resArray['wsMessages']['messages']))
            {
                foreach ($resArray['wsMessages']['messages'] as $msg)
                {
                    if($msg['severity'] == 'INFO') continue;
                    $t = $msg['infoFuerBenutzer'] ? $msg['infoFuerBenutzer'] : $msg['renderedMessage'];
                    if(!empty($msg['field'])) $t .= ' #'.$msg['field'];
                    $errors[] = array(
                        'key' => $msg['key'],
                        'msg' => $t);
                }
            }
            $ret->errors = $errors;

            return $ret;
        }
    }

    private function getCustomerInitData()
    {
        global $db;

        $ret = new stdClass();

        $ret->anrede =
            $_SESSION['customer']->customer_payment_address['customers_gender'] == 'm' ? 'HERR' : 'FRAU';

        $orderCount = $db->GetOne("SELECT COUNT(orders_id) FROM ".TABLE_ORDERS." where customers_id = ?", array($_SESSION['customer']->customers_id));
        $hasLogin = !empty($_SESSION['registered_customer']);

        $ret->kundenstatus = $orderCount < 2 || !$hasLogin ? 'NEUKUNDE' : 'BESTANDSKUNDE';

        $ret->kundeSeit = $_SESSION['customer']->customer_info['date_added'];

        $ret->bestellungErfolgtUeberLogin = $hasLogin;

        $ret->anzahlProdukteImWarenkorb = count($_SESSION['cart']->show_content);

        $ret->anzahlBestellungen = $orderCount;

        $ret->negativeZahlungsinformation = 'KEINE_INFORMATION';

        $ret->risikoartikelImWarenkorb = false;

        return $ret;
    }

    private function getCartInitData()
    {
        global $db, $store_handler, $language;

        $lang = false;
        $langList = $language->_getLanguageList('store');
        foreach ($langList as $l)
        {
            if ($l['code'] == 'de')
            {
                $lang = $l['code'];
                break;
            }
        }
        if(empty($lang))
        {
            $lang = $language->default_language;
        }

        $ret = array();

        $warenkorbInfo = array();
        $total = 0;

        foreach ($_SESSION['cart']->show_content as $sc)
        {
            $sc_price = round($sc['products_price']['plain'],2);
            if($sc_price == 0) continue;

            // artikelnr
            $artikelnummern = array(
                array(
                    "nummerntyp" => 'ARTIKEL_NUMMER',
                    "nummer" => $sc['products_model'])
            );
            if(!empty($sc['products_ean']))
            {
                $artikelnummern[] = array(
                    "nummerntyp" => 'EAN',
                    "nummer" => $sc['products_ean']);
            }
            // kategorie
            $cs = $db->GetArray("SELECT DISTINCT (cd.categories_id), cd.categories_name FROM ".TABLE_PRODUCTS_TO_CATEGORIES." p2c
					    
					    LEFT JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd ON cd.categories_id = p2c.categories_id
					    WHERE p2c.products_id=? AND cd.categories_store_id=? and cd.language_code = ? order by master_link DESC", array($sc['products_id'], $store_handler->shop_id, $lang));

            $kategorie = null;
            if(count($cs))
            {
                $kategorie = $cs[0]['categories_name'];
            }

            $manu = null;
            if($sc['manufacturers_id'])
            {
                $manu = $db->GetOne("SELECT manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id=?", array($sc['manufacturers_id']));
                if(empty($manu)) $manu = null;
            }

            $total += $sc_price * $sc['products_quantity'];

            $warenkorbInfo[] = array(
                "produktbezeichnung" => $sc['products_name'],
                "menge" => $sc['products_quantity'],
                "preis" => $sc_price,
                "hersteller" => $manu,
                "produktkategorie" => $kategorie,
                "artikelnummern" => $artikelnummern
            );
        }

        $_SESSION['cart']->_refresh();

        foreach ($_SESSION['cart']->show_sub_content as $k => $sc)
        {
            $sc_price = round($sc['products_price']['plain'],2);
            if($sc_price == 0) continue;

            // artikelnr
            $artikelnummern = array(
                array(
                    "nummerntyp" => 'ARTIKEL_NUMMER',
                    "nummer" => $k)
            );


            $total += $sc_price * $sc['products_quantity'];

            $warenkorbInfo[] = array(
                "produktbezeichnung" => $sc['products_name'],
                "menge" => $sc['products_quantity'],
                "preis" => $sc_price,
                "hersteller" => 'XT',
                "produktkategorie" => $sc['products_key'],
                "artikelnummern" => $artikelnummern
            );
        }

        $ret['total'] = $total;
        $ret['warenkorbinfos'] = $warenkorbInfo;
        return $ret;
    }

    function getTextZustimmung()
    {
        $url = $this->REST_TEXT.'/zustimmung/'.$this->REST_CLIENT_ID;

        $result = $this->ApiJSONCall(array(), $url, 'get_text_zustimmung', 'GET');
        $resArray = json_decode($result,true);
        //error_log(print_r($resArray,true));

        if(is_array($resArray) && !empty($resArray['zustimmungDatenuebertragungPaymentPage']))
        {
            return $resArray;
        }
        else
        {
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_getDecision';
            $log_data['orders_id'] = 0;
            $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
            $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
            $this->_addCallbackLog($log_data);

            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }
    }

    function getRestbetrag()
    {
        $url = $this->REST_RESTBETRAG;

        $result = $this->ApiJSONCall(array(), $url, 'get_restbetrag', 'GET');
        $resArray = json_decode($result,true);
        //error_log(print_r($resArray,true));

        if(is_array($resArray) && !empty($resArray['restbetrag']))
        {
            return $resArray['restbetrag'];
        }
        else
        {
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_getDecision';
            $log_data['orders_id'] = 0;
            $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
            $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
            $this->_addCallbackLog($log_data);

            return false;
        }
    }

    function getDecision()
    {
        if (empty($_SESSION['easy_credit']['tbVorgangskennung']))
        {
            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }
        $url = str_replace('{vorgangskennung}', $_SESSION['easy_credit']['tbVorgangskennung'], $this->REST_VORGANG_DECISION);

        $result = $this->ApiJSONCall(array(), $url, 'get_decision', 'GET');
        $resArray = json_decode($result,true);
        //error_log(print_r($resArray,true));

        if(is_array($resArray) && !empty($resArray['entscheidung']['entscheidungsergebnis']))
        {
            $decision = $resArray['entscheidung']['entscheidungsergebnis'];
            return $resArray;
        }
        else
        {
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_getDecision';
            $log_data['orders_id'] = 0;
            $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
            $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
            $this->_addCallbackLog($log_data);

            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }
    }

    function getVorgang()
    {
        if (empty($_SESSION['easy_credit']['tbVorgangskennung']))
        {
            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }
        $url = str_replace('{vorgangskennung}', $_SESSION['easy_credit']['tbVorgangskennung'], $this->REST_VORGANG);
        $result = $this->ApiJSONCall(array(), $url, 'error_getVorgang', 'GET');
        $resArray = json_decode($result,true);

        //error_log(print_r($resArray,true));

        if(is_array($resArray))
        {
            return $resArray;
        }
        else
        {
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_getVorgang';
            $log_data['orders_id'] = 0;
            $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
            $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
            $this->_addCallbackLog($log_data);

            return false;
        }
    }

    function getFinancingInfo()
    {
        if (empty($_SESSION['easy_credit']['tbVorgangskennung']))
        {
            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }
        $url = str_replace('{vorgangskennung}', $_SESSION['easy_credit']['tbVorgangskennung'], $this->REST_VORGANG_FINANCING);
        $result = $this->ApiJSONCall(array(), $url, 'error_getFinancingInfo', 'GET');
        $resArray = json_decode($result,true);

        //error_log(print_r($resArray,true));

        if(is_array($resArray))
        {
            return $resArray;
        }
        else
        {
            $log_data = array();
            $log_data['module'] = 'xt_easy_credit';
            $log_data['class'] = 'error_getFinancingInfo';
            $log_data['orders_id'] = 0;
            $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
            $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
            $this->_addCallbackLog($log_data);

            return false;
        }
    }


    function confirm($type = '')
    {
        global $order,$filter,$db, $currency;

        if (empty($_SESSION['easy_credit']['tbVorgangskennung']))
        {
            return array('decision' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL);
        }

        $url = str_replace('{vorgangskennung}', $_SESSION['easy_credit']['tbVorgangskennung'], $this->REST_VORGANG_CONFIRM);
        $result = $this->ApiJSONCall(array(), $url, 'confirm', 'POST');
        $resArray = json_decode($result,true);
        //error_log(print_r($resArray,true));

        if(is_array($resArray) && !empty($resArray['wsMessages']['messages'][0]))
        {
            foreach($resArray['wsMessages']['messages'] as $msg)
            {
                if($msg['key'] == 'BestellungBestaetigenServiceActivity.Infos.ERFOLGREICH')
                {
                    return array('confirmed' => true);;
                }
            }
            $resArray['wsMessages']['messages'][0]['key'];
        }

        $log_data = array();
        $log_data['module'] = 'xt_easy_credit';
        $log_data['class'] = 'error_confirm';
        $log_data['orders_id'] = $_SESSION['last_order_id'] ? $_SESSION['last_order_id'] : 0;
        $log_data['error_msg'] = is_array($resArray) && count($resArray['wsMessages']['messages'] && $resArray['wsMessages']['messages'][0]['renderedMessage']) ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Unknown error';
        $log_data['error_data'] = array('vorgangskennung' => $_SESSION['easy_credit']['vorgangskennung'], 'result' => $result);;
        $this->_addCallbackLog($log_data);

        $msg = $resArray['wsMessages']['messages'] && $resArray['wsMessages']['messages'][0]['renderedMessage'] ? $resArray['wsMessages']['messages'][0]['renderedMessage'] : 'Leider ist ein Fehler aufgetreten. Bitte wählen Sie eine andere zahlungsweise';
        return array('confirmed' => false, 'url' => $this->CANCEL_REST_VORGANG_INIT_URL, 'msg' => $msg);
    }


    /**
     *      REST access
     *
     */
    /*CheckAccessTokenLivetime
	* Checks if access token is still alive
	* @params $generate_new_session - whether to generate new session is expired
	* @return boolean
	*/
    function getFinancingOptions($product_price, $currency, $country)
    {
        global $language;
        $data = array(
            'webshopId' => $this->REST_CLIENT_ID,
            'finanzierungsbetrag' => $product_price
            //'tracking_id' => 'foo',
        );
        $result = $this->ApiJSONCall($data, $this->REST_FINANCING_OPTIONS_URL,'get_financing_options', 'GET');
        if ($result)
        {
            $financingOptions = json_decode($result,true);
            $financingOptions['financing_options']['financing_value'] = $product_price;

            $dec_point = ',';
            $thousands_sep = '.';
            if($language->code == 'en')
            {
                $dec_point = '.';
                $thousands_sep = ',';
            }
            array_walk_recursive ( $financingOptions ,array('xt_easy_credit','ec_installments_format_array'), array('dec_point' => $dec_point , 'thousands_sep' => $thousands_sep) );

            return $financingOptions;
        }
        return false;
    }


    /**
     * @param cart_amount array of options or value of cart/product
     * @param $position  cart or product
     * @param bool|false $html_element  'link' or 'popup'
     * @return string    created html, may be empty
     */
    public static function buildInfoButton($financingOptions, $position, $html_element = false)
    {
        $html = '';
        $subDir = '';

        if($position=='cart')
        {
            $financing_what = TEXT_CART;
        }
        else {
            $financing_what = TEXT_ARTICLE;
        }

        global $xtLink, $price;
        if($html_element == 'link')  // link only, no example, no data; popup body data loaded via ajax; see $html_element == 'popup'
        {
            $tplFile = 'easy_credit_widget_button_ajax.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
            $url = $xtLink->_link(array('page' => 'xt_easy_credit'));
            $options_url = $xtLink->_link(array('page' => 'xt_easy_credit', 'params' => 'easy_credit_action=getFinancingOptions&value='.$financingOptions['value']));
            $tpl_data = array(
                'url' => $url,
                'options_url' => $options_url,
                'css_class' => 'installments-' . $position,
                'example' => $financingOptions['repraesentativesBeispiel'],
                'financing_what' => $financing_what,
                'financing_value' => $financingOptions['financing_options'][0]['financing_value'],
                'value' => $financingOptions['value'],
                'provider' => _STORE_SHOPOWNER_COMPANY . ', ' . _STORE_SHOPOWNER_STREETADDRESS . ', ' . _STORE_SHOPOWNER_ZIP . ' ' . _STORE_SHOPOWNER_CITY
            );
            $html = $template->getTemplate('', $tplFile, $tpl_data);

            $tplFile = 'easy_credit_widget_modal.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
            $html .= $template->getTemplate('', $tplFile, $tpl_data);
        }
        else if ($financingOptions && is_array($financingOptions) && is_array($financingOptions['ergebnis']))
        {
            // we have an valid financing option array
            if(is_array($financingOptions['ergebnis'])
                && count($financingOptions['ergebnis']))
            {
                while (count($financingOptions['ergebnis']) > 6)
                {
                    array_shift($financingOptions['ergebnis']);
                }
                $financingOptions['ergebnis'] = array_reverse($financingOptions['ergebnis']);

                // we have at least one qualifying option
                $html = '';
                $tpl_data = array(
                    'example' => $financingOptions['repraesentativesBeispiel'],
                    'qualifying_options' => $financingOptions['ergebnis'],
                    'non_qualifying_options' => $financingOptions['financing_options'][0]['non_qualifying_financing_options'],
                    'options' => $financingOptions['ergebnis'],
                    'css_class' => 'installments-' . $position,
                    'financing_what' => $financing_what,
                    'financing_value' => $financingOptions['financing_options'][0]['financing_value'],
                    'provider' => _STORE_SHOPOWNER_COMPANY . ', ' . _STORE_SHOPOWNER_STREETADDRESS . ', ' . _STORE_SHOPOWNER_ZIP . ' ' . _STORE_SHOPOWNER_CITY
                );

                if($html_element == 'popup')
                {
                    // ajax call for popup data
                    $tplFile = 'easy_credit_widget_modal_body.tpl.html';
                    $template = new Template();
                    $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
                    $html .= $template->getTemplate('', $tplFile, $tpl_data);
                }
                else if(!$html_element)
                {
                    // 'default' - show button with example an load popup data in hidden modal
                    $tplFile = 'easy_credit_widget_button.tpl.html';
                    if ($financingOptions['financing_options'][0]['maxApr'] == 0)
                    {
                        $tplFile = 'easy_credit_widget_button_no_apr.tpl.html';
                    }
                    $template = new Template();
                    $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
                    $html = $template->getTemplate('', $tplFile, $tpl_data);

                    $tplFile = 'easy_credit_widget_modal.tpl.html';
                    $template = new Template();
                    $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
                    $html .= $template->getTemplate('', $tplFile, $tpl_data);
                }
            }
        }
        else {
            $tplFile = 'easy_credit_widget_button_out_of_range.tpl.html';
            $template = new Template();
            $template->getTemplatePath($tplFile, 'xt_easy_credit', $subDir, 'plugin');
            $tpl_data = array(
                'min' => self::getFinancingMin(),
                'max' => self::getFinancingMax(),
                'css_class' => 'installments-' . $position,
                'financing_what' => $financing_what
            );
            $html = $template->getTemplate('', $tplFile, $tpl_data);
        }

        return $html;
    }


    /* Do the API call posting JSON encoded data to the endpoint
     * @param $data - array
     * @return cUrl response
     * */
    function ApiJSONCall($data, $endpoint, $error_message = '', $method = "POST")
    {

        global $store_handler;
        $uid = 0;
        if (EASY_CREDIT_API_LOGGING == 1)
        {
            $uid = uniqid();
            $log_data = array(
                'time' => date("Y-m-d H:i:s"),
                'store_id' => $store_handler->shop_id,
                'data' => $data,
                'endpoint' => $endpoint,
                'log_message' => $error_message,
                'method' => $method
            );
            $log_string = 'API Request ' . $uid . PHP_EOL . print_r($log_data, true);
            $this->dataLog($log_string);
        }

        $ch = curl_init();
        switch ($method)
        {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
                break;
            case 'PATCH':

                //curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
                break;
            case 'GET':
                $endpoint .= "?" . http_build_query($data);
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'tbk-rk-shop: '.$this->REST_CLIENT_ID,
                'tbk-rk-token: '.$this->REST_SECRET_KEY,
                'partner_id: '.$this->EASY_CREDIT_partnerID,
                'software_version: xtcommerce_'._SYSTEM_VERSION,
                'module_version: '.$this->_module_version)
        );

        if (EASY_CREDIT_API_LOGGING == 1)
        {
            $fp = fopen(_SRV_WEBROOT . _SRV_WEB_LOG . 'curl_log.txt', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }

        // value should be an integer for the following values of the option parameter: CURLOPT_SSLVERSION
        // CURL_SSLVERSION_TLSv1_2 (6)
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        // to ensure curls verify functions working in dev:
        // download http://curl.haxx.se/ca/cacert.pem
        // point _CURL_CURLOPT_CAINFO to cacert.pem, eg in config.php
        if (_CURL_CURLOPT_CAINFO !== '_CURL_CURLOPT_CAINFO')
        {
            curl_setopt($ch, CURLOPT_CAINFO, _CURL_CURLOPT_CAINFO);
        }

        $result = curl_exec($ch);

        if (EASY_CREDIT_API_LOGGING == 1)
        {
            $log_data = array(
                'time' => date("Y-m-d H:i:s"),
                'store_id' => $store_handler->shop_id,
                'curl_errno' => curl_errno($ch),
                'result' => $result
            );
            $log_string = 'API Response ' . $uid . PHP_EOL . print_r($log_data, true);
            $this->dataLog($log_string);
        }

        if (curl_errno($ch))
        {
            $log_data = array();
            $log_data['class'] = 'error_ApiJSONCall';
            $log_data['transaction_id'] = ' ';
            $log_data['error_msg'] = $error_message . '-' . curl_error($ch) . '-' . curl_errno($ch);
            $this->_addCallbackLog($log_data);
        }
        curl_close($ch);
        return $result;
    }

    function dataLog($data_string)
    {
        $f = fopen(_SRV_WEBROOT . 'xtLogs/easy_credit.log', 'a+');
        if ($f)
        {
            fwrite($f, $data_string . "\n");
            fclose($f);
        }
    }

    function _addCallbackLog($log_data)
    {
        global $db;
        //if (is_null($log_data['transaction_id'])) return false;
        $log_data['module'] = 'xt_easy_credit';

        $log_data['orders_id'] = isset($this->orders_id) ? $this->orders_id : 0;
        if (is_array($log_data['callback_data']))
        {
            $log_data['callback_data'] = serialize($log_data['callback_data']);
        }
        if (is_array($log_data['error_data']))
        {
            $log_data['error_data'] = serialize($log_data['error_data']);
        }
        if ($log_data['error_data'] == '')
        {
            $log_data['error_data'] = '';
        }
        $db->AutoExecute(TABLE_CALLBACK_LOG, $log_data, 'INSERT');
    }


    protected function cleanPhoneNumber($s)
    {
        $s = preg_replace("/[^0-9\+]/", "", $s);
        $s = preg_replace("/(?!^)\+/", "", $s);
        return $s;
    }

    protected static function ec_installments_format_array(&$v, $k, $u)
    {
        global $price;
        $skip = array('terminErsteRate', 'terminLetzteRate', 'anzahlRaten', 'effektivzins', 'nominalzins');
        if(!in_array($k, $skip) && is_numeric($v))
        {
            //$v = number_format($v,2,$u['dec_point'],$u['thousands_sep']);
            $v = trim($price->_StyleFormat($v));
        }
    }

    public static function getFinancingMin()
    {
        return self::$_financing_range['min'];
    }

    public static function getFinancingMax()
    {
        return self::$_financing_range['max'];
    }


}
