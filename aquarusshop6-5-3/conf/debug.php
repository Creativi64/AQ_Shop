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

define('ADODB_ERROR_LOG_TYPE',3); // 
define('ADODB_ERROR_LOG_DEST',_SRV_WEBROOT.'xtLogs/db_error.log'); // destination to store sql errors
define('DB_ERROR_LOG_TRACE', true); // trace sql errors
define('DB_ERROR_MIN_MAIL_TIME', 60); // selbe sql-Fehlerstelle max alles x Sekunden per mail senden (xtcommerce-errorhandler.inc.php)
define('DB_ERROR_SEND_MAIL', true); // sollen überhaupt sql-Fehlermeldungen gesendet werden

//define('XT_WIZARD_STARTED','false');

/**
 * please note:
 *
 * some backend function not work if display_errors = 1 / _SYSTEM_DEBUG_MANUALLY = 'true'
 * because when an error/warning is displayed it would break the expected json responses (javascript error in console)
 * that's why display_errors is set to 1 only in shop frontend
 * shop frontend could look ugly
 */

define('_SYSTEM_DEBUG_MANUALLY', 'false');// if set to 'true' all errors, warning will be displayed but not in backend
if((defined('_SYSTEM_DEBUG') && _SYSTEM_DEBUG=='true') || (_SYSTEM_DEBUG_MANUALLY=='true' && USER_POSITION != 'admin')){
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        if (constant('USER_POSITION') == 'store') ini_set("display_errors", "1");
		define('_SYSTEM_DEBUG_FINAL', 'true');
}else{
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
    ini_set("display_errors", "0");
    define('_SYSTEM_DEBUG_FINAL', 'false');
}

/**
 *  _SYSTEM_PHPLOG
 *
 *  when set to 'true' php errors will be logged to xtLogs/phpErrors.txt
 *  in wizard logging is enabled by default
 *
 *  default  'false'
 */
if (defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED === true)
{
    define('_SYSTEM_PHPLOG','true');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set("display_errors", "0");
}
else
    define('_SYSTEM_PHPLOG','false');

/**
 *  _SYSTEM_PHPLOG_IP
 *
 *  with _SYSTEM_PHPLOG_IP set, errors will be logged to xtLogs/phpErrors_THE_IP.log
 *  errors will be logged even if _SYSTEM_PHPLOG is set to false
 *  you may use a comma separated list of ip addresses
 *  see main.php in /xtCore and in /xtAdmin
 *
 *  also, even if shop is set to not active in backend, the shop will be visible for _SYSTEM_PHPLOG_IP
 *  see class multistore
 *
 *  default  false
*/
define('_SYSTEM_PHPLOG_IP', false);

/**
 *  CSRF_PROTECTION
 *
 *  with CSRF_PROTECTION set to 'true' perform check for admin session security key (ASSK), on failure die()
 *  when set to 'debug' checks ASSK but only log error to backend > system > log
 *  when set to 'false' skip the ASSK check
 *  default  'debug'
 */
define ('CSRF_PROTECTION','debug');
 
/* if CHECK_STORE_ID_EXISTS=true, will perform check for store_id field in a table. When set to FALSE the store_id won't be checked */
define ('CHECK_STORE_ID_EXISTS','true'); 

// time in millisec
define('MIN_QUERYTIME_LOG','0.00005');

// output template hook names in shop frontend
define('_TPL_OUTPUT_HOOK_NAMES',false);

// dev settings
define("DISPLAY_SQL_QUERIES_COUNT",false); // setting to true will only take effect if you edit the adodb-lib too...
define("_SYSTEM_PARSE_TIME",false);

// plugin update system
define('_PLUGIN_UPDATE_SCOPE','BETA');

/**
 * __DEBUG_IP defines if and for what IP xtFramework/functions/debug.php::__debug should output to __DEBUG_OUT_FILE
 * __DEBUG_OUT_FILE log file used in xtFramework/functions/debug.php::__debug
 * if __DEBUG_OUT_FILE is empty messages are echo'd
 */
define('__DEBUG_IP', false);
define('__DEBUG_OUT_FILE', '_debug.log');

define('_KP_LOG', false);
define('_MOLLIE_LOG', false);
define('PAYPAL_CHECKOUT_DEBUG', false);
