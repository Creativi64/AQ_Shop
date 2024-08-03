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
 * Klasse enthält diverse Hilfsmethoden, welche über alle calls hinweg genutzt werden.
 * Hier ist sehr viel Logik der Schnittstelle abgebildet.
 */
class SoapHelper
{

    /** Hält Systemsprachen als DB Abfrage: "SELECT DISTINCT code,code FROM " . TABLE_LANGUAGES; */
    static $languages;

    /** Hält customer stati als DB Abfrage: "SELECT DISTINCT customers_status_id,customers_status_id FROM " . TABLE_CUSTOMERS_STATUS */
    static $cust_status;

    /** Hält Informationen zu allen Systemtabllen in assoziativem Array. Wird per DB Abfrage "SHOW TABLES" gefüllt */
    static $tablesInDb;

    /** Cache für Sprachen Abfrage */
    static $cache_language_list;

    static $request;
    static $response;


    /** Konstante WIRD NICHT GENUTZT!!!
     *  TODO: Entfernen!
     */
    const defaultLang = 'de'; // unused

    /** Konstante für SOAP/System-Meldung: Fehler */
    const MessageFail = "FAIL";
    /** Konstante für SOAP/System-Meldung: Erfolg */
    const MessageSuccess = "SUCCESS";
    /** Konstante für SOAP/System-Meldung: Login fehlgeschlagen */
    const MessageLoginFailed = "LOGIN FAILED";

    static $ActivateLogging;


    static function setRequest($request, $type = 'json')
    {

        if ($type == 'json') {
            $request['paras']['pass'] = '******';
            $request = json_encode($request);
        }
        if ($type == 'xml') {
            $request = preg_replace('/<pass[":=\w\s]+>.*?<\/pass>/','<pass xsi:type="xsd:string">*******</pass>',$request);
        }

        self::$request = $request;
    }

    static function setResponse($response)
    {
        self::$response = $response;
    }

    static function logRequest($service)
    {
        global $db;
        if (self::$ActivateLogging == 1) {

            $data = array();
            $data['api_user_id'] = '1';
            $data['request_service'] = $service;
            $data['api_request'] = self::$request;
            $data['api_response'] = self::$response;
            $data['log_time'] = time();
            $data['request_source'] = self::getCurrentIp();
            $oC = new adminDB_DataSave(TABLE_XT_API_LOG, $data, false, __CLASS__); // true == insert??
            $oC->saveDataSet();

        }
    }

    /**
     * Produkt SEO Daten in bestimmter Sprache ($lang_code) in DB updaten
     * @global type $db
     * @global type $seo
     * @param type $productname Name vom Produkt
     * @param type $lang_code Sprachcode der Produktinfos
     * @param type $productID ID vom Produkt
     * @param type $Productdata Array mit Produktdaten
     */
    static function processProductSeoUrl($productname, $lang_code, $productID, $Productdata)
    {
        // globale Variablen $db und $seo nutzen
        global $db, $seo;

        // Datenarray bauen
        $data = array();

        // Wenn Eintrag "seo_url" in $Productdata gefüllt, dann
        if (isset($Productdata['seo_url']) && strlen($Productdata['seo_url']) > 2) {
            // In datenarray Eintrag "url_text_" + $lang_code mit Inhalt von $Productdata['seo_url'] füllen
            $data['url_text_' . $lang_code] = $Productdata['seo_url'];
        } else {
            // In datenarray Eintrag "url_text_" + $lang_code mit Produktnamen füllen
            $data['url_text_' . $lang_code] = $productname;
        }

        // Flag: Sollen SEO URLs neu generiert werden?
        $auto_generate = true;

        // Soll "alte" SEO URL gerettet werden?
        if (XT_API_REBUILD_SEO_URLS == 'false') {

            $record = $db->Execute("SELECT link_id,language_code,url_text FROM " . TABLE_SEO_URL . " WHERE link_type=1 AND link_id=" . $productID . " and language_code='" . $lang_code . "'");
            while (!$record->EOF) {
                $data['url_text_' . $record->fields["language_code"]] = $record->fields["url_text"];

                $record->MoveNext();

                // Artikel ist vorhanden!
                $auto_generate = false;


            }

            $record->Close();
        }


        // Evtl. schon vorhandenen Produkteintrag in DB löschen
        $sql = "delete from " . TABLE_SEO_URL . " where link_type='1' and link_id='" . $productID . "' and language_code='" . $lang_code . "'";
        $db->Execute($sql);


        // Datenarray füllen, jeweils mit lang_code dran
        $data['meta_title_' . $lang_code] = $Productdata['meta_title'];
        $data['meta_description_' . $lang_code] = $Productdata['meta_description'];
        $data['meta_keywords_' . $lang_code] = $Productdata['meta_keywords'];


        // Produkteintrag in DB updaten mit XT Funktion _UpdateRecord
        $seo->_UpdateRecord('product', $productID, $lang_code, $data, $auto_generate);
    }

    /**
     * Schreibt alle Systemsprachen in statische Variable $languages, falls diese noch nicht gefüllt wurde
     * @global type $db
     * @return type
     */
    static function getLanguages()
    {
        // wenn $languages noch nicht gefüllt
        if (!self::$languages) {
            global $db;
            $sql = "SELECT DISTINCT code,code FROM " . TABLE_LANGUAGES;
            self::$languages = $db->GetAssoc($sql);
        }
        return self::$languages;
    }


    /**
     * Funktion gibt Array mit Infos zu allen aktiven Sprachen zurück analog zu $language->_getLanguageList()
     * Unterschied: Hier weden alle aktiven Shopsprachen UNABHÄNGIG zur Domain zurückgeliefert. (siehe mehsprachige Multishopsysteme )
     * @global type $db
     * @return type
     */
    static function getLanguageList()
    {

        global $db;

        // caching

        if (isset($cache_language_list)) {
            return $cache_language_list;
        }

        $data = array();

        $record = $db->CacheExecute("SELECT * FROM " . TABLE_LANGUAGES . " l  where l.languages_id != '' and l.language_status = '1'");
        while (!$record->EOF) {
            $record->fields['id'] = $record->fields['code'];
            $record->fields['text'] = $record->fields['name'];
            $record->fields['icon'] = $record->fields['image'];
            $record->fields['edit'] = $record->fields['allow_edit'];

            $data[] = $record->fields;
            $record->MoveNext();
        }
        $record->Close();

        // in den cache damit
        $cache_language_list = $data;

        return $data;


    }


    /**
     * Schreibt alle customer stati in statische Variable $cust_status
     * @global type $db
     * @return type
     */
    static function getCustomerStatus()
    {
        // ist statische Variable $cust_status gefüllt?
        if (!self::$cust_status) {
            // Nein, dann alle Kundengruppen in Variable $cust_status schreiben

            global $db;
            $sql = "SELECT DISTINCT customers_status_id,customers_status_id FROM " . TABLE_CUSTOMERS_STATUS;
            $all = $db->getAll($sql);
            foreach ((array)$all as $i => $array) {
                $table = array_shift($array);
                self::$cust_status[$table] = $table;
            }
        }
        return self::$cust_status;
    }

    /**
     * Artikeldaten in DB updaten/anlegen
     * Wird von "setArticle.php" aufgerufen
     * @param type $description Array mit Produktinformationen
     * @param type $productID ID vom Produkt
     * @param type $modus Updatemodus (default ist "UPDATE" )
     * @throws Exception Fallse ein Fehler auftritt
     */
    static function processProductsDescription($description, $productID, $modus = 'UPDATE')
    {
        // Ist $productID = false oder leer
        if ($productID == false || $productID == "") {
            // Exception werfen
            throw new Exception("Products ID is invalid");
        }

        // Alle Systemsprachen in Variable $tempLngArray kopieren
        $tempLngArray = self::getLanguages();

        // Wenn array $description mehr als einen Eintrag hat
        if (count($description) > 0) {
            // Über array $description laufen. Einträge in $data mappen
            foreach ($description as $index => $data) {

                // Ist der Sprachcode der Daten in $data auch Systemweit gesetzt, also in $languages vorhanden
                if (isset(self::$languages[$data['language_code']])) {

                    // existierende sprache entfernen, alle sprachen die uebrig bleiben werden spaeter zu den descriptions hinzugefuegt
                    unset($tempLngArray[$data['language_code']]);

                    // Wenn XT_API_CHARSET = ISO
                    if (XT_API_CHARSET == 'ISO-8859-1') {
                        // Über alle Dateneiträge in $data laufen
                        foreach ($data as $key => $val) {
                            // Alle Einträge in UTF-8 konvertieren
                            $data[$key] = self::utf8helper($val);
                        }
                    }

                    // Prdukt SEO Daten pro Sprache in DB anlegen lassen
                    self::processProductSeoUrl($data['products_name'], $data['language_code'], $productID, $data);
                    // Produkts ID in $data setzen. Wird gemacht, falls es ein neuer Produkteintrag in DB war.
                    // Dann wurde die neue productId von der Methode processProductSeoUrl gesetzt
                    $data['products_id'] = $productID;


                    $oC = new adminDB_DataSave(TABLE_PRODUCTS_DESCRIPTION, $data, false, __CLASS__); // true == insert??
                    // Methode saveDataSet aufrufen = Daten werden in DB geschrieben
                    $objC = $oC->saveDataSet();
                }
            }
        }
        // alle nicht uebergebenen Sprachen auch einfuegen, bei update nichts machen sprachen koennen auch im shop gepflegt werden
        // Ist noch ein Eintrag in $tempLngArray vorhanden und der modus="INSERT"
        if (count($tempLngArray) && $modus == 'INSERT') {
            // Für alle NICHT genutzten Sprachen trotzdem einen Eintrag in DB anlegen
            foreach ($tempLngArray as $lang) {
                $data = array();
                $data['language_code'] = $lang;
                $data['products_id'] = $productID;
                $oC = new adminDB_DataSave(TABLE_PRODUCTS_DESCRIPTION, $data, true, __CLASS__); // true == insert??
                $objC = $oC->saveDataSet();
            }
        }
    }


    /**
     * Wandelt String in UTF-8 Encoding um, falls XT_API_CHARSET NICHT UTF-8
     * Generell sollte die SOAP Schnittstelle immer UTF-8 nutzen!
     * @param type $string Umzuwandelnder String
     * @return string UTF-8 kodierter String
     */
    static function utf8helper($string)
    {
        // Wenn String leer, dann Leerstring zurückgeben
        if ($string == '')
            return '';

        // Wenn XT encoding schon UTF-8 dann String OHNE Umwandlung zurück, ist ja schon UTF-8
        if (XT_API_CHARSET == 'UTF-8')
            return $string;

        // wenn wir hier sind, dann ist entweder XT_API_CHARSET auf ISO-8859-1 oder auf was anderes gesetzt
        // aktuell werden aber nur diese beiden Modi gesetzt (siehe SoapController.class.php). Wenn also
        // was anderes als UTF-8 oder ISO-8859-1 gesetzt wird, wird soap_defencoding für den nusoap-server
        // nicht gesetzt -> damit bleibt dieser Wert auf dem Standard und der ist ISO-8859-1. Also können
        // wir hier erstmal einfach utf8_encode benutzen.
        // String zu UTF-8 konvertiert, zurückgeben
        return utf8_encode($string);
    }


    /**
     * Preisstaffeln für ein Produkt löschen
     * Wird von setArticle.php genutzt
     * @global type $db
     * @param type $productsID
     */
    static function deleteProductPricesStaffel($productsID)
    {
        // globale Variable $db nutzen
        global $db;

        // Produkt ID zu int umwandeln
        $productsID = (int)$productsID;
        // Produktgruppen holen
        $groups = self::getCustomerStatus();
        // Über alle Produktgruppen laufen
        foreach ($groups as $key => $val) {
            // Gibt es Preisgruppen Tabelle für diese Gruppe in DB?
            if ($table = self::getTableName(TABLE_PRODUCTS_PRICE_GROUP . "" . $key)) {
                // SQL lösch-Anweisung bauen
                $sql = "DELETE from " . $table . " where products_id='$productsID'";
                // Wenn globales flag XT_SOAP_DEL_GRP_PRC gesetzt, dann Preisgruppe löschen
                if (XT_API_DEL_GRP_PRC == 'true') {
                    $db->Execute($sql);
                }
            }
        }

        // In globaler Preisgruppentabelle TABLE_PRODUCTS_PRICE_GROUP alle Eintrgäge für PridukID löschen,
        // wenn globale flag XT_SOAP_DEL_GRP_PRC gesetzt
        $sql = "DELETE from " . TABLE_PRODUCTS_PRICE_GROUP . "all where products_id='$productsID'";
        if (XT_API_DEL_GRP_PRC == 'true')
            $db->Execute($sql);
    }

