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

// Einbinden, sonst findet PHP diese Klassen nicht
require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.product_to_mastercat.php');
require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.product_to_cat.php');

// Nur einbinden, wenn PlugIn xt_master_slave installiert und aktiviert
if (isset($xtPlugin->active_modules['xt_master_slave'])) {
    require_once(_SRV_WEBROOT . 'plugins/xt_master_slave/classes/class.product_to_attributes.php');
}

/**
 *
 * @global type $db
 * @global type $xtPlugin
 * @param type $user
 * @param type $pass
 * @param array $ProductItem
 * @return string
 */
function setArticle($user = '', $pass = '', $ProductItem = '') {
    global $store_handler;


    // hat user Zugriff?
    $hasAccess = SoapHelper::secured($user, $pass);
    $Exception = "";
    if (!$hasAccess) {
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageLoginFailed;
        $result['http_response_code'] = 401;
        return $result;
    }


    // ist $ProductItem kein array?
    if (!is_array($ProductItem)) {
        // Dann Fehlermeldung bauen und raus
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " ProductItem no array";
        return $result;
    }

    global $db, $xtPlugin;

    //Hookpoint Top
    ($plugin_code = $xtPlugin->PluginCode('setArticle.php:setArticle_top')) ? eval($plugin_code) : false;

    // Daten mappen über product Klasse
    // Hersteller Mappen
    if ($ProductItem['manufacturers_ext_id'] != null && strlen($ProductItem['manufacturers_ext_id']) > 0) {
        $manuID = SoapHelper::getManufacturersIDByexternalID($ProductItem['manufacturers_ext_id']);
    } else if ($ProductItem['manufacturers_int_id'] != null && $ProductItem['manufacturers_int_id'] > 0) {
        $manuID = $ProductItem['manufacturers_int_id'];
    }

    // Hersteller ID einmappen
    $ProductItem['manufacturers_id'] = $manuID;

    // $data als Kopie von $ProductItem erzeugen
    // ACHTUNG: Dies ist das Hauptdaten Array für die _set Methode von product!!!!
    $data = $ProductItem;

    // MASTER_FLAG Affenproblem...siehe
    $data['products_model_old'] = $data['products_model'];

    // PRODUCTS_PRICE merken
    // ... da $data['products_price'] und $ProductItem['products_price'] manipuliert wird (Bug bei Brutto-Admin Einstellung)
    $productsprice = $data['products_price'];


    // PRODUCTS_STATUS merken
    // ... da $data['products_status'] manipuliert wird
    $products_status = $data['products_status'];


    // Hier die Mehrsprachenfelder umsetzen in die XT Struktur - > einmappen in $data und dann alte Sprachenstruktur entfernen!
    // 2014-12-01: Anpassung auf multiStore Beschreibungen mit Fallback auf alte WSDL
    // Neues array 'productDescriptionsList' für Beschreibungen wird genutzt
    if ($data['productDescriptionsList'] && sizeof($data['productDescriptionsList']) > 0) {

        // über alle einzelnen descritionItems laufen
        foreach ($data['productDescriptionsList'] as $productDescriptionsItem) {

            reorgLangFieldStores($productDescriptionsItem, "products_keywords", $data, "products_keywords", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "products_description", $data, "products_description", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "products_short_description", $data, "products_short_description", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "products_name", $data, "products_name", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "meta_description", $data, "meta_description", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "meta_title", $data, "meta_title", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "meta_keywords", $data, "meta_keywords", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "seo_url", $data, "url_text", $productDescriptionsItem['products_store_id']);
            reorgLangFieldStores($productDescriptionsItem, "url", $data, "products_url", $productDescriptionsItem['products_store_id']);

            // indiv
            if (is_array($productDescriptionsItem["indivFieldsList"]) ) {
                // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
                foreach ($productDescriptionsItem["indivFieldsList"] as $key => $indivField) {

                    if (isset($indivField["dstTable"]) && $indivField["dstTable"] == "TABLE_PRODUCTS_DESCRIPTION" ) {
                        // hier das langItem nehmen
                        // Über alle Sprachen laufen und in $product_descriptions einmappen

                        if (is_array($indivField["langValue"])) {
                           reorgLangFieldStores($indivField, "langValue", $data, $indivField["sqlFieldName"] , $productDescriptionsItem['products_store_id']);
                        }
                    }
                }
            }
        }

        // Liste raus aus struktur
        unset($data['productDescriptionsList']);
    } else {
        // Fallback auf alte WSDL mit Beschreibungen in allen Sprachen aber nicht pro shop
        // Dann für alle Shops die gleichen Texte setzen
        // über alle stores laufen
        $stores = $store_handler->getStores();
        foreach ($stores as $store) {

            //
            // Sprachfelder Struktur umbauen pro store
            //
            reorgLangFieldStores($data, "products_keywords", $data, "products_keywords", $store['id']);
            reorgLangFieldStores($data, "products_description", $data, "products_description", $store['id']);
            reorgLangFieldStores($data, "products_short_description", $data, "products_short_description", $store['id']);
            reorgLangFieldStores($data, "products_name", $data, "products_name", $store['id']);
            reorgLangFieldStores($data, "meta_description", $data, "meta_description", $store['id']);
            reorgLangFieldStores($data, "meta_title", $data, "meta_title", $store['id']);
            reorgLangFieldStores($data, "meta_keywords", $data, "meta_keywords", $store['id']);
            reorgLangFieldStores($data, "seo_url", $data, "url_text", $store['id']);
            reorgLangFieldStores($data, "url", $data, "products_url", $store['id']);
        }
    }

    // unset von Feldern in original WSDL Struktur
    unset($data["products_keywords"]);
    unset($data["products_description"]);
    unset($data["products_short_description"]);
    unset($data["products_name"]);
    unset($data["meta_description"]);
    unset($data["meta_title"]);
    unset($data["meta_keywords"]);
    unset($data["seo_url"]);
    unset($data["products_url"]);

    //
    // Datumsangaben umformatieren
    //
    if( strpos($data['last_modified'],"0000-", 0) === 0 || strpos($data['last_modified'],"0001-", 0) === 0){
        unset($data['last_modified']);
    }
    else{
        reformatDate($data, "last_modified");
    }

    reformatDate($data, "date_available");
    reformatDate($data, "date_added");


    // ist products_master_flag NICHT gesetzt?
    if (!isset($data['products_master_flag']))
    // products_master_flag auf 0
        $data['products_master_flag'] = 0;

    // Ist products_master_flag = "J" dann auf 1 setzen
    if (strcmp($data['products_master_flag'], "J") == 0) {
        $data['products_master_flag'] = 1;
    }

    // wenn products_status NICHT gesetzt, dann auf 1
    if (!isset($data['products_status'])){
        $data['products_status'] = 1;
    }

    // Individualfelder einmappen
    if (is_array($data["indivFieldsList"])) {
        $stores = $store_handler->getStores();

        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach ($data["indivFieldsList"] as $key => $indivField) {
            // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS"
            if ($indivField["dstTable"] == "TABLE_PRODUCTS") {
                // value mappen in Product
                $data[$indivField["sqlFieldName"]] = SoapHelper::utf8helper($indivField["value"]);
            }

            // in $product zumappen, wenn Zieltabelle = "TABLE_PRODUCTS_DESCRIPTION"
            else if ($indivField["dstTable"] == "TABLE_PRODUCTS_DESCRIPTION") {
                // hier das langItem nehmen
                // Über alle Sprachen laufen und in $product_descriptions einmappen

                if (is_array($indivField["langValue"])) {
                    foreach ($stores as $store) {
                        reorgLangFieldStores($indivField, 'langValue', $data, $indivField["sqlFieldName"], $store['id']);
                    }
                }
            }
        }
    }


    // Product ID identifizieren
    /*
     * prio 1: external_id
     * prio 2: products_model
     * prio 3: products_id
     */
    $productID = SoapHelper::getProductID($data['external_id'], $data['products_model'], $data['products_id']);

    $set_type = "edit";

    // Produkt Neu?
    if (false === $productID) {

        // Ja, neues Produkt, also noch keine products_id bekannt !
        unset($data['products_id']);

        $set_type = "new";
    } else {
        // Update
        $data['products_id'] = $productID;

        // date_added merken
        $record = $db->Execute("SELECT date_added FROM ".TABLE_PRODUCTS." WHERE products_id=". $productID);
        $date_added_old = $record->fields["date_added"];

        // Soll "alte" SEO URL gerettet werden?
        if (XT_API_REBUILD_SEO_URLS == 'false') {
            $record = $db->Execute("SELECT link_id,language_code,url_text,store_id FROM " . TABLE_SEO_URL . " WHERE link_type=1 AND link_id=" . $productID);
            while (!$record->EOF) {

                $data['url_text_store' . $record->fields["store_id"] . "_" . $record->fields["language_code"]] = $record->fields["url_text"];

                $record->MoveNext();
            }$record->Close();
        }
    }

    $data['products_price'] = buildPrice($productsprice, $data['products_tax_class_id']);

    // Neues product Objekt anlegen
    $productObj = new product;

    // position auf "admin" setzen, damit _set funktioniert
    $productObj->setPosition('admin');

    // Neu: $store_field_exists setzen
    $productObj->store_field_exists = true;

    // _set von product aufrufen und Antwort holen
    $setResp = $productObj->_set($data, $set_type);
 

    // wenn Produkt Neu angelegt wurde, dann ist neue ID in $setResp["new_id"]
    if ($set_type == "new") {
        $productID = $setResp->new_id;
        $data['products_id'] = $productID;
    }

    // Spezial Individualfelder direkt in MySQL schreiben
    // Gibt es eine vordefinierte Zieltabelle?
    if (is_array($data["indivFieldsList"])) {
        // Ja es gibt Individual felder, also über alle laufen in indivFieldsList
        foreach ($data["indivFieldsList"] as $key => $indivField) {

            // Bonus Credits Simple Solution
            if ($indivField["dstTable"] == "TABLE_BONUS_ORDERS_PRODUCT_CREDITS") {

                if( $indivField["value"] == null || strlen($indivField["value"]) == 0 ){
                    continue;
                }

                // Credit-Value
                $credits = floatval($indivField["value"]);

                // löschen aller credits für bestimmten artikel
                $sql = "delete from " . TABLE_BONUS_ORDERS_PRODUCT_CREDITS . " where products_id=" . $productID . "; ";
                $db->Execute($sql);

                // Credits für bestimmten Artikel in MySQL schreiben
                // insert into xt_plg_vt_bonus_orders_product_credits (products_id, store_id, cs_id, credits) values ('18249','-1', '-1','15');
                if ($credits > 0){
                    $sql = "insert into " . TABLE_BONUS_ORDERS_PRODUCT_CREDITS . "(products_id, store_id, cs_id, credits) values ('".$productID."','" . -1 . "', '" . -1 . "','". $credits."');";
                    $db->Execute($sql);
                }
            }
        }

        // inidv Array raus
        unset($data["indivFieldsList"]);
    }


    // 2018-10-31: Wenn XT_API_SET_ARTICLE_MANAGE_STARTPAGE_IN_BACKEND gesetzt ist,
    // werden die im Shop hinterlegten Startseiten Produkte nicht verändert. Somit ist
    // die Pflege der Startseiten Produkte über das Shop-Backend möglich.

    // Wenn XT_API_SET_ARTICLE_MANAGE_STARTPAGE_IN_BACKEND nicht vorhanden bzw. false
    // --> Standard verhalten
    // --> Bisherige Startseitenzuordnungen per sql löschen und mit Zuweisungen aus Request überschreiben
    if(XT_API_SET_ARTICLE_MANAGE_STARTPAGE_IN_BACKEND == 'false'){
        // Startpages direkt per SQL setzen
        // Alle Startpages Einträge für product löschen
        $sql = "DELETE FROM " . DB_PREFIX . "_startpage_products where products_id='" . $productID . "'";
        //_SOAPdebug($sql, 'products_startpage_delete');
        $db->Execute($sql);

        // Ist  products_startpageList, dann neuen Modus ab 4.2 nutzen sonst Fallback
        if ($data['products_startpageList'] && sizeof($data['products_startpageList']) > 0) {

        } else {
            // Nur setzen wenn true
            if ($data['products_startpage'] === 1) {

                // über alle stores laufen
                $stores = $store_handler->getStores();
                foreach ($stores as $store) {
                    // Für jeden store startpage setzen
                    $sql = "INSERT INTO " . DB_PREFIX . "_startpage_products (shop_id, products_id, startpage_products_sort) values( " . $store['id'] . ", " . $productID . ", " . $data['products_startpage_sort'] . ")";
                    //_SOAPdebug($sql, 'products_startpage_insert');
                    $db->Execute($sql);
                }
            }
        }
    }

    $productObjImg = new product;
    $productObjImg->setPosition('admin');

    // Hauptbild nach dem ersten _set speichern, da in _set das Feld products_image weggeworfen wird
    $productObjImg->_setImage($productID, $data['products_image']);

    // Status muss nach _setImage gesetzt werden
    $productObj->_setStatus($productID, $data['products_status']);

    // Preis und Kundengruppenpreise setzen
    //2014-07-14: Prüfen auf "Brutto-Admin"
    //2018-07-20: über lokale Variable $productsprice gehen, da $data[products_price] vorher vom Shop manipuliert wurde
    $data['products_price'] = buildPrice($productsprice, $data['products_tax_class_id']);

    // Vorher ALLE Preise löschen!!!
    SoapHelper::deleteProductPricesStaffel($productID);

    $product_priceObj = new product_price;
    $product_priceObj->setPosition('admin');

    // über products_prices laufen
    foreach ($data["products_prices"] as $index => $priceArr) {
        $prodPriceArr = array();

        $prodPriceArr["products_id"] = $productID;
        $prodPriceArr["discount_quantity"] = $priceArr["quantity"];
        $prodPriceArr["price"] = buildPrice($priceArr["price"], $data['products_tax_class_id']);
        $prodPriceArr["cstatus_id"] = $priceArr["group"];
        $prodPriceArr["group"] = $priceArr["group"];
        $prodPriceArr["c_sort_id"] = "(" . $priceArr["group"] . ")";
        // HIER muss "edit" gesetzt werden, obwohl es neu ist
        $resp = $product_priceObj->_set($prodPriceArr, "edit");
    }
    //2014-07-14: Prüfen auf "Brutto-Admin"
    //$data['products_price'] = buildPrice($data['products_price'], $data['products_tax_class_id']);



    // 2016-04-04: Wenn SET_ARTICLE_CAT_DO_NOT_USE in config.php gesetzt ist, verwende
    // statt Liste aus Request für alle stores die SET_ARTICLE_CAT_ID aus config.
    // Die KategorieID aus config wird nur für Neue Produkte gesetzt, vorhandene Produkte behalten
    // somit ihre im Shop-Backend zugewiesene KategorieIDs.
    //
    // Do not use categories list from Request. Don't change product_to_categories by edit from Request.
    // define('SET_ARTICLE_CAT_DO_NOT_USE','true');
    // Default KatID for new products.
    // define('SET_ARTICLE_CAT_ID','12961');
    //
    // Wenn SET_ARTICLE_CAT_DO_NOT_USE nicht vorhanden bzw. false
    // --> Standard verhalten
    // --> Bisherige Kategoriezuweisungen aus sql löschen und mit Zuweisungen aus Request überschreiben
    if(SET_ARTICLE_CAT_DO_NOT_USE == 'true' && SET_ARTICLE_CAT_ID > 0){

        // KatID aus config in integer umwandeln
        $setArticleCatID = (int) SET_ARTICLE_CAT_ID;

        // Mögliche Listen aus Request wegwerfen
        unset($data["productCategoriesList"]);
        unset($data["products_categories"]);
        unset($data["products_external_categories"]);

        // Nur bei neuen Artikeln die KatID aus config dem Product zuweisen
        if ($set_type == "new"){
           // KatID aus config dem internen products_categorie Array zuweisen
           $data["products_categories"][0] = $setArticleCatID;
       }

    }else{
        // 2014-02-11: Löschen aller vorhandener Kategoriezuweisungen aus DB
        // Alle vorhandenen Produktkategorien aus Tabelle TABLE_PRODUCTS_TO_CATEGORIES für Produkt entfernen
        $sql = "DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $productID . "'";
        $db->Execute($sql);
    }

    //
    //  MASTER CAT
    //

    // Wenn Kategorien als external Ids übergeben, dann diese auflösen und in cats rein
    // container für INTERNE IDs bauen
    $cats = array();
    $master_cats = array();

    // Werden Kategorien in der neuen Struktur übertragen (storeids)?
    if ($data['productCategoriesList'] && sizeof($data['productCategoriesList']) > 0) {

        // über Daten der einzelnen stores laufen
        foreach ($data['productCategoriesList'] as $index => $productCategoriesItem) {

            // sind externe Kategorien gesetzt?
            if ( sizeof($productCategoriesItem["products_external_categories"] ) > 0 && strlen($productCategoriesItem["products_external_categories"][0]) > 0) {

                // über alle externen Kategorien laufen
                foreach ($productCategoriesItem["products_external_categories"] as $index2 => $extCat) {
                    // interne CatId anhand von extId auflösen
                    $intCatId = SoapHelper::getCategoriesIDByexternalID($extCat);

                    // Auflösung hat NICHT funktioniert: raus mit error
                    if ($intCatId === FALSE) {
                        // Fehlermeldungen in $result setzen
                        $result['result'] = false;
                        $result['message'] = SoapHelper::MessageFail . " Unable to resolve external category id: " . $extCat . " for products_id= " . $productID . " products_model= " . $ProductItem['products_model'];
                        // Mit Fehlermeldung raus
                        return $result;
                    }
                    // in cats
                    //$cats[] = $intCatId;
                    //$cats[] = $intCatId . "_1";
                    // an Pos 0 steht master
                    if ($index2 === 0) {
                        // über alle stores laufen
                        $master_cats[] = $productCategoriesItem['categories_store_id'] . "_" . $intCatId;

                    } else {
                        $cats[] = $productCategoriesItem['categories_store_id'] . "_" . $intCatId;
                    }
                }
            } else {
                // es wurde ein array mit internen kategorien übergeben, dann einfach in cats mappen
                //$cats = array();

                if( sizeof( $productCategoriesItem["products_categories"] ) > 0 ){

                    $stores = $store_handler->getStores();

                    foreach ($productCategoriesItem["products_categories"] as $index3 => $catId) {

                        // an Pos 0 steht master
                        if ($index3 === 0) {
                                $master_cats[] =  $productCategoriesItem['categories_store_id'] . "_" . $catId ;
                        } else {
                                $cats[] = $productCategoriesItem['categories_store_id'] . "_" . $catId ;
                        }
                    }
                }
            }
        }
    } else {
        // Daten im alten SOAP Format
        // sind externe Kategorien gesetzt?
        if (strlen($data["products_external_categories"][0]) > 0) {

            // über alle externen Kategorien laufen
            foreach ($data["products_external_categories"] as $index => $extCat) {
                // interne CatId anhand von extId auflösen
                $intCatId = SoapHelper::getCategoriesIDByexternalID($extCat);

                // Auflösung hat NICHT funktioniert: raus mit error
                if ($intCatId === FALSE) {
                    // Fehlermeldungen in $result setzen
                    $result['result'] = false;
                    $result['message'] = SoapHelper::MessageFail . " Unable to resolve external category id: " . $extCat . " for products_id= " . $productID . " products_model= " . $ProductItem['products_model'];
                    // Mit Fehlermeldung raus
                    return $result;
                }

                if ($index === 0) {
                    // über alle stores laufen
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store) {
                        $master_cats[] = $store['id'] . "_" . $intCatId;
                    }
                } else {

                    // über alle stores laufen
                    $stores = $store_handler->getStores();
                    foreach ($stores as $store) {
                        $cats[] = $store['id'] . "_" . $intCatId;
                    }
                }
            }
        } else {

            // 2016-04-04 Anpassung EK: Nur wenn $data["products_categories"] ein Array ist durchführen. Sonst, PHP Warning:  Invalid argument supplied for foreach()
            // es wurde ein array mit internen kategorien übergeben, dann erste kategorie in master_cats, rest in cats mappen
            if (is_array($data["products_categories"])) {
                $stores = $store_handler->getStores();

                foreach ($data["products_categories"] as $index => $catId) {

                    // an Pos 0 steht master
                    if ($index === 0) {
                        foreach ($stores as $store) {
                            $master_cats[] =  $store['id'] . "_" . $catId ;
                        }
                    } else {
                        foreach ($stores as $store) {
                            $cats[] = $store['id'] . "_" . $catId ;
                        }
                    }
                }
            }

        }
    }

    // wurden überhaupt Kategorien übergeben bzw. master_cats korrekt befüllt?
    if (sizeof($master_cats) > 0) {

        // Master Cat setzen = 1. Kategorie
        $product_to_mastercatObj = new product_to_mastercat;
        $product_to_mastercatObj->setPosition('admin');
        // Daten direkt einmappen
        $product_to_mastercatObj->url_data['products_id'] = $productID;

        $product_to_mastercatObj->url_data['catIds'] = implode(',', $master_cats);

        // Master Cat setzen
        $setDataResp = $product_to_mastercatObj->setData(TRUE);
    }

    // RESTLICHE Kategorien setzen
    if (sizeof($cats) > 0) {
        // Nun Komma-separierte Liste aus KatIds bauen
        $catList = implode(',', $cats);

        $product_to_catObj = new product_to_cat;
        $product_to_catObj->setPosition('admin');

        // Daten direkt einmappen
        $product_to_catObj->url_data['products_id'] = $productID;
        $product_to_catObj->url_data['catIds'] = $catList;

        // Weitere Cat setzen
        $setDataResp = $product_to_catObj->setData(TRUE);
    }

    //
    // ATTRIBUTE
    //
    if (isset($xtPlugin->active_modules['xt_master_slave'])) {
        // hält Liste mit Attributen, kommagetrennt
        $attrList = "";

        $firstAttr = false;
        // enthält externe IDs > auflösen zu internen IDs und zu einem Kommaseparierten String zusammensetzen
        foreach ($data["products_attributes"] as $index => $attrArr) {
            // per Komma trennen ab den ersten Eintrag
            if ($firstAttr)
                $attrList .= ",";

            // wenn external_attributes_id gefüllt ist (>0) dann diese auflösen, sonst direkt attributes_id nehmen
            if ($attrArr["external_attributes_id"] > 0) {
                $attrId = SoapHelper::getAttributesIDByexternalID($attrArr["external_attributes_id"]);

                if (!$attrId === false) {
                    // Auflösung hat funktioniert
                    $attrList .= $attrId;
                } else {
                    $Exception.=SoapHelper::MessageFail . " : External Attribute not found: " . $attrArr["external_attributes_id"] . " ";

                    // Auflösung hat NICHT funktioniert, dann attributes_id einfügen
                    $attrList .= $attrArr["attributes_id"];
                }
            } else {
                $attrList .= $attrArr["attributes_id"];
            }
            $firstAttr = true;
        }

        // Neues product_to_attributes bauen
        $product_to_attributesObj = new product_to_attributes;
        $product_to_attributesObj->setPosition('admin');

        // Daten direkt einmappen
        $product_to_attributesObj->url_data['products_id'] = $productID;
        $product_to_attributesObj->url_data['attIds'] = $attrList;

        // Attributzuweisungen löschen
        $db->Execute("DELETE FROM " . $product_to_attributesObj->_table . " WHERE products_id = '" . (int) $product_to_attributesObj->url_data['products_id'] . "'");

        // Attribute setzen lassen
        // ACHUTNG: Die Funktion $product_to_attributesObj->setData() schreibt per echo in die Ausgabe!
        if ($product_to_attributesObj->url_data['attIds'] && $product_to_attributesObj->url_data['products_id']) {
            $product_to_attributesObj->url_data['attIds'] = str_replace(array('[', ']', '"', '\\'), '', $product_to_attributesObj->url_data['attIds']);

            // split ist mit PHP 5.3 Deprecated und mit PHP 7.0 entfernt worden
            //$att_ids = split(',', $product_to_attributesObj->url_data['attIds']);
            $att_ids = preg_split('/,/', $product_to_attributesObj->url_data['attIds']);

            for ($i = 0; $i < count($att_ids); $i++) {
                if ($att_ids[$i]) {

                    $record = $db->Execute("select attributes_parent from " . TABLE_PRODUCTS_ATTRIBUTES . " where attributes_id = '" . (int) $att_ids[$i] . "'");
                    if ($record->RecordCount() > 0) {
                        $parent = $record->fields['attributes_parent'];
                    }

                    $attrdata = array($product_to_attributesObj->_master_key => (int) $att_ids[$i], 'attributes_parent_id' => $parent, 'products_id' => (int) $product_to_attributesObj->url_data['products_id']);
                    $o = new adminDB_DataSave($product_to_attributesObj->_table, $attrdata, false, __CLASS__);
                    $obj = $o->saveDataSet();
                }
            }
        }
    }

    //
    // SPECIAL PRICES / SONDERPREISE
    //

    // Hier wird  SoapHelper::processProductSpecialPrices genutzt, da dort intern die Klasse
    // plugins/xt_special_products/classes/class.product_sp_price.php genutzt wird, also die richtigen Hookpoints genutzt werden

    try {
        SoapHelper::processProductSpecialPrices($data['products_special_prices'], $productID, $tax);      //OK
    } catch (Exception $ex) {
        $Exception.=SoapHelper::MessageFail . " Update processProductSpecialPrices: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    //
    // X SELL / ZUSATZZUBEHÖR
    //

    // Hier wird SoapHelper genutzt, da die Klasse cross_selling_to_products keine Hookpoints nutzt
    try {
        // Zubehör einfügen per SoapHelper::processProductXSell
        SoapHelper::processProductXSell($data['products_cross_sell'], $productID);    // OK
    } catch (Exception $ex) {
        // Exception gefangen, also Fehlermeldung schreiben
        $Exception.=SoapHelper::MessageFail . " Update processProductXSell: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }


    //
    // Media Items: Files für Artikel setzen, wenn vorhanden
    //

    // Alle media Links von type=files löschen
    $m = new MediaData();
    if (XT_API_DEL_MEDIA_FILES=='true') {
        $m->unsetAllMediaLink($productID, 'product', 'media');
    }


    if (is_array($ProductItem["mediaItemXTLinkList"])) {
        // code übernommen + angepasst aus class.product_to_media.setData
        // array für mediaIds
        $med_ids = array();

        // über alle mediaItemXTLink in mediaItemXTLinkList laufen
        foreach ($ProductItem["mediaItemXTLinkList"] as $index => $mediaItemXTLink) {

            // ist external_id gesetzt, dann über diese id auflösen
            if (strlen($mediaItemXTLink["external_id"]) > 0) {

                $med_id = SoapHelper::getMediaIDByexternalID($mediaItemXTLink['external_id']);

                if ($med_id === false) {
                    // Auflösung hat NICHT funktioniert - Fehler melden
                    $Exception.=SoapHelper::MessageFail . " : External media id not found: " . $mediaItemXTLink["external_id"] . " ";
                } else {
                    $med_ids[] = $med_id;
                }
            } else {
                // Keine external_id gesetzt, dann id nehmen, wenn gefüllt
                if (strlen($mediaItemXTLink["id"]) > 0) {
                    $med_ids[] = $mediaItemXTLink["id"];
                }
            }
        }

        for ($i = 0; $i < count($med_ids); $i++) {
            if ($med_ids[$i]) {
                $med_data = array('m_id' => (int) $med_ids[$i],
                    'link_id' => $productID,
                    'class' => 'product',
                    'type' => 'media'
                );

                $o = new adminDB_DataSave(TABLE_MEDIA_LINK, $med_data, false, __CLASS__);
                $obj = $o->saveDataSet();
            }
        }
    }

    // Artikel müssen am Schluss noch mal gespeichert werden, da die SEO-URL erst nach der Kategoriezuweisung gebaut werden kann
    // Dies ist auch bei Updates von Artikeln notwendig, da sich die Hauptkategorie geändert haben kann
    // 
    $productObj->_set($data, "edit");

    // Status muss am Ende gesetzt werden
    // aus temporärer Variable nehmen, da $data['products_status'] manipuliert wird
    $productObj->_setStatus($productID, $products_status);

    // date_added muss seit 5.1.x manuell in DB geschrieben werden, da date_added vom Save ausgeschlossen wird
    // Prüfen ob $data['date_added'] ein gültiges Datum enthält
    if( strpos($data['date_added'],"0000-", 0) !== 1 && strpos($data['date_added'],"0001-", 0) !== 1 && strlen($data['date_added'])> 0){
        $sql = "UPDATE ".TABLE_PRODUCTS." SET date_added='". $data['date_added'] . "' WHERE products_id=". $productID;
        $db->Execute($sql);
    // Wenn Prüfen ob $date_added_old ein gültiges Datum enthält, da $data['date_added'] nicht übergeben wurde
    }elseif (strpos($date_added_old,"0000-", 0) !== 1 && strpos($date_added_old,"0001-", 0) !== 1 && strlen($date_added_old)> 0) {
        $sql = "UPDATE ".TABLE_PRODUCTS." SET date_added='". $date_added_old . "' WHERE products_id=". $productID;
        $db->Execute($sql);
    }

    //
    // PERMISSIONS
    //

    // Permissions für Produkt schreiben
    try {
        // Neues product Objekt erzeugen
        $_perm_product = new product;
        // Neues array $_item anlegen
        $_item = array();
        // Pridukt ID in $_item['products_id'] setzen
        $_item['products_id'] = $productID;

        // Anpassung 2012-11-15: Umgestellt auf permissionList
        if (is_array($data['permissionList'])) {
            // Über permission Items laufen
            foreach ($data["permissionList"] as $key => $pItem) {
                // in $data mappen
                $_item[$pItem["pgroup"]] = 1;
            }
        }

        // Neues item_permission Objekt erzeugen
        $set_perm = new item_permission($_perm_product->perm_array);
        // In $set_perm Methode setPosition auf "admin" setzen
        $set_perm->setPosition('admin');
        // Permissiondaten für dieses Produkt in DB speichern
        $set_perm->_saveData($_item, $productID);

        // setSimplePermissionID setzen für Produkt
        $set_perm->_setSimplePermissionID('', $productID, 'categories_id', 'products_id', TABLE_CATEGORIES, TABLE_PRODUCTS, TABLE_PRODUCTS_TO_CATEGORIES);
    } catch (Exception $ex) {
        // Exception gefangen, also Fehlermeldung schreiben
        $Exception.=SoapHelper::MessageFail . " Update Permissions: Execption Message:" . $ex->getMessage() . " in " . $ex->getFile() . "::" . $ex->getLine() . ") ";
    }

    // Produkt ID setzen in Antwort
    $result['products_id'] = $productID;

    // external ID setzen in Antwort
    $result['external_id'] = $ProductItem['external_id'];

    //Hookpoint Bottom
    ($plugin_code = $xtPlugin->PluginCode('setArticle.php:setArticle_bottom')) ? eval($plugin_code) : false;

    // Wurden Fehlermeldungen geschrieben in $Exception?
    if (strlen($Exception) > 0) {
        // Fehlermeldungen in $result setzen
        $result['result'] = false;
        $result['message'] = SoapHelper::MessageFail . " Update: " . $Exception . $productID;
        // Mit Fehlermeldung raus
        return $result;
    }

    // Alles hat OHNE Exception funktioniert
    $result['result'] = true;

    // Erfolgsmeldung setzen
    $result['message'] = SoapHelper::MessageSuccess . " " . $modus . " xt internal products id:" . $productID . " products model:" . $product['products_model'] . $output . $output2;

    // Rückgabe
    return $result;
}

/**
 * Datum umformatieren von ISO zu Veytonformat Bsp: 2013-03-07T00:00:00 zu 2013-03-07 00:00:00
 * @param type $data
 * @param type $fieldName
 */
function reformatDate(&$data, $fieldName) {
    $utc_date = DateTime::createFromFormat(
                        'Y-m-d\TH:i:sO',
                        $data[$fieldName],
                        new DateTimeZone('UTC')
    );
    if($utc_date){
        $local_date = $utc_date;
        $local_date->setTimeZone(new DateTimeZone(date_default_timezone_get ( )));
        $data[$fieldName] = $local_date->format('Y-m-d H:i:s'); // output: 2011-04-26 21:45:59
    }
    else{
        $data[$fieldName] = str_replace('T', ' ', $data[$fieldName]);
    }
}

function buildPrice($pprice, $tax_class_id) {

    if (XT_API_PRODUCT_TRANSFER_NET == 'true') {
        global $price;
        if (_SYSTEM_USE_PRICE == 'true') {
            $pprice = $price->_BuildPrice($pprice, $tax_class_id, 'show');
        }
    }

    return $pprice;
}
?>
