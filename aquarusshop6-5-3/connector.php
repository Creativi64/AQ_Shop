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

/**
 *   experimentell, vorläufig, funktionsweise kann sich ändern
 *   connector um ohne zuviel framework zb plugins abzufragen
 *   bsp implementierung xtCore/connector/cart.php
 */
 
include_once ('xtCore/main_slim.php');
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'security_handler.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'store_handler.php';

global $xtPlugin, $store_handler, $customers_status, $language, $_content, $currency, $countries, $tax, $price, $template;

($plugin_code = $xtPlugin->PluginCode('page_registry.php:bottom')) ? eval($plugin_code) : false;
($plugin_code = $xtPlugin->PluginCode('connector.php:registry')) ? eval($plugin_code) : false;

$customers_status = new customers_status();

$language = new language(isset($_SESSION['selected_language']) ? $_SESSION['selected_language'] : '');
$language->_setLocale();
$language->_getLanguageContent(USER_POSITION);

$currency = new currency();

$tax_country = false;
$countries = new countries('true','store');
if(isset($_SESSION['geoip_country']) && !empty($_SESSION['geoip_country'])
    && is_array($countries->countries_list) && array_key_exists($_SESSION['geoip_country'], $countries->countries_list))
{
    $tax_country = $_SESSION['geoip_country'];
    $agent_check = new agent_check();
    if($agent_check->isBot() && defined('_STORE_COUNTRY'))
    {
        $tax_country = constant('_STORE_COUNTRY');
    }
}

$tax = new tax($tax_country);

$price = new price($customers_status->customers_status_id, $customers_status->customers_status_master);

if ($customers_status->customers_status_template != '') {
    define('_STORE_TEMPLATE', $customers_status->customers_status_template);
} else {
    define('_STORE_TEMPLATE', constant("_STORE_DEFAULT_TEMPLATE"));
}
$template = new Template();

//$store_handler->loadConfig();

$ret = new stdClass();
$ret->success = false;
$ret->message = null;
$response_code = 200;

$json_in = file_get_contents('php://input');
$data_in = json_decode($json_in);
if(empty($data_in)) $data_in = new stdClass();
if(is_array($_REQUEST)) {
    foreach ($_REQUEST as $k => $v) $data_in->$k = $v;
}

if($data_in && isset($data_in->connector) && !empty($data_in->connector))
{
    // die standard connector aus xt, zZ nur testweise der cart; evtl verzeichnis laden?
    define('CONNECTOR_CART', _SRV_WEBROOT._SRV_WEB_CORE.'connector/cart.php');
    define('CONNECTOR_PAGE', _SRV_WEBROOT._SRV_WEB_CORE.'connector/page.php');

    $connector = strtoupper($data_in->connector);
    if (defined($connector) && file_exists($file = constant($connector)))
    {
        include_once $file;
    }
    else $response_code = 404;
}
else $response_code = 404;

http_response_code($response_code);
header('Content-Type: application/json');
echo json_encode($ret);
die();