    /**
     * Staffelpreise für ein Produkt in DB einfügen
     *
     * @param mixed $prices
     * @param mixed $productsID
     */
    static function processProductPricesStaffel($prices, $productsID)
    {
        // Globle Variable $db nutzen
        global $db;

        // Produkt ID zu int umwandeln
        $productsID = (int)$productsID;

        // delete all group prices
        /*
          $groups = self::getCustomerStatus();
          foreach ($groups as $key => $val) {
          if($table = self::getTableName(TABLE_PRODUCTS_PRICE_GROUP."".$key)){
          $sql = "DELETE from ".$table." where products_id='$productsID'";
          if (XT_SOAP_DEL_GRP_PRC=='true') $db->Execute($sql);
          }
          }
          $sql = "DELETE from ".TABLE_PRODUCTS_PRICE_GROUP."all where products_id='$productsID'";
          if (XT_SOAP_DEL_GRP_PRC=='true') $db->Execute($sql);
         */

        // Über Staffelpreise laufen
        foreach ((array)$prices as $group => $array) {
            // Gibt es Tabelle für diese Preisgruppe im System?
            if ($table = self::getTableName(TABLE_PRODUCTS_PRICE_GROUP . "" . $group)) {

                // durch gruppe gehen
                // und einzelne Staffelpreise anlegen
                foreach ($array as $key => $val) {
                    $newRow = array();
                    $newRow['products_id'] = $productsID;
                    $newRow['price'] = (double)$val['products_price'];
                    $newRow['discount_quantity'] = (int)$val['products_quantity'];
                    $oC = new adminDB_DataSave($table, $newRow, false, __CLASS__);
                    $objC = $oC->saveDataSet();
                }

            }
        }

        // Flag für Produkt setzen, dass Staffelpreise vorhanden sind
        try {  // set price flags
            $group_prices = new product_price();
            // XT Funktion _updateProductsGroupPriceflag aufrufen
            $group_prices->_updateProductsGroupPriceflag($productsID);
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Produktpreise für ein Produkt in DB schreiben
     * @global type $db
     * @param type $prices Array mit Preisen
     * @param type $productsID Produkt ID
     * @throws Exception
     */
    static function processProductPrices($prices, $productsID)
    {
        // Globale Variable $db nutzen
        global $db;
        // Produkt ID zu int umwandeln
        $productsID = (int)$productsID;
        // TODO delete all prices from all tables!
        // transform key = group
        /*
          $tmp = array();
          foreach((array)$prices as $index => $array){
          $tmp[$array['group']][] = $array;
          }
          $prices = $tmp;
         */
        //sdebugbreak();

        // Über alle Preise/Preisgruppen laufen
        foreach ((array)$prices as $group => $array) {

            // Gibt es Systemtabelle TABLE_PRODUCTS_PRICE_GROUP . "" . $array['status']?
            if ($table = self::getTableName(TABLE_PRODUCTS_PRICE_GROUP . "" . $array['status'])) {
                // delete old data
                //	$sql = "delete from ".$table." where products_id='$productsID'";
                //	if (XT_SOAP_DEL_GRP_PRC=='true') $db->Execute($sql);
                //foreach($array as $key=>$data){
                // Neuen Eintrag/Zeile in Tabelle anlegen mit Preisinfos
                $newRow = array();
                $newRow['products_id'] = $productsID;
                $newRow['price'] = (double)$array['products_price'];
                $newRow['discount_quantity'] = (int)$array['products_quantity'];
                $oC = new adminDB_DataSave($table, $newRow, false, __CLASS__);
                $objC = $oC->saveDataSet();
                //}
            }
        }

        // Produktpreise / Gruppenpreise Flag setzen
        try {  // set price flags
            $group_prices = new product_price();
            // XT Methode _updateProductsGroupPriceflag aufrufen, um Preistabelle für Produkt updaten
            $group_prices->_updateProductsGroupPriceflag($productsID);
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    /**
     * kundenpreise in db updaten
     * @param  [type] $customer_prices [description]
     * @param  [type] $productID       [description]
     * @param  [type] $customersID     [description]
     * @return [type]                  [description]
     */
    static function processProductCustomerPrices($customer_prices,$productID,$customersID) {
      global $db,$price;

      // Alle Kundenpreise Einträge für diese Produkt aus Tabelle TABLE_CUSTOMERS_PRICES löschen
      $db->Execute("DELETE FROM " . TABLE_CUSTOMER_PRICES . " WHERE products_id= ? AND customers_id = ?",array($productID,$customersID));

      if (!is_array($customer_prices)) return;

      include_once 'plugins/xt_customer_prices/classes/class.xt_customer_prices.php';

      $customerP = new xt_customer_prices;

      foreach ($customer_prices as $id => $prices) {
        if (isset($prices['price']) && $prices['quantity']>0) {

          $special = array();
          $special['products_id'] = $productID;
          $special['price'] = $prices['price'];
          $special['customers_id'] = $customersID;
          $special['discount_quantity'] = $prices['quantity'];

          $special['date_available'] = strtotime($prices['date_available']);
          $special['date_available'] = date("Y-m-d H:i:s", $special['date_available']);
          $special['date_expired'] = strtotime($prices['date_expired']);
          $special['date_expired'] = date("Y-m-d H:i:s", $special['date_expired']);

          $customerP->_set($special, "new");
        }

      }

    }

    /**
     * Produktspezial Preise in DB updaten
     * @global type $db
     * @global type $price
     * @param type $special_prices
     * @param type $productID
     * @param type $tax
     */
    static function processProductSpecialPrices($special_prices, $productID, $tax = 0)
    {
        // Globale Variablen $db und $price nutzen
        global $db, $price;

        // Alle Spezialpreis Einträge für diese Produkt aus Tabelle TABLE_PRODUCTS_PRICE_SPECIAL löschen
        $db->Execute("DELETE FROM " . TABLE_PRODUCTS_PRICE_SPECIAL . " WHERE products_id='" . $productID . "'");


        if (!is_array($special_prices)) return;

        // externe php Datei des plugins "xt_special_products" einbinden
        include_once 'plugins/xt_special_products/classes/class.product_sp_price.php';

        // Neues Objekt der Klasse product_sp_price bauen
        $specialP = new product_sp_price;


        //    $new_special_prices = array();
        //    $new_special_prices[0] = $special_prices;

        // Über alle speciual Preise in $special_prices laufen
        foreach ($special_prices as $id => $prices) {

            // Ist Eintrag "special_price" in $prices vorhanden?
            if (isset($prices['special_price'])) {
                // Neues array für Spezialpreise bauen
                $special = array();
                // Produkt ID setzen
                $special['products_id'] = $productID;

                // Preis setzen, dabei "," zu "." umwandeln (zu double Notation)
                $special['specials_price'] = $prices['special_price'];


                // Wenn $price staus = "1", dann auch special Preis status auf 1 setzen
                if ($prices['status'] == '1')
                    $special['status'] = (int)$prices['status'];

                // Datumsangaben setzen - dabei Datumsformate umwandeln
                $special['date_available'] = strtotime($prices['date_available']);
                $special['date_available'] = date("Y-m-d H:i:s", $special['date_available']);
                $special['date_expired'] = strtotime($prices['date_expired']);
                $special['date_expired'] = date("Y-m-d H:i:s", $special['date_expired']);

                // group_permissions für VKs setzen.liegen in int array group_permissions
                foreach ($prices["group_permissions"] as $id => $vk) {
                    $special['group_permission_' . $vk] = 1;
                }

                if ($prices["group_permission_all"] == 1) {
                    $special['group_permission_all'] = 1;
                }


                // Spezialpreise mit XT Funktion "_set" setzen
                // Anpassung: $set_type auf "new" damit Brutto_Admin Flag den Preis nicht ändert!
                $specialP->_set($special, "new");


                // wenn PReisstatus = 1, dann XT Funktion "_setStatus" aufrufen
                if ($prices['status'] == '1')
                    $specialP->_setStatus($productID, $special['status']);
            }

            // Produkt Spezialpreise Flags updaten lassen
            // per XT Funktion _updateProductsSpecialflag
            $specialP->_updateProductsSpecialflag($productID);

        }
    }


    /**
     * Ein array mit ShopPermissions anhand von $permissions bauen
     * @param type $permissions
     * @return int
     */
    static function processShopPermissions($permissions)
    {
        // Neues array $perm_array bauen
        $perm_array = array();

        // ist $permissions ein array?
        if (is_array($permissions)) {
            // über alle Einträge in $permissions laufen
            foreach ($permissions as $key => $val) {
                // Neuen Eintrag in $perm_array anlegen mit key= "shop_" + shop id und Wert = 1
                $perm_array['shop_' . (int)$val['shop_id']] = 1;
            }
        }

        return $perm_array;
    }


    /**
     * Produktattribute in DB einfügen
     * @global type $db
     * @global type $language
     * @param type $ProductItem
     * @param type $productID
     */
    static function processProductAttributes($ProductItem, $productID)
    {
        // Globale Variablen $db und $language nutzen
        global $db, $language;

        // externe PHP Datei einbinden
        require_once _SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.xt_master_slave.php';

        // attr to prod connection drop
        // Alle Produkt zu Attribut Verknüpfungen aus Tabelle TABLE_PRODUCTS_TO_ATTRIBUTES für diese Produkt löschen
        $sql = "DELETE FROM " . TABLE_PRODUCTS_TO_ATTRIBUTES . " WHERE products_id = '" . (int)$productID . "'";
        $db->Execute($sql);

        // Feld $ProductItem["products_attributes"] enthält array von  products_attributes_ids
        // In DB eintragen
        foreach ((array)$ProductItem["products_attributes"] as $index => $products_attributes_ids) {


            // per external IDs einfügen
            if ((int)$products_attributes_ids["external_attributes_id"] > 0) {

                $newId = SoapHelper::getAttributesIDByexternalID($products_attributes_ids["external_attributes_id"]);
                $newParentId = SoapHelper::getAttributesIDByexternalID($products_attributes_ids["external_attributes_parent_id"]);

                $sql = "insert into " . TABLE_PRODUCTS_TO_ATTRIBUTES . "(products_id, attributes_id, attributes_parent_id) values ('" . $productID . "','" . (int)$newId . "', '" . (int)$newParentId . "')";
                $db->Execute($sql);

            } else {

                $sql = "insert into " . TABLE_PRODUCTS_TO_ATTRIBUTES . "(products_id, attributes_id, attributes_parent_id) values ('" . $productID . "','" . (int)$products_attributes_ids["attributes_id"] . "', '" . (int)$products_attributes_ids["attributes_parent_id"] . "')";
                $db->Execute($sql);

            }


        }

    }


    /**
     * Produktzubehör Einträge in DB schreiben
     * @global type $db
     * @param type $product_cross_sell
     * @param type $productID
     */
    static function processProductXSell($product_cross_sell, $productID)
    {
        // Xsell
        global $db;

        // Alle evtl. vorhandenen Produktzubehör Einträge aus Tabelle TABLE_PRODUCTS_CROSS_SELL löschen
        $sql = "delete from " . TABLE_PRODUCTS_CROSS_SELL . " where products_id='" . (int)$productID . "'";
        $db->Execute($sql);

        // über alle Zubehöre in array $product_cross_sell laufen
        foreach ((array)$product_cross_sell as $i => $xsell_id) {

            // Produkt ID von Zubehör in $xsell schreiben
            $xsell = self::getProductID('', $xsell_id, '');

            // Ist $xsell gesetzt, dh es gibt eine zugehörige ProduktID
            if ($xsell > 0) {
                // Neuen Eintrag in Tabelle TABLE_PRODUCTS_CROSS_SELL anlegen
                // In der das Produkt mit dem Zubehör Produkt verknüpft wird
                $sql = "insert into " . TABLE_PRODUCTS_CROSS_SELL . "(products_id, products_id_cross_sell) values ('" . (int)$productID . "', '" . $xsell . "')";
                $db->Execute($sql);
            }
        }
    }


    /**
     * Produktkategorien in DB eintragen
     * Wird von setArticle.php genutzt
     * @global type $db
     * @param type $product_categories
     * @param type $productID
     */
    static function processProductsToCategories($product_categories, $productID, $doResolveExtId)
    {
        // Kategorien
        global $db;

        // Alle vorhandenen Produktkategorien aus Tabelle TABLE_PRODUCTS_TO_CATEGORIES für Produkt entfernen
        $sql = "DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $productID . "'";
        $db->Execute($sql);

        // Flag für erste Runde in Schleife
        $first = true;
        // Über Produktkategorien laufen
        foreach ((array)$product_categories as $i => $category_id) {

            // Kategorie ID ist die externe BW Kategorie ID! Auflösen! wenn flag $doResolveExtId=true

            if ($doResolveExtId) {
                $categoryIdXt = SoapHelper::getCategoriesIDByexternalID($category_id);
            } else {
                $categoryIdXt = $category_id;
            }


            // Wenn in erster Runde von Schleife
            if ($first) {
                // SQL bauen: Kategorieverknüpfung in DB anlegen mit master_link = 1
                $sql = "insert into " . TABLE_PRODUCTS_TO_CATEGORIES . "(products_id, categories_id, master_link ) values ('" . $productID . "', '" . $categoryIdXt . "','1')";
            } else {
                // SQL bauen: Kategorieverknüpfung in DB anlegen mit master_link = 0
                $sql = "insert into " . TABLE_PRODUCTS_TO_CATEGORIES . "(products_id, categories_id, master_link ) values ('" . $productID . "', '" . $categoryIdXt . "','0')";
            }
            // Falg $first auf false = erste Runde vorbei
            $first = false;
            // SQL ausführen = Kategoriemapping anlegen in Tabelle TABLE_PRODUCTS_TO_CATEGORIES
            $db->Execute($sql);
        }


    }


    /**
     * Verarbeitet Median vom Typ files
     *
     * @param string $fileBase64
     * @param string $name
     * @param string $element_id
     * @param string $miClass
     * @param string $fileUrl
     * @param string $fileHash
     *
     * @throws Exception
     */
    static function processFiles($fileBase64 = '', $name = '', $element_id, $miClass = "product", $fileUrl = '',$max_downloads=0,$max_downloads_days=0,$displayName=array(),$displayDesc=array())
    {
        global $language;

        // Keine Base64 Dateidaten UND keine fileUrl  --> raus
        if (strlen($fileBase64) < 20 && empty($fileUrl)) {
            if ('true' == XT_SOAP_DEBUG) {
                error_log('SOAP Warning: File processing abort. No base64 data or fileUrl available.');
            }
            return;
        }

        // Kein Dateiname --> raus
        if ($name == '') {
            if ('true' == XT_SOAP_DEBUG) {
                error_log('SOAP Warning: File processing abort. Filename is empty');
            }
            return;
        }

        // Dateipfad auf Ordner für Dateien bauen
        $dstFolder = _SRV_WEBROOT._SRV_WEB_MEDIA_FILES;

        // 2014-01-16: Garcia: Bugfix: Wenn JSON genutzt wird, dann muss die Konvertierung von base64 zu bytes hier erfolgen
        // Ansonsten erledigt das SOAP Framework das selbst
        if( XT_API_USE_JSON =='true'){
            $fileBase64 = base64_decode( $fileBase64 );
        }

        // Wenn $dstFolter ein Ordner und beschreibbar ist
        if (is_dir($dstFolder) && is_writable($dstFolder)) {

            $filePath = $dstFolder . $name;


                if (!empty($fileBase64)) {
                    // Base64 Daten in Datei schreiben, wenn vorhanden
                    file_put_contents($filePath, $fileBase64);

                } else if (!empty($fileUrl)) {

                    $fileUrl = str_replace(" ","%20",$fileUrl); // to properly format the url

                    // Daten von URL laden und in Datei schreiben, wenn URL angegeben
                    $resource = fopen($fileUrl, 'r');
                    if ($resource === false) {
                        // Falls das Bild nicht geladen werden kann, Verarbeitung hier abbrechen!
                        return;
                    }
                    file_put_contents($filePath, $resource);
                    fclose($resource);
                }


            global $db;

            // Media ID für diese Datei anhand von $name aus Tabelle TABLE_MEDIA holen
            $sql = "SELECT `id` FROM " . TABLE_MEDIA . " WHERE `file` = ? ";
            $mediaId = $db->GetOne($sql, array($name));

            // Datei ist noch nicht in der MEDIA Tabelle enthalten, also lege einen neuen Eintrag an
            if (empty($mediaId)) {

                // Neues MediaData Obj bauen
                $md = new MediaData();
                $md->setClass($miClass);

                // Dateityp anhand von Dateisuffix ermitteln
                $type = $md->_getFileTypesByExtension($name);
                $dl_type = 'free';
                if ($miClass!='files_free') $dl_type = 'order';

                $data = array();
                $data = array('file' => $name,
                  'type' => $type,
                  'max_dl_count'=>$max_downloads,
                  'max_dl_days'=>$max_downloads_days,
                  'download_status'=>$dl_type);

                // language stuff
                foreach ($language->_getLanguageList() as $key => $val) {
                  if (isset($displayName[$val['code']])) $data['media_name_'.$val['code']]=$displayName[$val['code']];
                  if (isset($displayDesc[$val['code']])) $data['media_description_'.$val['code']]=$displayDesc[$val['code']];
                  $data['language_code']='1';
                }



                // Dateiname und Typ setzen in $md und in DB anlegen
                $md->setMediaData($data);

                // Media ID ermitteln, nachdem die Datei hinzugefügt wurde
                $mediaId = $db->GetOne($sql, array($name));
            }

            // Versuche einen Eintrag für die Tabelle MEDIA LINK zu erzeugen, um die Datei dem Produkt zuzuornen
            try {
                $data            = array();
                $data['type']    = "media";
                $data['class']   = "product";
                $data['link_id'] = $element_id; // artikelnummer
                $data['m_id']    = $mediaId; // media id


                $sql = "SELECT * FROM " . TABLE_MEDIA_LINK . " WHERE `link_id` = ? AND `m_id`= ?";
                $checkq = $db->Execute($sql, array($element_id,$mediaId));
                if ($checkq->RecordCount()==0) {
                  // Daten in Tabelle anlegen
                  $oC = new adminDB_DataSave(TABLE_MEDIA_LINK, $data, false, __FILE__); // true == includes language_code
                  $objC = $oC->saveDataSet();
                }




            } catch (Exception $ex) {

                // Werfe eine Exception, sofern die Datei nicht dem Produkt zugeordnet werden kann.
                throw new Exception('SOAP Error: Error while creating link of file (' . $name . ') to product (' . $element_id . ')');
            }
        }
    }

    /**
    * 	Loeschen aller Image zuordnungen zu einem Produkt
    *
    * @param int $product_id
    */
    static function clearMediaFilesLinkFromProduct($product_id) {
      global $db;
      $sql = "DELETE FROM " . TABLE_MEDIA_LINK . " WHERE `class`='product' AND `type`='media' AND `link_id`='" . (int) $product_id . "'";
      $db->Execute($sql);
    }


    /**
     * Process Image File, Store and resize File, insert data in table media and media_link
     * Grafik (Base64 kodiert) im Filesystem anlegen und in in media + media_link Tabelle eintragen
     *
     * @param string $imageBase64 imageSource
     * @param string $name ImageName
     * @param string $imageUrl URL zum Bild
     * @param string $imageHash md5-Hash des Bildes
     * @param int $products_id
     * @param boolean $firstimage if firstimage no insert in additional composition table media_link
     * @param string $miClass
     *
     * @throws Exception
     */
    // 2015-11-05: Blackbit Anpassung imageUrl und imageHash
    //static function processImage($imageBase64 = '', $name = '', $products_id, $firstimage = true, $miClass = "product") {
    static function processImage($imageBase64 = '', $name = '', $products_id, $firstimage = true, $miClass = "product", $imageUrl = '', $imageHash = '')
    {
        // Keine Base64 Bilddaten UND keine imageUrl  --> raus

        if (strlen($imageBase64) < 20 && empty($imageUrl)) {
            return;
        }

        // Kein Bildname = raus
        if ($name == '') {
            return;
        }

        // 2014-01-16: Garcia: Bugfix: Wenn JSON genutzt wird, dann muss die Konvertierung von base64 zu bytes hier erfolgen
        // Ansonsten erledigt das SOAP Framework das selbst
        if (XT_API_USE_JSON == 'true') {
            $imageBase64 = base64_decode($imageBase64);
        }

        // Dateipfad auf Ordner für Original-Grafiken (=unskaliert) bauen
        $imageFolder = _SRV_WEBROOT . _SRV_WEB_IMAGES . 'org/';

        // Wenn $imageFolder ein Ordner und beschreibbar ist
        if (is_dir($imageFolder) && is_writable($imageFolder)) {

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

                // resizen und einsortieren in die entsprechenden Verzeichnisse
                $md = new MediaImages;

                // Klasse setzen auf "product"
                $md->setClass($miClass);
                $md->processImage($name, true);
            }


            global $db;

            // media id für diese Datei anhand von $name aus Tabelle TABLE_MEDIA holen
            $sql = "select id from " . TABLE_MEDIA . " where file='" . $name . "' ";
            $media_id = $db->GetOne($sql);


            // Es gibt noch keine Eintrag in TABLE_MEDIA für diese Datei, also anlegen
            if (empty($media_id)) {
                // tabelle media befuellen
                // Neues MediaData Obj bauen
                $md = new MediaData();
                $md->setClass($miClass);
                // Dateityp anhand von Dateisuffix ermitteln
                $type = $md->_getFileTypesByExtension($name);
                // Dateiname und Typ setzen in $md und in DB anlegen
                $md->setMediaData(array('file' => $name, 'type' => $type));

                // nun die media Id aus DB ziehen
                // TODO: Frage: Warum kennt das Obj $md seine ID nicht? Weshalb muss sie hier erneut aus DB abgefragt werden?
                $media_id = $db->getOne("select id from " . TABLE_MEDIA . " where file='" . $name . "' ");
            }

            /* 2018-06-24: CaseSensitive Problem.
             *  Der Dateiname wird bei Änderung von aussen nicht in DB aktualisiert
             *  durch vorher manuelle Zuweisung im Shop, kann es vorkommen das durch nachträgliches ändern per API
             *  immer noch der Dateiname in TABLE_MEDIA auf den alten Dateinamen verweist
             */
            if (!empty($media_id) && !empty($name)) {
                $db->Execute("UPDATE " . TABLE_MEDIA . " SET file='" . $name . "'  WHERE id=" . $media_id);
            }

            // wenn $firstimage == false UND von Typ boolean!
            // '===' Operator vergleicht Wert UND Typ!
            // dh Grafik ist NICHT das erste Bild für Produkt, so dass ein Eintrag in
            // Tabelle TABLE_MEDIA_LINK mit Zuweisung zwischen prductId und mediaId angelegt werden
            if (false === $firstimage) {
                // Evtl. Exception soll gefangen werden
                try {
                    // Eintrag für Tabelle TABLE_MEDIA_LINK erzeugen
                    // zuordung zum produkt:
                    $data = array();
                    $data['type'] = "images";
                    $data['class'] = "product";
                    $data['link_id'] = $products_id; // artikelnummer
                    $data['m_id'] = $media_id; // media id
                    // Daten in Tabelle anlegen
                    $oC = new adminDB_DataSave(TABLE_MEDIA_LINK, $data, false, __FILE__); // true == includes language_code
                    $objC = $oC->saveDataSet();
                } catch (Exception $ex) {
                    // Exception wurde gefangen
                    // und eine neue Exception wird geworfen
                    throw new Exception('Cant create Image ' . $imageFolder . ' is not writable');
                }
            } else {
                // Hauptbild

                /**
                 *
                 * Durch _setImage "verliert" das product alle Status Felder (boolean-Werte),
                 * daher wird als Hotfix der Bilddateineime direkt in die DB geschrieben.
                 *
                 * // Status von Produkt sichern, wird durch _setImage gelöscht / auf false gesetzt
                 * $sql = "select products_status from " . TABLE_PRODUCTS . " where products_id='" . $products_id . "' ";
                 * $curr_status = $db->GetOne($sql);
                 *
                 * //$prod = new product;
                 * $prod = new product($products_id);
                 * $prod->setPosition('admin');
                 *
                 * $prod->_setImage($products_id, $name);
                 *
                 * // Status muss nach _setImage gesetzt werden
                 * $prod -> _setStatus($products_id, $curr_status);
                 *
                 */
                $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET products_image='" . $name . "'  WHERE products_id=" . $products_id);
            }
        } else {
            // Hier wird hinverzweigt, wenn Bilderordner NICHT beschreibbar ist
            // Eine neue Exception wird geworfen
            throw new Exception('Cant create Image ' . $imageFolder . ' is no folder or is not writable');
        }
    }


    /**
     * Kategorie ID anhand von externer Kategorie ID zurückgeben
     * Wird von "setCategory.php" genutzt
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getCategoriesIDByexternalID($external_id)
    {
        global $db, $filter;


        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select categories_id from " . TABLE_CATEGORIES . " where external_id = '" . $external_id . "'";
        // Ersten Treffer in $categoryID schreiben
        $categoryID = $db->getOne($sql);

        // Wenn keine KategorieID ermittlet werden konnte, dann false zurückgeben,
        // sonst $categoryID
        return (empty($categoryID) ? false : $categoryID);
    }

    static function getCategoriesExternalIDByID($categories_id)
    {
        global $db, $filter;

        // categories_id filtern
        $categories_id = $filter->_filter($categories_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select external_id from " . TABLE_CATEGORIES . " where categories_id = " . $categories_id;
        // Ersten Treffer in $categoryID schreiben
        $external_id = $db->getOne($sql);

        return (empty($external_id) ? false : $external_id);
    }

    /**
     * Attribut ID anhand von externer Attribut ID zurückgeben
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getAttributesIDByexternalID($external_id)
    {
        global $db, $filter;

        // ExternalID filtern
        $external_id = $filter->_filter($external_id);

        // SQL Abfrage für Tabelle TABLE_PRODUCTS_ATTRIBUTES bauen
        $sql = "SELECT attributes_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE bw_id = '" . $external_id . "'";

        $attributesID = $db->GetOne($sql);

        // Wenn keine AttributID ermittlet werden konnte, dann false zurückgeben, sonst $attributesID
        return (empty($attributesID) ? false : $attributesID);
    }

    /**
     *  Externe Attribut ID anhand von interner Attribut ID zurückgeben
     * @global type $db
     * @global type $filter
     * @param type $attributes_id
     * @return type
     */
    static function getAttributesExternalIDByID($attributes_id)
    {
        global $db, $filter;

        // $attributes_id filtern
        $attributes_id = $filter->_filter($attributes_id);

        // SQL Abfrage für Tabelle TABLE_PRODUCTS_ATTRIBUTES bauen
        $sql = "SELECT bw_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE attributes_id = '" . $attributes_id . "'";

        $external_id = $db->GetOne($sql);

        // Wenn keine AttributID ermittlet werden konnte, dann false zurückgeben,
        // sonst $attributes_id
        return (empty($external_id) ? false : $external_id);
    }

    /**
     * Manufacturers ID anhand von externer Hersteller ID zurückgeben
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getManufacturersIDByexternalID($external_id)
    {
        global $db, $filter;

        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select manufacturers_id from " . TABLE_MANUFACTURERS . " where external_id = '" . $external_id . "'";
        // Ersten Treffer in $categoryID schreiben
        $manuID = $db->getOne($sql);

        // Wenn keine KategorieID ermittlet werden konnte, dann false zurückgeben,
        // sonst $categoryID
        return (empty($manuID) ? false : $manuID);
    }

    /**
     * Customers ID anhand von externer customer ID zurückgeben
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getCustomersIDByexternalID($external_id)
    {
        global $db, $filter;

        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select customers_id from " . TABLE_CUSTOMERS . " where external_id = '" . $external_id . "'";
        // Ersten Treffer in $categoryID schreiben
        $custID = $db->getOne($sql);

        // Wenn keine $custID ermittlet werden konnte, dann false zurückgeben,
        // sonst $custID
        return (empty($custID) ? false : $custID);
    }

    /**
     *
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getMediaIDByexternalID($external_id)
    {
        global $db, $filter;


        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select id from " . TABLE_MEDIA . " where external_id = '" . $external_id . "'";
        // Ersten Treffer in $categoryID schreiben
        $mediaID = $db->getOne($sql);

        // Wenn keine KategorieID ermittlet werden konnte, dann false zurückgeben,
        // sonst $categoryID
        return (empty($mediaID) ? false : $mediaID);
    }

    /**
     * Customers external id anhand von Customer ID zurückgeben
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getCustomersExternalId($customers_id)
    {
        global $db, $filter;


        // SQL Abfrage für Tabelle TABLE_CUSTOMERS bauen
        $sql = "select external_id from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'";
        // Ersten Treffer nehmen
        $external_id = $db->getOne($sql);

        // 2013-01-21:  Block eingefügt, da einige Alt-Kunden schon die alte Schnittstelle V-Connect genutzt haben
        if (empty($external_id)) {
            // SQL Abfrage für Tabelle TABLE_CUSTOMERS bauen
            $sql = "select customers_cid from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'";
            // Ersten Treffer nehmen
            $external_id = $db->getOne($sql);

        }


        // Wenn keine external ermittlet werden konnte, dann false zurückgeben,
        // sonst $external_id
        return (empty($external_id) ? false : $external_id);


    }


    /**
     * Produkt ID anhand von ProduktModell ermitteln
     * Wird von "setArticleImages.php" genutzt
     * @global type $db
     * @param type $product_model
     * @return type
     */
    static function getProductIDByModel($product_model)
    {
        global $db;
        // SQL bauen: Produkt ID aus Tabelle TABLE_PRODUCTS anhand von products_model holen
        $sql = "select products_id from " . TABLE_PRODUCTS . " where products_model = '" . $product_model . "'";
        // Ersten Treffer in $productID mappen
        $productID = $db->getOne($sql);

        // wenn kein Treffer gefunden, dann false zurückgeben, sonst die Produkt ID
        return (empty($productID) ? false : $productID);
    }


    /**
     * CustomerId anhand seiner EMail Adresse aus DB ermitteln
     * Wird von "setCustomer.php" genutzt
     * @global type $db
     * @global type $filter
     * @param type $email_address EMailadresse von Kunden
     * @param type $store_id Shop ID in dem gesucht werden soll
     * @return type
     */
    static function getCustomersIDByEmail($email_address, $store_id)
    {
        global $db, $filter;

        // $email_address filtern
        $email = $filter->_filter($email_address);

        // SQL Teil für shop_id
        $store_sql = "and shop_id = " . (int)$store_id . "";
        // account_type darf nicht 1 sein (Gastkunde)
        $status_sql = "and account_type != 1";

        // SQL Abfrage bauen: customers_id aus Tabelle TABLE_CUSTOMERS ziehen anhand von Emailadresse / store_id und account_type
        $sql = "SELECT customers_id FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . $email . "' " . $store_sql . " " . $status_sql . "";

        // SQL Abfrage ausführen und ersten Treffer in $customers_id
        $customers_id = $db->getOne($sql);

        // wenn kein Treffer gefunden, dann false zurückgeben, sonst die Kunden ID
        return (empty($customers_id) ? false : $customers_id);
    }


    /**
     * Produkt ID anhand von externalId ODER productsModel ODER productID(!) ermitteln
     * Wird von "setArticle.php" und "setStocks.php" genutzt
     * @global type $db
     * @param type $externalID
     * @param type $productsModel
     * @param type $productID
     * @return boolean, wenn ProduktID NICHT ermittelt werden kann
     */
    static function getProductID($externalID = '', $productsModel = '', $productID = '')
    {
        global $db;
        // Wenn alle Parameter leer sind, dann false zurückgeben
        if (trim($externalID) == '' && trim($productsModel) == '' && trim($productID) == '') {
            return false;
        }

        // fix #312
        
        if ($productID) {
            $rs = $db->GetOne("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_id=?",array($productID));
            if ($rs) {
                return $productID;
            }
        }
        

        // wenn externalID gefüllt
        if ($externalID) {

            $externalID = str_replace("'", "''", $externalID);

        
            // Produkt ID anhand von external_id ermittlen aus Tabelle TABLE_PRODUCTS
            $sql = "select products_id from " . TABLE_PRODUCTS . " where external_id = '" . $externalID . "'";
            // Ersten Treffer in $productID
            $productID = $db->getOne($sql);
            // Wenn $productID leer, dann false zurück sonst $productID
            return (empty($productID) ? false : $productID);
        }
        // wenn $productsModel gefüllt
        if ($productsModel) {
            // Methode getProductIDByModel aufrufen und Wert zurückgeben
            return self::getProductIDByModel($productsModel);
        }

        // $productID Parameter selbst wieder zurückgeben
        return $productID;
    }

    //2015-11-05: Blackbit Anpassung
    static function getProductIDsByExternalID($externalIds)
    {
        /**
         * @var $db ADOConnection
         */
        global $db, $filter;
        $productIds = array();

        $ids = array();
        foreach ($externalIds as $externalId) {
            $ids[] = $filter->_filter($externalId);
        }

        if (!empty($ids)) {
            $productIds = $db->GetCol("SELECT `products_id` FROM " . TABLE_PRODUCTS . " WHERE `external_id` IN (" . join(',', $ids) . ")");
        }


        return $productIds;
    }


    //2015-11-05: Blackbit Anpassung

    /**
     * Content ID anhand von externer Content ID zurückgeben
     * Wird von "setContent.php" genutzt
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getContentIDByexternalID($external_id)
    {
        global $db, $filter;


        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select content_id from " . TABLE_CONTENT . " where external_id = '" . $external_id . "'";

        // Ersten Treffer in $categoryID schreiben
        $contentID = $db->getOne($sql);

        // Wenn keine KategorieID ermittlet werden konnte, dann false zurückgeben,
        // sonst $categoryID
        return (empty($contentID) ? false : $contentID);


    }

    static function getContentExternalIDByID($content_id)
    {
        global $db, $filter;


        // categories_id filtern
        $content_id = $filter->_filter($content_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select external_id from " . TABLE_CONTENT . " where content_id = " . $content_id;
        // Ersten Treffer in $categoryID schreiben
        $external_id = $db->getOne($sql);

        return (empty($external_id) ? false : $external_id);

    }


    /**
     * ContentBlock ID anhand von externer ContentBlock ID zurückgeben
     * Wird von "setContent.php" und "setContentBlock.php" genutzt
     * @global type $db
     * @global type $filter
     * @param type $external_id
     * @return type
     */
    static function getContentBlockIDByexternalID($external_id)
    {
        global $db, $filter;

        // ExternalID filtern
        $external_id = $filter->_filter($external_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select block_id from " . TABLE_CONTENT_BLOCK . " where external_id = '" . $external_id . "'";

        // Ersten Treffer in $categoryID schreiben
        $contentBlockID = $db->getOne($sql);

        // Wenn keine KategorieID ermittlet werden konnte, dann false zurückgeben,
        // sonst $categoryID
        return (empty($contentBlockID) ? false : $contentBlockID);
    }

    static function getContentBlockExternalIDByID($content_block_id)
    {
        global $db, $filter;


        // categories_id filtern
        $content_block_id = $filter->_filter($content_block_id);
        // SQL Abfrage für Tabelle TABLE_CATEGORIES bauen
        $sql = "select external_id from " . TABLE_CONTENT_BLOCK . " where block_id = " . $content_block_id;

        // Ersten Treffer in $categoryID schreiben
        $external_id = $db->getOne($sql);

        return (empty($external_id) ? false : $external_id);

    }


    /**
     * Hersteller ID anhand von Herstellernamen zurückgeben
     * query manufacturers ID by name
     *
     * @param mixed $manufacturers_name
     * @return mixed
     */
    static function getManufacturersIDByName($manufacturers_name)
    {
        global $db;
        // DB Abfrgaen und Hersteller ID in $id
        $id = $db->getOne("select manufacturers_id from " . self::getTablePrefix() . "manufacturers where manufacturers_name = '" . $manufacturers_name . "'");
        // wenn $id leer, dann "0" zurückgeben sonst $id
        return (!empty($id) ? $id : "0");
    }


    /**
     * Einen Filterwert von eine String zu einem Array umwandeln lassen
     * per regulärem Ausdruck.
     * Filter wird zerlegt zu "column", "operator" und "condition"
     * @param type $filterValue
     * @return boolean
     */
    static function filterValueTransformToArray($filterValue)
    {
        preg_match('/^(\w+)(?: *)([>|=|<]+)(?: *)([ A-Za-z0-9\"\':-]+)$/i', stripslashes($filterValue), $test);
        if (is_array($test) && count($test) == 3) {
            return array(
                'column' => $test[0],
                'operator' => $test[1],
                'condition' => $test[2]
            );
        }
        return false;
    }


    /**
     * Produkt anhand von Filtern holen
     * ACHTUNG: METHODE SCHEINT NICHT GENUTZT UND NICHT FERTIG ZU SEIN! KEIN RETURN!!!
     * Soll wohl von getOrdersListByStatus genutzt werden
     * TODO: PRÜFEN UND ENTFERNEN, WENN OK
     * @param type $filter
     */
    static function getProductByFilter($filter)
    {
        $where = array();
        foreach ((array)$filter as $k => $v) {
            $transformed = self::filterValueTransformToArray($v);
            if ($transformed) {
                $where[] = $transformed['column'] . " " . $transformed['operator'] . " " . $transformed['condition'];
            }
        }
        if (count($where)) {

        }
    }


    /**
     * Liste der orders holen
     * Wird von "getOrders.php" genutzt
     *
     * @global type $db
     * @param type $options optionales Array mit Filteroptionen für Abfrage
     * @param type $start offset für Abfrage
     * @param type $size blocksize der Abfrage
     * @param type $indivFieldsList Optional: IndivField Liste mit Zusatzfeldern aus Posten Tabelle. SQL Select wird dann erweitert
     * @return type
     * @throws Exception
     */
    static function getOrdersListByStatus($options = array(), $start, $size, $extNumberRangeCustomerId = 0, $indivFieldsList)
    {
        // Array für Abfragefilter
        $filter = array();

        // WHERE Bedingung der SQL Abfrage
        $where = "";

        // Array für bestimmte Filtern für den Bestellstatus
        $status_filter = [];

        // Ist $options ein gefülltes array?
        if (is_array($options) && count($options)) {

            // Über Filtereinträge in $options laufen
            foreach ((array)$options as $filterKey => $filterValue) {
                // Array für einzelnen Filter
                $f = array();

                // Backslashes entfernen
                $filterValue = stripslashes($filterValue);

                // $filterValue per regulären Ausdrücken zerlegen und in array $f schreiben
                // ACHTUNG: DIE FUNKTION stripslashes($filterValue) WIRD ERNEUT AUFGERUFEN! WARUM?
                // TODO: klären!
                // Hier soll wohl noch die Funktion filterValueTransformToArray genutzt werden
                // 2014-07-28: Filter erweitert um ".", damit email adr sauber durchlaufen
                preg_match('/^(\w+)(?: *)([>|=|<]+)(?: *)([ A-Za-z0-9\"\':\-@._]+)$/i', stripslashes($filterValue), $f);

                // ISt das array $f gefüllt worden (=hat die Zerlegung funktioniert)
                if (isset($f[1]) && isset($f[2]) && isset($f[3])) {

                    // DB Column steht in 1
                    $dbColumn = $f[1];
                    // Operator sthet in 2; wieder in Slashes einhüllen
                    $operator = addslashes($f[2]);
                    // Wert für den Operator
                    $dbColumnValue = $f[3];

                    $firstLetter = substr($dbColumnValue, 0, 1);

                    // Wenn erster Buchstabe ein ' oder ", dann dieses entfernen
                    if ($firstLetter == "'" || $firstLetter == '"') {
                        $dbColumnValue = substr($dbColumnValue, 1, strlen($dbColumnValue));
                    }

                    // Letzten Buchstaben von $dbColumnValue holen
                    $lastLetter = substr($dbColumnValue, -1);

                    // Wenn letzter Buchstabe ein ' oder ", dann dieses entfernen
                    if ($lastLetter == "'" || $lastLetter == '"') {
                        $dbColumnValue = substr($dbColumnValue, 0, strlen($dbColumnValue) - 1);
                    }

                    // switch über COlumnnamen
                    switch ($dbColumn) {
                        // Filtern nach "status" und "orders_status" (lösen das gleiche aus)
                        case 'status':
                            //orders_status=16,orders_status=23
                            //oldstat=17
                        case 'orders_status':
                            // $status_filter array füllen mit Column Wert
                            $status_filter[] = $dbColumnValue;
                            break;
                        // Filtern nach shop ID
                        case 'shop_id':
                            $filter[] = "o.shop_id  " . $operator . "  '" . $dbColumnValue . "'";
                            break;
                        // Filtern nach customer ID
                        case 'customers_id':
                            $filter[] = "o.customers_id  " . $operator . "  '" . $dbColumnValue . "'";
                            break;
                        // Filtern nach payment code
                        case 'payment':
                            $filter[] = "o.payment_code  " . $operator . "  '" . $dbColumnValue . "'";
                            break;
                        // Filtern nach Datum ("date" oder "date_purchased" )
                        case 'date':
                        case 'date_purchased':
                            $filter[] = "o.date_purchased " . $operator . " '" . $dbColumnValue . "'";
                            break;
                        // Filtern nach orders_exported
                        case 'exported':
                            $filter[] = "o.orders_exported " . $operator . " '" . $dbColumnValue . "'";
                            break;
                        // Filtern nach Felder in Haupttabelle
                        default:
                            $filter[] = "o." . $dbColumn . " " . $operator . " '" . $dbColumnValue . "'";
                            break;
                    }
                }
            }


            // Gibt es Abfragefilter?
            if (count($filter) > 0) {
                // WHERE Block um Filterausdrücker erweitern
                $where .= " where ";
                // Alle Filter per " and " verknüpfen und an $where packen
                $where .= implode(' and ', $filter);
            }

            // Gibt es StatusFilter?
            if (count($status_filter) > 0) {
                // Wurden schon "normale" Filter angehangen, dann WHERE um " and " erweitern
                if (count($filter) > 0) {
                    $where .= " and ";
                } else {
                    // Sonst where eröffnen
                    $where .= " where ";
                }
                // WHERE Block um alle status_filter erweitern
                $where .= " o.orders_status IN (" . implode(',', $status_filter) . ")";
            }
        } else {
            // Parameter $options ist KEIN array und leer, dann Exception werfen
            throw new Exception("filter options not set");
        }

        global $db;

        // Offset Wert für Abfrage zu int casten
        $start = (int)$start;
        // Blocksize der Abfrage zu int casten
        $size = (int)$size;

        // SQL Abfrage zusammenbauen aus Tabelle TABLE_ORDERS
        $sql = " select * from " . TABLE_ORDERS . " as o " . $where . "LIMIT " . $start . "," . $size;

        // SQL ausführen und Treffer in assoziatives Array umwandlen lassen
        $orders = $db->getAssoc($sql);

        // Alle order ids in array schreiben
        $orderIDs = array_keys($orders);

        // Über alle orders laufen
        foreach ($orders as $orderID => $orderData) {

            // Neues order Objekt bauen
            $order = new order($orderID, $orders[$orderID]['customers_id']);

            // Steueranteile setzen in Eintrag "total_tax" setzen
            foreach ($order->order_total['total_tax'] as $arr => $val) {
                $orders[$orderID]['total_tax'][]
                    = array(
                    'value' => round($val['tax_value']['plain'], 2),
                    'tax_rate' => $val['tax_key'],
                    'value_formated' => $val['tax_value']['formated']
                );
            }

            // Gesamtpreis formatiert und entspr. Zahlennotation
            $orders[$orderID]['total_price_formated'] = $order->order_total['total']['formated'];

            // Gesamtpreis unformatiert
            // <total_price xsi:type="xsd:decimal">5.6843418860808E-14</total_price>
            $total_price = $order->order_total['total']['plain'];
            if ($total_price < 0.01) {
                $total_price = 0;
            }

            $orders[$orderID]['total_price'] = $total_price;

            // total
            $orders[$orderID]['orders_products'] = self::getOrdersProduct($orderID, $orderIDs, $indivFieldsList);
            $orders[$orderID]['orders_total'] = self::getOrdersTotal($orderID, $orderIDs);
            $orders[$orderID]['delivery_country'] = $orders[$orderID]['delivery_country'];
            $orders[$orderID]['orders_id'] = $orderID;

            // external id rein: wenn null, dann berechnen aus $orders[$orderID]['customers_id'] + $extNumberRangeCustomerId;
            // external id von Kunden anhand seiner ID ermittlen lassen
            $external_id = self::getCustomersExternalId($orders[$orderID]['customers_id']);

            if ($external_id === false) {
                // External ID anhand von $extNumberRangeCustomerId berechnen
                $extId = $orders[$orderID]['customers_id'] + $extNumberRangeCustomerId;

                // external ID + customers_cid in DB schreiben
                if (XT_API_CUSTOMER_WRITE_BACK_EXT_ID == 'true') {
                    $db->Execute("UPDATE " . TABLE_CUSTOMERS . " SET external_id='" . $extId . "', customers_cid='" . $extId . "' WHERE customers_id=" . $orders[$orderID]['customers_id']);
                }
                $orders[$orderID]['customers_external_id'] = $extId;
                $orders[$orderID]['customers_cid'] = $extId;
            } else {
                // external_id VORHANDEN bei Kunde, dann setzen
                $orders[$orderID]['customers_external_id'] = $external_id;
            }


            // Datumscheck auf IsoDate
            $orders[$orderID]['order_date'] = self::checkIsoDate($orders[$orderID]['date_purchased']);
            $orders[$orderID]['last_modified'] = self::checkIsoDate($orders[$orderID]['last_modified']);
            $orders[$orderID]['date_purchased'] = self::checkIsoDate($orders[$orderID]['date_purchased']);

            // currency_value kann NULL sein, zb. durch magnalister plugin
            if ($orders[$orderID]['currency_value'] == NULL || strlen($orders[$orderID]['currency_value']) == 0) {
                $orders[$orderID]['currency_value'] = 0;
            }

            global $xtPlugin;
            ($plugin_code = $xtPlugin->PluginCode('SoapHelper.php:getOrdersListByStatus_orders_bottom')) ? eval($plugin_code) : false;

        }
        return $orders;
    }


    /**
     * Sicherheitsfunktion: Gibt es Nutzer mit Passwort in Tabelle TABLE_ADMIN_ACL_AREA_USER
     * Diese Funktion wird von allen calls aufgerufen
     *
     * @global type $db
     * @param type $user
     * @param type $password
     * @return type
     */
    static function secured($user, $password, $scope = '')
    {
        global $db;

        $bruto_force = new bruto_force_protection();

        if ($password != '' && $user != '') {

            if (!$bruto_force->_isLocked($user, 2)) {

                // query for user
                $query = "SELECT * FROM " . TABLE_XT_API_USER . " WHERE api_username=? and access_status='1'";

                $rs = $db->Execute($query, $user);

                if ($rs->RecordCount() != 1) {
                    $bruto_force->escalateLoginFail($user, '2', 'api_login');
                } else {

                    self::$ActivateLogging = $rs->fields['api_log_active'];
                    // validate if there is an IP list for this user

                    if (strlen($rs->fields['api_access_restrictions']) > 0) {
                        $ip = self::getCurrentIp();
                        $allowed_ip = explode(';', $rs->fields['api_access_restrictions']);
                        if (!in_array($ip, $allowed_ip)) return false;
                    }

                    // validate password
                    $pw_check = self::verify_api_password($password, $rs->fields['api_password']);
                    if ($pw_check) {
                        return true;
                    }

                    $bruto_force->escalateLoginFail($user, '2', 'api_login');
                }

            } else {
                return false;
            }
        }

        return false;
    }


    static function verify_api_password($pw_plain, $pw_hash)
    {

        if (password_verify($pw_plain, $pw_hash)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Order Stus holen
     * @global type $db
     * @staticvar type $orders_status
     * @param type $status
     * @param type $lang SparchCode (def = "de")
     * @return type
     */
    static function getOrderStatus($status, $lang = 'de')
    {
        // Statischer Cache
        static $orders_status;
        // Ist Cache noch nicht gefüllt?
        if (!$orders_status) {
            global $db;
            // DB Abfrage: Alle Stati aus Tabelle TABLE_SYSTEM_STATUS_DESCRIPTION abfragen
            // Dabei wird Spalte id_lang dynamisch aus Spalten status_id und language_code erzeugt
            $orders_status = $db->GetAssoc("select concat(status_id,'_',language_code) as id_lang ,status_name,status_image  from " . TABLE_SYSTEM_STATUS_DESCRIPTION);
        }

        return (isset($orders_status[$status . "_" . $lang])) ? true : false;
    }


    /**
     * Liste aller order items
     * @global type $db
     * @staticvar type $resultOrdersProduct
     * @param type $ordersID Bestell ID (erste BestellID als Key für Cache)
     * @param type $ordersIDslistIn Liste mit allen BestellIds
     * @return type
     * @throws Exception
     */
    static function getOrdersProduct($ordersID, $ordersIDslistIn = array(), $indivFieldsList)
    {
        // Statischer Cache
        static $resultOrdersProduct;
        // Ist Cache noch nicht gefüllt?
        if (!$resultOrdersProduct) {
            // Array $ordersIDslistIn muss Inhalt haben!
            if (!count($ordersIDslistIn)) {
                throw new Exception("List of orders IDs must given at first time!");
            }

            // SQL WHERE Part: Filter auf TABLE_ORDERS_PRODUCTS.orders_id mit allen Werten von
            // array $ordersIDslistIn
            $whereIn = " where op.orders_id in ('" . implode("','", $ordersIDslistIn) . "')";


            // 2016-03-01:
            // Werden zusätzliche indiv fields angefordert aus posten tab? Dann sql select dynmisch erweitern
            $indivSelect = "";

            if (is_array($indivFieldsList)) {
                // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
                foreach ($indivFieldsList as $key => $indivField) {
                    // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS"
                    if ($indivField["dstTable"] == "TABLE_ORDERS_PRODUCTS") {
                        // $indivField["value"] = "TEST"; //$items[  $indivField["sqlFieldName"] ];
                        $indivSelect .= ", op." . $indivField["sqlFieldName"];
                    }
                }
            }


            // SQL bauen mit join zwischen TABLE_ORDERS_PRODUCTS und TABLE_PRODUCTS um products_weight zu ermittlen
            $sql = "SELECT
				p.products_weight,
				op.orders_products_id,
				op.orders_id,
				op.products_id,
				op.products_model,
				op.products_name,
				op.products_price,
				op.products_discount,
				op.products_tax,
				op.products_tax_class,
				op.products_quantity,
				op.products_data,
				op.allow_tax,
				op.products_shipping_time";


            $sql .= $indivSelect;
            $sql .= " FROM " . TABLE_ORDERS_PRODUCTS . " as op left join " . TABLE_PRODUCTS . " as p on op.products_id=p.products_id " . $whereIn;

            global $db;
            // SQL Abfrage ausführen und Ergebnis in $tmp
            $tmp = $db->getAll($sql);

            // Neues array bauen
            $resultOrdersProduct = array();

            // Über Treffer der SQL Abfrage laufen
            foreach ((array)$tmp as $index => $orderItem) {
                // Einzelnes orderItem in zweidimensionales array packen
                $resultOrdersProduct[$orderItem['orders_id']][] = $orderItem;
            }
        }
        // Im Cache nachschauen, ob geüwnschter Eintrag vorhanden ($ordersID ist sozusagen der key vom cache Eintrag)
        if (isset($resultOrdersProduct[$ordersID])) {
            // Gewünschtes Tielarray aus cache ziehen
            return $resultOrdersProduct[$ordersID];
        }

        // Default: Leerarray zurückgeben - dh keine orders gefunden!
        return array();
    }


    /**
     *
     * @global type $db
     * @staticvar type $resultOrderTotal
     * @param type $ordersID
     * @param type $ordersIDslistIn
     * @return type
     * @throws Exception
     */
    static function getOrdersTotal($ordersID, $ordersIDslistIn = array())
    {
        // statischer Cache
        static $resultOrderTotal;

        // Cache noch leer, also füllen
        if (!$resultOrderTotal) {
            // SQL Anweisung
            $sql = "";

            // Array $ordersIDslistIn leer? Dann Exception werfen
            if (!count($ordersIDslistIn)) {
                throw new Exception("List of orders IDs must given at first time!");
            }
            global $db;

            // tmp Array für DB Abfrage
            $tmp = array();

            // WHERE Part der SQL Abfrage ( Liste aller orderIds als Filter)
            $whereIn = "where ot.orders_id in ('" . implode("','", $ordersIDslistIn) . "')";

            // SQL bauen
            $sql = "select * from " . self::getTablePrefix() . "orders_total as ot " . $whereIn;

            // Alle Treffer in $tmp
            $tmp = $db->getAll($sql);

            // Über alle Treffer in $tmp laufen
            foreach ((array)$tmp as $index => $totalItem) {

                // query for coupon code
                if ($totalItem['orders_total_key'] == 'xt_coupon') {
                    // Coupon Code für die Bestellung anziehen
                    $totalItem['orders_total_model'] = self::getCouponCode($totalItem['orders_id']);
                }

                if ($totalItem["orders_total_tax"] == null || strlen($totalItem["orders_total_tax"]) == 0) {
                    $totalItem["orders_total_tax"] = 0;
                }

                if ($totalItem["orders_total_tax_class"] == null || strlen($totalItem["orders_total_tax_class"]) == 0) {
                    $totalItem["orders_total_tax_class"] = 0;
                }

                if (strlen($totalItem['allow_tax']) == 0 || !((int)$totalItem['allow_tax'] == 0 || (int)$totalItem['allow_tax'] == 1)) {
                    $totalItem['allow_tax'] = 0;
                }

                // Alles in statischen cache
                $resultOrderTotal[$totalItem['orders_id']][] = $totalItem;
            }
        }

        // Rückgabe geforderte Werte oder Leerarray, falls nicht gefunden
        return (isset($resultOrderTotal[$ordersID])) ? $resultOrderTotal[$ordersID] : array();
    }

    /**
     * get coupon id for redeemed coupon
     * Coupon Id für eingelösten Coupon zurückgeben
     *
     * @param unknown_type $ordersID
     */
    static function getCouponCode($ordersID)
    {
        global $db;

        // check for redeemed coupon
        // SQl Query bauen aus Tabelle TABLE_COUPONS_REDEEM alle Felder ziehen für order_id. Nur erster Treffer / limitiert
        $qry = "SELECT * FROM " . TABLE_COUPONS_REDEEM . " WHERE order_id='" . $ordersID . "' LIMIT 0,1";

        // SQL Abfrage ausführen. Ergebnis in $rs
        $rs = $db->Execute($qry);

        // Gibt es genau einen Treffer?
        if ($rs->RecordCount() == 1) {

            // token or general ?

            // Ist coupon_token_id gesetzt?
            if ($rs->fields['coupon_token_id'] > 0) {
                // Ja, dann coupon_token_code aus Tabelle TABLE_COUPONS_TOKEN ermitteln und zurückgeben
                $qry = "SELECT * FROM " . TABLE_COUPONS_TOKEN . " WHERE coupons_token_id='" . $rs->fields['coupon_token_id'] . "'";
                $rrs = $db->Execute($qry);
                if ($rrs->RecordCount() == 1) {
                    return $rrs->fields['coupon_token_code'];
                }
            } else {
                // Nein, coupon_token_id nicht gesetzt
                $coupon_id = $rs->fields['coupon_id'];
                // Coupon_code aus Tabelle TABLE_COUPONS ermittlen und zurückgeben
                $qry = "SELECT coupon_code FROM " . TABLE_COUPONS . " WHERE coupon_id='" . $coupon_id . "'";
                $rrs = $db->Execute($qry);
                if ($rrs->RecordCount() == 1) {
                    return $rrs->fields['coupon_code'];
                }
            }
        }
    }


    /**
     * Wird von "getOrders.php" genutzt.
     * @global type $db
     * @param type $xtOrders Array mit Bestellungen
     * @return type
     */
    static function orderListTransform($xtOrders, $extNumberRangeDeliveryAdr = 0, $indivFieldsList)
    {
        global $db,$system_status,$xtPlugin;

        // load customers status infos
        $customers_status = array();
        // SQL: Alle Felder aus Tabelle TABLE_CUSTOMERS_STATUS abfragen
        $qry = "SELECT * FROM " . TABLE_CUSTOMERS_STATUS;
        // SQL ausführen
        $rs = $db->Execute($qry);
        // Über alle Treffer laufen, also komplette Tabelle
        while (!$rs->EOF) {
            // Array $customers_status füllen mit Tabellendaten mit key=customers_status_id
            $customers_status[$rs->fields['customers_status_id']] = $rs->fields;
            // zum nächsten Treffer in DB springen
            $rs->MoveNext();
        }

        // Über alle Bestellungen laufen - in $order mappen
        foreach ((array)$xtOrders as $i => $order) {

            // Hier werden die Daten aus dem array $xtOrders umgeschrieben in das XT Format

            $xtOrders[$i]['last_modified'] = self::checkIsoDate($order['last_modified']);
            $xtOrders[$i]['orders_status_text'] = $system_status->values['order_status'][$xtOrders[$i]['orders_status']]['name'];


            $xtOrders[$i]['delivery_data']['address_id'] = $order['billing_address_book_id'];
            $xtOrders[$i]['customer_data']['dob'] = self::getCustomersDoB($order['billing_address_book_id']);
            $xtOrders[$i]['customer_data']['firstname'] = $order['billing_firstname'];
            $xtOrders[$i]['customer_data']['name'] = $order['billing_lastname'];
            $xtOrders[$i]['customer_data']['street'] = $order['billing_street_address'];
            $xtOrders[$i]['customer_data']['address_addition'] = $order['billing_address_addition']; // 2018-07-12 NEU
            $xtOrders[$i]['customer_data']['gender'] = $order['billing_gender'];
            $xtOrders[$i]['customer_data']['title'] = $order['billing_title']; // 2018-07-12 NEU
            $xtOrders[$i]['customer_data']['email'] = $order['customers_email_address'];
            $xtOrders[$i]['customer_data']['city'] = $order['billing_city'];
            $xtOrders[$i]['customer_data']['suburb'] = $order['billing_suburb'];
            $xtOrders[$i]['customer_data']['company'] = $order['billing_company'];
            $xtOrders[$i]['customer_data']['company_2'] = $order['billing_company_2'];
            $xtOrders[$i]['customer_data']['company_3'] = $order['billing_company_3'];
            $xtOrders[$i]['customer_data']['country'] = $order['billing_country'];
            $xtOrders[$i]['customer_data']['country_code'] = $order['billing_country_code'];
            $xtOrders[$i]['customer_data']['federal_state_code'] = $order['billing_federal_state_code'];
            $xtOrders[$i]['customer_data']['federal_state_code_iso'] = $order['billing_federal_state_code_iso'];
            $xtOrders[$i]['customer_data']['phone'] = $order['billing_phone'];
            $xtOrders[$i]['customer_data']['mobile_phone'] = $order['billing_mobile_phone'];
            $xtOrders[$i]['customer_data']['fax'] = $order['billing_fax'];
            $xtOrders[$i]['customer_data']['zip'] = $order['billing_postcode'];
            $xtOrders[$i]['customer_data']['shop_custid'] = $order[''];
            $xtOrders[$i]['customer_data']['vat_id'] = $order['customers_vat_id'];
            $cid = $order['customers_cid'];
            if ($order['customers_cid'] == '' or $order['customers_cid'] == NULL) {
                $cid = $order['customers_id'];
            }
            $xtOrders[$i]['customer_data']['customers_cid'] = $cid;
            $xtOrders[$i]['tax_included'] = 0;

            // check payment method
            if ($order['payment_code'] == 'xt_banktransfer') {

                $_bank_data = unserialize($order['orders_data']);
                $_bank = array();
                $xtOrders[$i]['customer_data']['banktransfer_owner'] = $_bank_data['banktransfer_owner'];
                $xtOrders[$i]['customer_data']['banktransfer_blz'] = $_bank_data['banktransfer_blz'];
                $xtOrders[$i]['customer_data']['banktransfer_bank_name'] = $_bank_data['banktransfer_bank_name'];
                $xtOrders[$i]['customer_data']['banktransfer_number'] = $_bank_data['banktransfer_number'];
                $xtOrders[$i]['customer_data']['banktransfer_iban'] = $_bank_data['banktransfer_iban'];
                $xtOrders[$i]['customer_data']['banktransfer_bic'] = $_bank_data['banktransfer_bic'];
            }

            // 2018-06-28: Neu EK: Unterstützung von PayPal Plus abwärtskompatibel
            if (($order['payment_code'] == 'xt_mollie' || $order['payment_code'] == 'xt_paypal_plus') && isset($xtPlugin->active_modules['xt_bank_details'])) {

                if (!defined('TABLE_BAD_BANK_DETAILS')) {
                  require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/constants.php';
                }
                // SQL-Abfrage für xt_bank_details der aktuellen Bestellung bauen. JOIN über xt_order_bank_details
                $sql = "SELECT BBD.bad_add_data FROM " . TABLE_BAD_BANK_DETAILS . " as BBD
                            LEFT JOIN " . TABLE_BAD_ORDER_BANK_DETAILS . " as BOBD on BOBD.bad_bank_details_id = BBD.bad_id
                            WHERE BOBD.bad_orders_id = '" . $xtOrders[$i]['orders_id'] . "'";


                // SQL ausführen. Erster Treffer in _bank_data
                $_bank_data = unserialize($db->GetOne($sql));

                $xtOrders[$i]['customer_data']['banktransfer_owner'] = $_bank_data->recipient_banking_instruction->account_holder_name;
                $xtOrders[$i]['customer_data']['banktransfer_blz'] = '';
                $xtOrders[$i]['customer_data']['banktransfer_bank_name'] = $_bank_data->recipient_banking_instruction->bank_name;
                $xtOrders[$i]['customer_data']['banktransfer_number'] = '';
                $xtOrders[$i]['customer_data']['banktransfer_iban'] = $_bank_data->recipient_banking_instruction->international_bank_account_number;
                $xtOrders[$i]['customer_data']['banktransfer_bic'] = $_bank_data->recipient_banking_instruction->bank_identifier_code;
            }

            $xtOrders[$i]['tax_free'] = -1;
            if ($order['allow_tax'] == 0) { // Netto Bestellung
                if ($order['delivery_zone'] == 31) { // Kunde ist innerhalb der EU -> Gewerbekunde im EU Ausland
                    $xtOrders[$i]['tax_free'] = 1;
                } else { // Kunde ist nicht innerhalb der EU -> Drittland
                    $xtOrders[$i]['tax_free'] = -1;
                }
            } else { // Brutto Bestellung ->
                // Kunde in der EU && hat Firmennamen angegeben -> Gerwerbekunde mit Steuer
                if ($order['delivery_zone'] == 31 && strlen($order['delivery_company']) > 2) {
                    $xtOrders[$i]['tax_included'] = 0;
                    $xtOrders[$i]['tax_free'] = 0;
                }
            }

            $xtOrders[$i]['delivery_data']['address_id'] = $order['delivery_address_book_id'] + $extNumberRangeDeliveryAdr;
            $xtOrders[$i]['delivery_data']['dob'] = self::getCustomersDoB($order['delivery_address_book_id']);
            $xtOrders[$i]['delivery_data']['firstname'] = $order['delivery_firstname'];
            $xtOrders[$i]['delivery_data']['name'] = $order['delivery_lastname'];
            $xtOrders[$i]['delivery_data']['street'] = $order['delivery_street_address'];
            $xtOrders[$i]['delivery_data']['address_addition'] = $order['delivery_address_addition'];  // 2018-07-12 NEU
            $xtOrders[$i]['delivery_data']['gender'] = $order['delivery_gender'];
            $xtOrders[$i]['delivery_data']['title'] = $order['delivery_title']; // 2018-07-12 NEU
            $xtOrders[$i]['delivery_data']['city'] = $order['delivery_city'];
            $xtOrders[$i]['delivery_data']['suburb'] = $order['delivery_suburb'];
            $xtOrders[$i]['delivery_data']['company'] = $order['delivery_company'];
            $xtOrders[$i]['delivery_data']['company_2'] = $order['delivery_company_2'];
            $xtOrders[$i]['delivery_data']['company_3'] = $order['delivery_company_3'];
            $xtOrders[$i]['delivery_data']['country'] = $order['delivery_country'];
            $xtOrders[$i]['delivery_data']['country_code'] = $order['delivery_country_code'];
            $xtOrders[$i]['delivery_data']['federal_state_code'] = $order['delivery_federal_state_code'];
            $xtOrders[$i]['delivery_data']['federal_state_code_iso'] = $order['delivery_federal_state_code_iso'];
            $xtOrders[$i]['delivery_data']['phone'] = $order['delivery_phone'];
            $xtOrders[$i]['delivery_data']['mobile_phone'] = $order['delivery_mobile_phone'];
            $xtOrders[$i]['delivery_data']['fax'] = $order['delivery_fax'];
            $xtOrders[$i]['delivery_data']['zip'] = $order['delivery_postcode'];
            $xtOrders[$i]['shipping_data'] = self::getOrderTotalByKey($order['orders_total'], 'shipping', $xtOrders[$i]['tax_included']);
            $xtOrders[$i]['payment_data'] = self::getOrderTotalByKey($order['orders_total'], 'payment', $xtOrders[$i]['tax_included']);

            if (count($xtOrders[$i]['payment_data']) == 0) {
                $xtOrders[$i]['payment_data'] = self::buerowarePaymentData($order['payment_code'], $xtOrders[$i]['tax_included']);
            }
            // 2013-01-18: payment_data/id muss eine Zahl enthalten
            if ($xtOrders[$i]['payment_data']['id'] == null || strlen($xtOrders[$i]['payment_data']['id']) == 0) {
                $xtOrders[$i]['payment_data']['id'] = 0;
            }

            // INDIVFIELD auf xt_orders Ebene
            if (is_array($indivFieldsList)) {
                $indivFieldsListOrder = array();

                // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
                foreach ($indivFieldsList as $key => $indivField) {
                    if ($indivField["dstTable"] == "TABLE_ORDERS") {
                        $indivFieldClone = $indivField;
                        $indivFieldClone["value"] = $xtOrders[$i][$indivField["sqlFieldName"]];
                        $indivFieldsListOrder[] = $indivFieldClone;
                    }
                }
                $xtOrders[$i]["indivFieldsList"] = $indivFieldsListOrder;
            }

            // Positionen der einzelnen aktuellen Bestellung
            $xtOrders[$i]['items'] = self::buerowareProductsItemsTransform($order['orders_products'], $xtOrders[$i]['tax_included'], $indivFieldsList);
            $xtOrders[$i]['count_items'] = count($xtOrders[$i]['items']);

            // customers status infos
            $xtOrders[$i]['customers_status_show_price_tax'] = $customers_status[$xtOrders[$i]['customers_status']]['customers_status_show_price_tax'];
            $xtOrders[$i]['customers_status_add_tax_ot'] = $customers_status[$xtOrders[$i]['customers_status']]['customers_status_add_tax_ot'];


            // Wenn im Feld "orders_data" keine Daten gefunden wurden, in der Tabelle TABLE_CALLBACK_LOG nach einer Transaction ID suchen
            if ($xtOrders[$i]['orders_data'] == NULL || strlen($xtOrders[$i]['orders_data']) == 0) {

                // SQL Abfrage für Tabelle TABLE_CALLBACK_LOG bauen
                $sql = "SELECT transaction_id FROM " . TABLE_CALLBACK_LOG . " WHERE orders_id = '" . $xtOrders[$i]['orders_id'] . "' ORDER BY created ";

                // Ersten Treffer der SQL-Abfrage in das Feld "orders_data" mappen
                $xtOrders[$i]['orders_data'] = $db->GetOne($sql);
            }
        }
        return $xtOrders;
    }


    /**
     * DateOfBirth / Geburtsdatum von Kunden ermittlen als ISO Datum yyyy-MM-ddThh:mm:ss
     * @global type $db
     * @param type $address_book_id ID von Kunden in Adressbuchtabelle TABLE_CUSTOMERS_ADDRESSES
     * @return type
     */
    static function getCustomersDoB($address_book_id)
    {
        global $db;
        // DB bbfragen
        $rs = $db->Execute("SELECT customers_dob FROM " . TABLE_CUSTOMERS_ADDRESSES . " WHERE address_book_id='" . $address_book_id . "'");

        // wenn es genau einen Treffer gibt
        if ($rs->RecordCount() == 1) {
            $dob = $rs->fields['customers_dob'];

            // check Iso Date
            $dob = self::checkIsoDate($dob);

            return $dob;

        } else {
            // Kein Treffer in DB, dann default Datum zurück
            return "1970-01-01T00:00:00";
        }
    }

    /**
     * Funktion: Anhand von payment_code die payment_id aus Tabelle TABLE_PAYMENT ermitteln
     * @global type $db
     * @param type $payment_code
     * @param type $tax_included
     * @return type
     */
    static function buerowarePaymentData($payment_code, $tax_included)
    {
        global $db;
        $rs = $db->Execute("SELECT payment_id FROM " . TABLE_PAYMENT . " WHERE payment_code='" . $payment_code . "'");

        $payment = array('id' => $rs->fields['payment_id']);
        return $payment;
    }


    /**
     * BW Funktion: Produkt Infos von BW Format in XT Format umwandlen
     * @global type $db
     * @global type $price
     * @param type $orders_products
     * @param type $tax_included
     * @return type
     */
    static function buerowareProductsItemsTransform($orders_products, $tax_included, $indivFieldsList)
    {
        global $db, $price;

        $bureowareItems = array();


        foreach ((array)$orders_products as $i => $items) {
            if ($items['products_tax'] == null || strlen($items['products_tax']) == 0) $items['products_tax'] = 0;

            $items['products_price_otax'] = round($items['products_price'], 4);
            if ($tax_included == 1) { // add tax to value
                $items['products_price'] = $items['products_price'] + $items['products_price'] / 100 * $items['products_tax'];
                $items['products_price'] = round($items['products_price'], 4);
            }
            $items['products_price_sum'] = $items['products_price'] * $items['products_quantity'];

            $data = '';
            $options_full = array();
            $options = array();
            if (isset($items['products_data'])) {
                $item_data = unserialize($items['products_data']);
                if (is_array($item_data['options'])) {
                    // $options = array();
                    //  $options_full=array();
                    foreach ($item_data['options'] as $key => $val) {
                        $options[] = $val['option_group_name'] . ' - ' . $val['option_value_name'] . ' - ' . $val['option_value_value'];

                        // calculate
                        if ($tax_included == 1) { // add tax to value
                            $val['option_price'] = $val['option_price'] + $val['option_price'] / 100 * $items['products_tax'];
                            $val['option_price'] = round($val['option_price'], 4);
                        }
                        $options_full[] = $val;
                    }
                    $data = implode("\n", $options);
                }


                // 2018-08-15 EK Unterstützung 4TFM Additional Product Options
                $i = 0;
                if (is_array($item_data['tfm_options'])) {
                    // $options = array();
                    //  $options_full=array();
                    foreach ($item_data['tfm_options'] as $key => $val) {
                        $options[] = $val['option_desc'] . ' - ' . $val['value_desc'];

                        $options_full[$i]['option_group_id'] = $val['option'];
                        $options_full[$i]['option_value_id'] = $val['value'];
                        $options_full[$i]['option_group_desc'] = $val['option_desc'];
                        $options_full[$i]['option_value_desc'] = $val['value_desc'];
                        $options_full[$i]['option_price'] = $val['surcharge'];

                        $i++;
                    }
                    $data = implode("\n", $options);
                }
            }


            $rs = $db->Execute("SELECT external_id FROM " . TABLE_PRODUCTS . " WHERE products_id='" . $items['products_id'] . "'");
            $external_id = $rs->fields['external_id'];


            $original_price = $items['products_price'];
            $discount = 0;

            if ($items['products_discount'] > 0) {
                $discount = $items['products_discount'];
                $original_price = $original_price / (1 - ($discount / 100));
            }

            // applied_discount_percent bzw $items['products_discount'] darf NICHT leer sein!
            if ($items['products_discount'] == "") {
                $items['products_discount'] = 0;
            }

            // products_weight darf NICHT leer sein!
            if ($items['products_weight'] == "") {
                $items['products_weight'] = 0;
            }

            // Indiv Fields
            if (is_array($indivFieldsList)) {
                // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
                foreach ($indivFieldsList as $key => $indivField) {
                    // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS"
                    if ($indivField["dstTable"] == "TABLE_ORDERS_PRODUCTS") {
                        $indivFieldsList[$key]["value"] = $items[$indivField["sqlFieldName"]];
                    } else {
                        unset ($indivFieldsList[$key]);
                    }
                }
            }

            $bureowareItems[] = array(
                'products_id' => $items['products_id'],
                'products_model' => $items['products_model'],
                'external_id' => $external_id,
                'price_sum' => $items['products_price_sum'],
                'name' => $items['products_name'],
                'item_data' => $data,
                'options_full' => $options_full,
                'quantity' => $items['products_quantity'],
                'tax' => $items['products_tax'],
                'tax_class' => $items['products_tax_class'],
                'weight' => $items['products_weight'],
                'applied_discount_percent' => $items['products_discount'],
                'original_price' => $original_price,
                'price' => $items['products_price'],
                'price_otax' => $items['products_price_otax'],
                'indivFieldsList' => $indivFieldsList
            );
        }
        return $bureowareItems;
    }


    /**
     * Gesamtpreis einer Bestellung ermittlen für eine bestimmte Art
     * @param type $orderTotal
     * @param type $orders_total_key
     * @param type $tax_included
     * @return type
     */
    static function getOrderTotalByKey($orderTotal, $orders_total_key = 'shipping', $tax_included)
    {
        // orders_total_id,orders_id,orders_total_model,orders_total_tax_class,orders_total_quantity,allow_tax'];
        $return = array();
        foreach ((array)$orderTotal as $index => $total) {

            if ($total['orders_total_key'] == $orders_total_key) {
                if ($tax_included == 1) { // add tax to value
                    $total['orders_total_price'] = $total['orders_total_price'] + $total['orders_total_price'] / 100 * $total['orders_total_tax'];
                    $total['orders_total_price'] = round($total['orders_total_price'], 4);
                }

                $return['id'] = $total['orders_total_key_id'];
                $return['cost'] = round($total['orders_total_price'], 2);
                $return['type'] = $total['orders_total_name'];
                $return['tax'] = $total['orders_total_tax'];

                return $return;
            }
        }
        return array();
    }


    /**
     * Tax Id anhand der tax rate ermittlen
     * TODO: HIER WURDE WOHL ERST EIN CACHE MECHANISMUS GEPLANT; DANN ABER NICHT IMPLEMENTIERT.
     * NACHFRAGEN: WARUM?
     * @global type $db
     * @param type $rate
     * @return string
     */
    static function getTaxID($rate)
    {
        $taxMapping = array();
        global $db;
        $sql = "select tax_class_id, tax_rate from " . self::getTablePrefix() . "tax_rates";
        $all = $db->getAll($sql);
        for ($i = 0; $i < count($all); $i++) {
            $taxMapping[(int)$all[$i]['tax_rate']] = $all[$i]['tax_class_id'];
        }
        if (isset($taxMapping[$rate]))
            return $taxMapping[$rate];
        return "0";
    }

    /**
     *    Loeschen aller Image zuordnungen zu einem Produkt
     *
     * @param int $product_id
     */
    static function clearMediaImageLinkFromProduct($product_id)
    {
        global $db;
        $sql = "DELETE FROM " . TABLE_MEDIA_LINK . " WHERE `class`='product' AND `type`='images' AND `link_id`='" . (int)$product_id . "'";
        $db->Execute($sql);
    }


    /**
     *    Loeschen aller Image zuordnungen zu einem Element
     * @todo Prüfen wo die Methode "clearMediaImageLinkFromProduct" verwendet wird und umschreiben auf diese allgemeingültige Version, danach die spezial Methode entfernen.
     *
     * @param int $element_id
     * @param string $element_class
     */
    static function clearMediaImageLinkFromElement($element_id, $element_class)
    {
        global $db;
        $sql = "DELETE FROM " . TABLE_MEDIA_LINK . " WHERE `class`='" . $element_class . "' AND `type`='images' AND `link_id`='" . (int)$element_id . "'";
        $db->Execute($sql);
    }


    /**
     * Tabellen Prefix zurückgeben
     * @return type
     */
    static function getTablePrefix()
    {
        // wenn DB_PREFIX gesetzt, dann DB_PREFIX + "_" zurückgeben,
        // sonst Leerstirng
        return ((strlen(DB_PREFIX)) ? DB_PREFIX . '_' : '');
    }

    /**
     * Formatierte Debugmeldung erzeugen aus message und einem array
     * @param type $message
     * @param type $array
     * @return string
     */
    static function getDebugOut($message, $array)
    {
        $debugOut = "";
        $debugOut .= $message . ":\n" . print_r($array, true) . "\n";
        return $debugOut;
    }


    /**
     * Gibt System-Tabellenname für eine Tabelle zurück
     * @global type $db
     * @param type $tableName
     * @return boolean
     */
    static function getTableName($tableName)
    {
        // ist statische Variable $tablesInDb gefüllt?
        if (!self::$tablesInDb) {
            // Nein, dann alle Tabellennamen vom System in Variable $tablesInDb schreiben

            global $db;
            $all = $db->getAll("SHOW TABLES");
            foreach ((array)$all as $i => $array) {
                $table = array_shift($array);
                self::$tablesInDb[$table] = $table;
            }
        }
        // Gibt es in asso. Array $tablesInDb eine Eintrag mit Namen = $tableName?
        if (isset(self::$tablesInDb[$tableName])) {
            // Tabellendaten zurückgeben
            return self::$tablesInDb[$tableName];
        }
        // Kein Eintrag gefunden für $tableName, also false zurückgeben
        return false;
    }


    /**
     * Hilfsfunktion zum Generieren von HTML Eingabefeldern
     * für alle Felder einer bestimmten Tabelle ($table)
     *
     * Testmaske sollte so aufegrufen werden:
     * http://localhost/veyton/index.php?page=xt_soap&genarateTest=TABELLENNAME
     * zb http://localhost/veyton/index.php?page=xt_soap&genarateTest=tax_rates
     * ACHTUNG: Schreibfehler in genarateTest!!!
     *
     * @global type $db
     * @param type $table
     */
    static function genarateInputFields($table)
    {
        $sql = "describe " . SoapHelper::getTablePrefix() . $table;
        global $db;
        $data = $db->getAll($sql);
        $test = "";
        foreach ($data as $index => $def) {
            $aHits = array();
            preg_match('/^[a-z]*/', $def['Type'], $aHits);
            if (is_array($aHits) && count($aHits) > 0) {
                $_type = $aHits[0];
            } else {
                die("Could not get mapping for the MysqlType:" . $def['Type']);
            }
            $type = "text";
            $value = $def['Default'];
            switch ($_type) {
                case "text":
                case "longtext":
                    $inner = "<textarea  class='" . $type . "' name='" . $table . "[" . $def['Field'] . "]' rows='3' cols='19'>" . $value . "</textarea>";
                    break;
                default:
                    $inner = "<input type='" . $type . "' class='" . $type . "' name='" . $table . "[" . $def['Field'] . "]' value='" . $value . "' />";
                    break;
            }
            $test .= "
					<p><b>" . $table . "::" . $def['Field'] . "</b>" . $inner . "</p>";
        }
        echo "<pre>";
        echo htmlentities($test);
        echo "</pre>";
    }

    static function getCurrentIp()
    {
        $admin_ip = '::1';

        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $admin_ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR")) {
            $admin_ip = getenv("REMOTE_ADDR");
        }

        return $admin_ip;
    }


    /**
     * Hilfsmethode baut anhand von Tabellendefinition die SoapDatenTypen für diese Tabelle auf.
     * Wird aufgerufen per:
     * http://localhost/veyton/index.php?page=xt_soap&def_table=TABELLENNAME
     *
     * zB http://localhost/veyton/index.php?page=xt_soap&def_table=tax_rates
     *
     * So kann für jede Tabelle im System einfach automatisch das SOAP Datenmodell ausgegeben werden.
     *
     *
     * @global type $db
     * @param type $table
     */
    static function SoapTypeGenerator($table)
    {
        $sql = "describe " . SoapHelper::getTablePrefix() . $table;
        global $db;
        $data = $db->getAll($sql);
        echo "<pre>
				array( //begin- " . $table . "Item base
					'name'=>'" . $table . "Item',
					'typeClass'=>'complexType',
					'phpType'=>'struct',
					'compositor'=>'all',
					'restrictionBase'=>'',
					'elements'=>array(

				\n";
        foreach ($data as $index => $def) {
            $aHits = array();
            preg_match('/^[a-z]*/', $def['Type'], $aHits);
            if (is_array($aHits) && count($aHits) > 0) {
                $type = $aHits[0];
            } else {
                die("Could not get mapping for the MysqlType:" . $def['Type']);
            }
            switch ($type) {
                case "varchar":
                case "char":
                case "text":
                case "enum":
                case "longtext":
                    $sElementType = "string";
                    break;
                case "int":
                case "tinyint":
                case "bigint":

                    $sElementType = "int";
                    break;
                case "decimal":
                    $sElementType = "decimal";
                    break;
                case "datetime":
                case "timestamp":
                    $sElementType = "dateTime";
                    break;
                default:
                    die("unknown-type:" . $type);
                    break;
            }
            echo "					'" . $def['Field'] . "' => array('name'=>'" . $def['Field'] . "','type'=>'xsd:" . $sElementType . "'),\n";
        }
        echo "), \n'attrs'=>array(),\n'arrayType'=>''), //end - " . $table . "Item base";

        echo "</pre>";
    }


    /**
     * Datumsangabe prüfen und evtl. anpassen
     * @param type $date
     * @return string
     */
    static function checkIsoDate($date)
    {
        // Datum zu kurz oder leer, kann kein gültiges iso datum sein
        if (strlen($date) < 7) {
            return "1970-01-01T00:00:00";
        }

        // fängt im anno "0000" an...ist ungültig. mySQL macht es trotzdem....die lappen!
        if (strpos($date, "0000-", 0) === 0) {
            return "1970-01-01T00:00:00";
        }
        if (strpos($date, "0001-", 0) === 0) {
            return "1970-01-01T00:00:00";
        }

        // fehlendes T ersetzen
        return str_replace(' ', 'T', $date);
    }


    /**
     * Hilfsfunktion um bestimmte Steuerzeichen auszufiltern in Beschreibungstexten
     * Diese Steuerzeichen zerstören sonst XML
     * @param type $text
     * @return type
     */
    static function fixUnvalidChars($text)
    {

        $search = array(chr(1), chr(2), chr(3), chr(4), chr(7), chr(28), chr(29), chr(30), chr(31));
        $replace = array('', '', '', '', '', '', '', '', '');

        return str_replace($search, $replace, $text);

    }

}
    /**
     * Wandelt String oder Array in ISO-8859-1 Encoding um
     * @param type $data
     * @return type
     */
    function xtSoapCharsetConvert($data)
    {
        // wenn $data = array, dann
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                // Rekursion mit jedem einzelnen Eintrag
                $data[$key] = xtSoapCharsetConvert($val);
            }
        } else {
            // String umwandeln
            $data = utf8_decode($data);
        }
        return $data;
    }

    /**
     * Hilfsfunktion
     * @param type $id
     * @return type
     */
    function _modifierCustomersID($id)
    {
        return $id + XT_SOAP_MODIFIER_CUSTOMERS_ID;
    }

    /**
     * Hilfsfunktion
     * @param type $id
     * @return type
     */
    function _modifierAddressBookID($id)
    {
        return $id + XT_SOAP_MODIFIER_ADDRESSBOOK_ID;
    }


    /**
     * Hilfsfunktion: Restrukturierung eines Multisprachfeldes
     * @param type $data ACHTUNG: Hier wird eine Referenz und keine Kopie vom Array genutzt!!!!
     * @param type $fieldName
     * @param type $newFieldName
     */
    function reorgLangField(&$data, $fieldName, $newFieldName)
    {

        // hier wird der aufbau von mehrsprachfeldern umgedreht von
        // feld/de/....
        // zu "de/feld/..."feld_de"


        // Quellarray - über alle Sprachen laufen
        foreach ((array)$data[$fieldName] as $lang => $value) {
            // remap in $data
            $data[$newFieldName . "_" . $lang] = SoapHelper::utf8helper($value);

        }

        // alte Form entfernen
        unset($data[$fieldName]);

    }

    /**
     * Hilfsfunktion: Restrukturierung eines Multisprachfeldes
     * @param type $data ACHTUNG: Hier wird eine Referenz und keine Kopie vom Array genutzt!!!!
     * @param type $fieldName
     * @param type $newFieldName
     */
    function reorgLangFieldStores(&$src_data, $fieldName, &$dst_data, $newFieldName, $store_id)
    {

        // hier wird der aufbau von mehrsprachfeldern umgedreht von
        // feld/de/....
        // zu "de/feld/..."feld_de"


        // 2014-12-01 TODO: store_id ist jetzt mit eincodiert!
        // data['products_name_'.$stor_f.$val['code']]);

        // Bsp: products_name_store1_de


        // über alle store ids laufen
        // Quellarray - über alle Sprachen laufen
        foreach ((array)$src_data[$fieldName] as $lang => $value) {
            // remap in $data
            $dst_data[$newFieldName . "_store" . $store_id . "_" . $lang] = SoapHelper::utf8helper($value);

        }


    }


    /**
     * Hilfsfunktion
     * @param type $value
     * @param type $type
     * @param type $defVal
     * @return type
     */
    function getValue($value, $type, $defVal)
    {
        switch ($type) {
            case "xsd:decimal":
            case "xsd:int":
                if (!is_numeric($value)) return $defVal;
                break;

            case "xsd:dateTime":
                if (strlen($value) < 4) return $defVal;

                // ungültiges mySQL Datum abfangen
                if ($value == "0000-00-00 00:00:00") return $defVal;

                // Datum zu ISO umwandeln
                $value = str_replace(' ', 'T', $value);

                break;

            case "xsd:string":
                // wenn XT Soap Charset = ISO-8859-1, dann alle Einträge umwandeln zu UTF-8
                if (XT_API_CHARSET == 'ISO-8859-1') {
                    $value = xtSoapCharsetConvert($value);
                }

                // 2014-02-04: Per SOAP Helper evtl. vorhandene Steuerezichen ausfiltern!
                $value = SoapHelper::fixUnvalidChars($value);


                break;
        }

        return $value;
    }


    /**
     * Hilfsfunktion
     * @param type $haystack
     * @param type $needle
     * @param type $case
     * @return type
     */
    function startsWith($haystack, $needle, $case = true)
    {
        if ($case)
            return strpos($haystack, $needle, 0) === 0;

        return stripos($haystack, $needle, 0) === 0;
    }

    /**
     * Hilfsfunktion
     * @param type $haystack
     * @param type $needle
     * @param type $case
     * @return type
     */
    function endsWith($haystack, $needle, $case = true)
    {
        $expectedPosition = strlen($haystack) - strlen($needle);

        if ($case)
            return strrpos($haystack, $needle, 0) === $expectedPosition;

        return strripos($haystack, $needle, 0) === $expectedPosition;
    }
