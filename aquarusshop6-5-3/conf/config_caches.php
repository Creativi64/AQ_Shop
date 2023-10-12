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

define('_TIME_24_HOURS',(24*3600));

/*
 *
 * Performance note: When your database server is much slower than your Web server or the database is very overloaded
 * then ADOdb's caching is good because it reduces the load on your database server.
 * If your database server is lightly loaded or much faster than your Web server, then caching could actually reduce performance.
 *
 */
define('_CACHE_DIR_ADODB', 'cache/adodb/'); // db cache dir relative to _SRV_WEBROOT

define('_CACHETIME_ADODB',0); // db cache time (seconds)


/* folgenden Wert nicht ändern, Zeile nicht editieren, wenn Sie nicht absolut genau wissen was Sie tun */
//define('_DONT_CLEAR_CACHE_ADODB_AUTOMATICALLY', false);


define('_CACHETIME_LANGUAGE_CONTENT',0);

define('_CACHETIME_MANUFACTURER_LIST',_TIME_24_HOURS);

/**
 * activate Smarty Template Cache
 */
define('SMARTY_USE_CACHE','false');

/**
 * activate/deactivate Smarty Compile Check
 * https://www.smarty.net/docsv2/de/variable.compile.check.tpl
 *
 * ! set it to TRUE during template development !
 * to speedup page load in production try setting it to FALSE
 *
 */
define('SMARTY_USE_COMPILE_CHECK', true);

/**
 * Smarty Cache Lifetime (secounds)
 * @link https://www.smarty.net/docs/en/variable.cache.lifetime.tpl
 */
define('SMARTY_CACHE_LIFETIME', 300);

/**
 * Smarty Cache Modified Check
 * @link https://www.smarty.net/docs/en/variable.cache.modified.check.tpl
 */
define('SMARTY_CACHE_CHECK',false);


// caching ID Settings für Templates, to override standard Cache ID Setting which might be to loose for some cases
/**
 * available Cache ID Parameters
 * language 		-> Current Language Code
 * currency 		-> Current Currency Code
 * shop				-> Current Shop ID
 * customer_group	-> Current Customer Group ID
 * page				-> Current Page
 * sorting			-> sorting parameter (lists)
 * category			-> Current Category ID
 * listing_page		-> Current Listing Page (Lists with pagination)
 */

define('CACHE_ID_OVERRIDE','true');

$_cache_id_settings = array();
$_cache_id_settings['/xtCore/boxes/box_payment_logos.html']=array('language','shop','customer_group');

$_cache_id_settings['/xtCore/boxes/box_categories_recursive.html']=array('language','shop','customer_group','category');
$_cache_id_settings['/xtCore/boxes/box_manufacturers.html']=array('language','shop','customer_group');

$_cache_id_settings['/xtCore/boxes/box_footer_contact.html']=array('language','shop','customer_group');
$_cache_id_settings['/xtCore/boxes/box_footer_left.html']=array('language','shop','customer_group');
$_cache_id_settings['/xtCore/boxes/box_footer_right.html']=array('language','shop','customer_group');

$_cache_id_settings['/xtCore/pages/categorie_listing/categorie_listing.html']=array('language','shop','customer_group','currency','sorting','category');

$_cache_id_settings['/xtCore/pages/product_listing/product_listing_v1.html']=array('language','shop','customer_group','currency','sorting','listing_page','category');
$_cache_id_settings['/xtCore/pages/product_listing/product_listing_v2.html']=array('language','shop','customer_group','currency','sorting','listing_page','category');
$_cache_id_settings['/xtCore/pages/product_listing/product_listing_simple_list.html']=array('language','shop','customer_group','currency','sorting','listing_page','category');


/**
 * empty Zend OP-Cache while changing/testing/debugging PHP
 */
define('FLUSH_OPCACHE', false);
if(FLUSH_OPCACHE === true && function_exists('opcache_reset'))
{
    opcache_reset();
}

/**
 * Wenn diese _SYSTEM_USE_DB_HOOKS auf 'false' gestellt wird, legt das System-Plugin Cache files im plugin_cache Ordner an.
 *
 * 'false' wurde ehemals für kleine Shops empfohlen, wir raten zur Verwendung von Datenbank-Hooks.
 * die korrekte Funktion mit 'false' wird momentan noch getestet
 *
 * Empfohlen: 'true'
 */
define('_SYSTEM_USE_DB_HOOKS', 'true');

/**
 * Mit Einstellung true wird die Länderkonfiguration in verschiedene /cache/_cache_xt.countries.*.ser geschrieben und von dort gelesen
 *
 * Standard: false
 */
define('_USE_CACHE_COUNTRIES', false);

/**
 * Mit Einstellung true werden die Sprachvariablen in verschiedene /cache/_cache_xt.language_content.*.ser geschrieben und von dort gelesen
 *
 * Standard: false
 */
define('_USE_CACHE_LANGUAGE_CONTENT', false);

/**
* Mit Einstellung true werden hookpoint codes vorab geladen, reduziert Datenbanklast bei größerer Plugin-Anzal
*
*/

define('_PRELOAD_PLG_HOOK_CODE',true);
