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
 * Hier werden die SOAP Funktionen und Datentypen in ein grosses array gemappt.
 * Hieraus wird auch die WSDL Datei erzeugt.
 */
class SoapMappings {

    static function getMappings() {
        
        global $xtPlugin;

        $aMappings = array(
            "setArticle" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "productItem" => "tns:productsItem",
                ),
                "out" => array(
                    "result" => "tns:productResultItem",
                ),
                "documentation" => "update or add product. For indivFieldsList use dstTable=TABLE_PRODUCTS",
            ),
            "setArticles" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:productsItemList",
                ),
                "out" => array(
                    "result" => "tns:productsResultItemList",
                ),
                "documentation" => "update or add a list of products. For indivFieldsList use dstTable=TABLE_PRODUCTS",
            ),
            "getArticle" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "products_id" => "xsd:int",
                    "external_id" => "xsd:string",
                    "indivFieldsList" => "tns:indivFieldsList",// 2017-02-28: Neu Liste mit Indivfields für Rückgabe
                ),
                "out" => array(
                    "getArticleResultItem" => "tns:getArticleResultItem",
                ),
                "documentation" => "get product data WITHOUT IMAGE DATA itself. Use SOAP method getImage to get the image data. Use filter: Examples: array('products_status=1','last_modified >= YYYY-mm-dd HH:MM:SS')",
            ),
            "getArticles" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "offset" => "xsd:string",
                    "blocksize" => "xsd:string",
                    "filter" => "tns:arrayOfString",
                    "indivFieldsList" => "tns:indivFieldsList",// 2017-02-28: Neu Liste mit Indivfields für Rückgabe
                ),
                "out" => array(
                    "result" => "tns:getArticlesResultItem",
                ),
                "documentation" => "get a list of products data WITHOUT IMAGE DATA itself. Use SOAP method getImage to get the image data. Use filter: Examples: array('products_status=1','last_modified >= YYYY-mm-dd HH:MM:SS')",
            ),
            "deleteArticle" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "products_id" => "xsd:string",
                    "external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:productResultItem",
                ),
                "documentation" => "delete an article",
            ),
            "setStocks" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:productsStockList",
                ),
                "out" => array(
                    "result" => "tns:productsResultItemList",
                ),
                "documentation" => "update stocks of a list of products",
            ),
            "setCustomer" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:customersItem",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                    "customers_id" => "xsd:int", // 2017-03-01: type von string in int geändert
                    "external_id" => "xsd:string",
                    "csid" => "xsd:string",
                ),
                "documentation" => "update or add a customer. For indivFieldsList use dstTable=TABLE_CUSTOMERS",
            ),
            "getCustomer" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "customers_id" => "xsd:int",// 2017-03-01: type von string in int geändert
                    "external_id" => "xsd:string",
                    "extNumberRangeCustomerId" => "xsd:int",
                    "extNumberRangeDeliveryAdr" => "xsd:int", 
                ),
                "out" => array(
                    "customerData" => "tns:customersItemXT",
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "get a customer by customer_id or external_id",
            ),
            "getCustomers" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "offset" => "xsd:int", // 2017-03-03: type von string in int geändert
                    "blocksize" => "xsd:int", // 2017-03-03: type von string in int geändert
                    "filter" => "tns:arrayOfString",
                    "extNumberRangeCustomerId" => "xsd:int",
                    "extNumberRangeDeliveryAdr" => "xsd:int",
                ),
                "out" => array(
                    "getCustomersResultItem" => "tns:getCustomersResultItem",
                ),
                "documentation" => "get a list of customers.",
            ),
            "deleteCustomerByMailAdr" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "customer_mail" => "xsd:string",
                    "shop_id" => "xsd:int", // 2017-03-03: type von string in int geändert
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "delete a customer by customer_mail and shop_id",
            ),          
                        
            
            // Garcia: Neu 2013-02-21
            // 2017-02-28: customers_id von string in int
            "deleteCustomer" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "customers_id" => "xsd:int",
                    "external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "delete a customer",
            ),
            "getOrders" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "filter" => "tns:arrayOfString",
                    "start" => "xsd:int", // 2017-03-03: typ von string in int geändert
                    "blocksize" => "xsd:int", // 2017-03-03: typ von string in int geändert
                    "extNumberRangeCustomerId" => "xsd:int",
                    "extNumberRangeDeliveryAdr" => "xsd:int",
                    "indivFieldsList" => "tns:indivFieldsList",// 2016-03-01: Garcia Neu Liste mit Indivfields für Rückgabe
                    ),
                "out" => array(
                    "result" => "tns:ordersList",
                    "message" => "xsd:string",
                ),
                "documentation" => "use filter: Examples: array('status=16','date_purchased >= YYYY-mm-dd HH:MM:SS')",
            ),
            "getOrderStatusNames" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:xtstatusList",
                    "message" => "xsd:string",
                ),
                "documentation" => "get order status names",
            ),
            "getBasePriceNames" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:xtstatusList",
                    "message" => "xsd:string",
                ),
                "documentation" => "get base price unit names",
            ),
            "getCustomersStatus" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:xtcustomersStatusList",
                    "message" => "xsd:string",
                ),
                "documentation" => "get customer status names (Kundengruppen)",
            ),
            "getVersion" => array( // getVersion
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "versionInfos" => "tns:xtVersionInfos",
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "get version infos of this SOAP interface",
            ),
            "setMedia" => array( // setMedia
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "link_id" => "xsd:string",
                    "isLinkExternal" => "xsd:boolean",
                    "className" => "xsd:string",
                    "mediaItem" => "tns:mediaItem",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set a media item",
            ),                 
            "setMediaXT" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "mediaItemXT" => "tns:mediaItemXT",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "id" => "xsd:int",
                    "file" => "xsd:string",
                    "message" => "xsd:string",
                ),
                "documentation" => "set a media XT item (picture or file)",
            ), 
            "getMediaXT" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "id" => "xsd:int",
                    "external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                    "mediaItemXT" => "tns:mediaItemXT",
                ),
                "documentation" => "get a media XT item (picture or file)",
            ),
            "getMediaXTMetaList" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    
                    "offset" => "xsd:int",
                    "blocksize" => "xsd:int",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                    "mediaItemXTMetaList" => "tns:mediaItemXTMetaList",
                ),
                "documentation" => "get a media XT item list metadata without the filedata. To get the file use getMediaXT",
            ),            
            "deleteMediaXT" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "id" => "xsd:int",
                    "external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "delete a media XT item by id or external_id",
            ),
            "getImage" => array(// 2012-10-25: Neue Funktion getImage $image_dir = 'org/', $image_name
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "image_dir" => "xsd:string",
                    "image_name" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:xtimage",
                    "message" => "xsd:string",
                ),
                "documentation" => "get image by name",
            ),
            "getAttributes" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "extNumberRange" => "xsd:string",
                    "start" => "xsd:int",
                    "size" => "xsd:int",
                ),
                "out" => array(
                    "result" => "tns:attributeItemList",
                    "message" => "xsd:string",
                ),
                "documentation" => "get all attributes",
            ),
            "setAttributes" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "attributeItemList" => "tns:attributeItemList",
                ),
                "out" => array(
                    "result" => "tns:attributeItemList",
                    "message" => "xsd:string",
                ),
                "documentation" => "set attributes. For indivFieldsList use dstTable=TABLE_PRODUCTS_ATTRIBUTES",
            ),
            "setAttribute" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "attributeItem" => "tns:attributeItem",
                ),
                "out" => array(
                    "result" => "tns:attributeResultItem",
                ),
                "documentation" => "set one attribute for products. For indivFieldsList use dstTable=TABLE_PRODUCTS_ATTRIBUTES",
            ),
            "deleteAttribute" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "attributes_id" => "xsd:string",
                    "attributes_ext_id" => "xsd:int", // 2017-02-28: attributes_ext_id type string in int
                ),
                "out" => array(
                    "result" => "tns:attributeResultItem",
                ),
                "documentation" => "delete an attribute",
            ),
            "getStores" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:storesList",
                    "message" => "xsd:string",
                ),
                "documentation" => "get stores",
            ),
            "getManufacturers" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "start" => "xsd:int", 
                    "size" => "xsd:int", 
                    "extNumberRange" => "xsd:int",
                ),
                "out" => array(
                    "result" => "tns:manufacturerList",
                    "message" => "xsd:string",
                ),
                "documentation" => "List Manufacturers",
            ),
            "getCategories" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "start" => "xsd:int", // 2017-03-03: start type string in int
                    "blocksize" => "xsd:int", // 2017-03-03: blocksize type string in int
                    "extNumberRange" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:categoryList",
                    "message" => "xsd:string",
                ),
                "documentation" => "List Categories",
            ),
            "setCategories" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:categoriesItemList",
                ),
                "out" => array(
                    "result" => "tns:categoriesResultItemList",
                ),
                "documentation" => "update or add a list of categories.  For indivFieldsList use dstTable=TABLE_CATEGORIES",
            ),
            "setCategory" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:categoryItem",
                ),
                "out" => array(
                    "result" => "tns:categoryResultItem",
                ),
                "documentation" => "update or add a category.  For indivFieldsList use dstTable=TABLE_CATEGORIES",
            ),
            "deleteCategory" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "categories_id" => "xsd:int",  // 2017-02-28: categories_id von string in int
                    "external_id" => "xsd:string",
                    "doRemoveSubCategories" => "xsd:boolean",
                ),
                "out" => array(
                    "result" => "tns:categoryResultItem",
                ),
                "documentation" => "delete a category optional with childcategories",
            ),
            "setManufacturers" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:manufacturerList",
                ),
                "out" => array(
                    "result" => "tns:manufacturersResultItemList",
                ),
                "documentation" => "update or add a list of manufacturers",
            ),  
            "setManufacturer" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:manufacturerListItem",
                ),
                "out" => array(
                    "result" => "tns:manufacturerResultItem",
                ),
                "documentation" => "update or add a manufactuerer",
            ),            
            "deleteManufacturer" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "manufacturers_id" => "xsd:int",  // 2017-02-28: manufacturers_id type string in int geändert
                    "external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:manufacturerResultItem",
                ),
                "documentation" => "delete a manufacturer",
            ),
            "getDeliveryTimes" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:xtstatusList",
                    "message" => "xsd:string",
                ),
                "documentation" => "List Categories",
            ),
            "getPaymentMethods" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:paymentList",
                    "message" => "xsd:string",
                ),
                "documentation" => "List Payment Methods",
            ),
            "getShippingMethods" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                ),
                "out" => array(
                    "result" => "tns:shippingList",
                    "message" => "xsd:string",
                ),
                "documentation" => "List Shipping Methods",
            ),
            "setOrderStatus" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "order_id" => "xsd:int",
                    "new_status_id" => "xsd:int",
                    "comments" => "xsd:string",
                    "sendmail" => "xsd:int",
                    "sendcomments" => "xsd:int", // 2015-11-05 Neu: Optional kann nun gewählt werden ob der Kommentar bei sendmail mitverschickt werden soll. Default ist 1
                    "indivFieldsList" => "tns:indivFieldsList",  // 2013-01-23 Neu: Inidividualfelder hinzugefügt
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set new order status",
            ),
            "setOrderStati" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "order_ids" => "tns:arrayOfInt",
                    "new_status_id" => "xsd:int",
                    "comments" => "xsd:string",
                    "sendmail" => "xsd:int",
                    "sendcomments" => "xsd:int", // 2015-11-05 Neu: Optional kann nun gewählt werden ob der Kommentar bei sendmail mitverschickt werden soll. Default ist 1
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set new order stati for a list of orders",
            ),
            "setOrderExported" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "order_id" => "xsd:int",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set exported flag to order",
            ),
            "setArticleImages" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "product_model" => "xsd:string",
                    "images" => "tns:productImages",
                    "product_external_id" => "xsd:string",
                ),
                "out" => array(
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set new Product Images ",
            ),
            "setArticleImagesList" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "images" => "tns:productImagesList",
                ),
                "out" => array(
                    "result" => "tns:productsResultItemList",
                ),
                "documentation" => "set new Product Images for a List of products",
            ),
            "setContent" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:contentItem",
                ),
                "out" => array(
                    "result" => "tns:contentResultItem",
                ),
                "documentation" => "update or add a content",
            ),
            "setContents" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:contentItemList",
                ),
                "out" => array(
                    "result" => "tns:contentResultItemList",
                ),
                "documentation" => "update or add a list of contents.",
            ),
            "setContentBlock" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:contentBlockItem",
                ),
                "out" => array(
                    "result" => "tns:contentBlockResultItem",
                ),
                "documentation" => "update or add a content",
            ),
            
            "setContentBlocks" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "Item" => "tns:contentBlocksItemList",
                ),
                "out" => array(
                    "result" => "tns:contentBlocksResultItemList",
                ),
                "documentation" => "update or add a list of contentBlocks.",
            ),

            "setContentImages" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "images" => "tns:contentImages",
                    "content_external_id" => "xsd:string",
                ),
                "out" => array(
                    "content_id" =>"xsd:string",
                    "external_id" => "xsd:string",
                    "result" => "xsd:boolean",
                    "message" => "xsd:string",
                ),
                "documentation" => "set new or update Content Images ",
            ),
            "setContentImagesList" => array(
                "in" => array(
                    "user" => "xsd:string",
                    "pass" => "xsd:string",
                    "images" => "tns:contentImagesList",
                ),
                "out" => array(
                    "result" => "tns:contentResultItemList",
                ),
                "documentation" => "set new or update Content Images List of contents",
            ),
        );

        //Hookpoint Bottom
        ($plugin_code = $xtPlugin->PluginCode('SoapMappings.class.php:getMappings_bottom')) ? eval($plugin_code) : false;
        
        return $aMappings;
    }
}
?>