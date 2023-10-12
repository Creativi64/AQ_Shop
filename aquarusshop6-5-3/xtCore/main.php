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

define('_VALID_CALL','true');
define('USER_POSITION', 'store');

$_SYSTEM_INSTALL_SUCCESS = 'false';

// sql log
define('_SYSTEM_SQLLOG','false');

//--------------------------------------------------------------------------------------
// Error Reporting  until DB initialization
//--------------------------------------------------------------------------------------
if (!defined('E_DEPRECATED')) define('E_DEPRECATED','8192');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors", "1");

//--------------------------------------------------------------------------------------
// Define Root Paths
//--------------------------------------------------------------------------------------
$root_dir = dirname(__FILE__);
$root_dir = str_replace('xtCore','',$root_dir);

$script_name = $_SERVER['SCRIPT_NAME'];

$sys_dir = $script_name;
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/')+1);
$sys_dir = str_replace('xtAdmin/', '', $sys_dir);
$check = substr($sys_dir,0,1);

if (!defined('_SRV_WEBROOT')) define('_SRV_WEBROOT', $root_dir);
if (!defined('_SRV_WEB')) define('_SRV_WEB',  $sys_dir);
include_once _SRV_WEBROOT.'xtFramework/functions/shutdown.php';
include_once _SRV_WEBROOT.'conf/debug.php';
include_once _SRV_WEBROOT.'conf/config_security.php';
include_once _SRV_WEBROOT.'versioninfo.php';
include_once _SRV_WEBROOT."conf/config_extended.php";

// see logging config in conf/debug.php
// check if logging for specific ip should be enabled
global $debug_ip, $remote_ip;
$debug_ip  = false;
$remote_ip = false;
$debug_ips = array();
if(_SYSTEM_PHPLOG=='false' && _SYSTEM_PHPLOG_IP!=false)
{
    $debug_ips = array_filter(array_map('trim', explode(',', _SYSTEM_PHPLOG_IP)));
    if(count($debug_ips))
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }
        if(in_array($remote_ip, $debug_ips))
        {
            $debug_ip = $remote_ip;
        }
    }
}
// log errors when _SYSTEM_PHPLOG==true or some _SYSTEM_PHPLOG_IP is set and it is current remote ip
if (_SYSTEM_PHPLOG=='true' || $debug_ip)
{
    ini_set("log_errors" , '1');
    $log_file = 'phpErrors.txt';
    if($debug_ip)
    {
        $log_file = 'phpErrors_'.$remote_ip.'.log';
        $log_file = str_replace(':','', $log_file);
    }
    ini_set("error_log" , _SRV_WEBROOT.'xtLogs/' . $log_file);
}

//--------------------------------------------------------------------------------------
// cleanup GET
//--------------------------------------------------------------------------------------
if (count($_GET)>0) {
    foreach ($_GET as $get_key=>$get_var) {
        if (substr($get_key,0,4)=='amp;') {
           unset($_GET[$get_key]);
           $_GET[str_replace('amp;','',$get_key)]=$get_var; 
        }
    }
}
//--------------------------------------------------------------------------------------
// Load Config Files
//--------------------------------------------------------------------------------------
if(!file_exists(_SRV_WEBROOT.'conf/config.php'))
{
    // create config.php
    $fp = fopen(_SRV_WEBROOT . 'conf/config.php', 'w');
    fputs($fp, '<?php'.PHP_EOL);
    fclose($fp);
}
include _SRV_WEBROOT.'conf/config.php';
include _SRV_WEBROOT.'conf/config_charsets.php';
$installer_warning = false;
if($_SYSTEM_INSTALL_SUCCESS != 'true' && !defined('XT_WIZARD_STARTED')){
	header('Location: ' . _SRV_WEB.'xtWizard/index.php');
	exit();
} else {
    // check if installer dir is still there
    if (file_exists(_SRV_WEBROOT.'xtWizard/index.php')) {
        $installer_warning = true;
    }
}

include _SRV_WEBROOT.'conf/paths.php';
include _SRV_WEBROOT.'conf/database.php';

require_once _SRV_WEBROOT.'conf/config_search.php';

require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'autoload.php';

// composer autoload
require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK .  'library/vendor/autoload.php';

//--------------------------------------------------------------------------------------
// Required Functions and Helpers
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'function_handler.php';

if($_SYSTEM_INSTALL_SUCCESS != 'true' && defined('XT_WIZARD_STARTED')) {
	return;
}

include _SRV_WEBROOT.'conf/config_caches.php';

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.Template.php';


//--------------------------------------------------------------------------------------
// Database Connection & Session
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'database_handler.php';
global $xtPlugin, $db;
($plugin_code = $xtPlugin->PluginCode('main.php:xtPlugin_available')) ? eval($plugin_code) : false;


//--------------------------------------------------------------------------------------
// Security & Links
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'security_handler.php';

//--------------------------------------------------------------------------------------
// Cookies
//-------------------------------
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'cookie_handler.php';

if($_SYSTEM_INSTALL_SUCCESS == 'true')
{
    // auto delete cache countries on changes in config_caches.php
    // solange nicht im be umschaltbar
    if(!isset($_SESSION['_USE_CACHE_COUNTRIES'])) {
        $_SESSION['_USE_CACHE_COUNTRIES'] = _USE_CACHE_COUNTRIES;
    }
    if($_SESSION['_USE_CACHE_COUNTRIES'] != _USE_CACHE_COUNTRIES) {
        xt_cache::deleteCache('countries');
        $_SESSION['_USE_CACHE_COUNTRIES'] = _USE_CACHE_COUNTRIES;
    }
    // auto delete cache lng on changes in config_caches.php
    // solange nicht im be umschaltbar
    if(!isset($_SESSION['_USE_CACHE_LANGUAGE_CONTENT'])) {
        $_SESSION['_USE_CACHE_LANGUAGE_CONTENT'] = _USE_CACHE_LANGUAGE_CONTENT;
    }
    if($_SESSION['_USE_CACHE_LANGUAGE_CONTENT'] != _USE_CACHE_LANGUAGE_CONTENT) {
        xt_cache::deleteCache('language_content');
        $_SESSION['_USE_CACHE_LANGUAGE_CONTENT'] = _USE_CACHE_LANGUAGE_CONTENT;
    }

    //--------------------------------------------------------------------------------------
    // Loading Config Tab
    //--------------------------------------------------------------------------------------

    _buildDefine($db, TABLE_CONFIGURATION);


    //--------------------------------------------------------------------------------------
    // Loading Store Handling
    //--------------------------------------------------------------------------------------

    include _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'store_handler.php';


    //--------------------------------------------------------------------------------------
    // Loading Needed Classes
    //--------------------------------------------------------------------------------------

    include _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'main_handler.php';

    include _SRV_WEBROOT . _SRV_WEB_CORE . 'form_handler.php';
}


if (!function_exists("json_encode") || !function_exists("json_decode")) 
{
    include_once(PHP_EXTJS_DOC_ROOT . "/Lib/json.php");
    $jsonEncoder = new Services_JSON();
    if (!function_exists("json_encode"))
    {
        function json_encode($value) 
        {
            global $jsonEncoder;
            return $jsonEncoder->encode($value);
        }
    }    
    if (!function_exists("json_decode")) 
    {
        function json_decode($value) 
        {
            global $jsonEncoder;
            return $jsonEncoder->decode($value);
        }
    }
}

($plugin_code = $xtPlugin->PluginCode('store_main.php:bottom')) ? eval($plugin_code) : false;     
