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
if (!defined('USER_POSITION')) define('USER_POSITION', 'store');
define('MAIN_SLIM', true);


$root_dir = dirname(__FILE__);
$root_dir = str_replace('xtCore','',$root_dir);

$script_name = $_SERVER['SCRIPT_NAME'];

$sys_dir = $script_name;
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/')+1);
$sys_dir = str_replace('export/', '', $sys_dir);

if (!defined('_SRV_WEBROOT')) define('_SRV_WEBROOT',$root_dir);
if (!defined('_SRV_WEB')) define('_SRV_WEB', $sys_dir);

define('_SYSTEM_SQLLOG','false');

include_once _SRV_WEBROOT.'xtFramework/functions/shutdown.php';

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

include_once _SRV_WEBROOT.'conf/config.php';
include_once _SRV_WEBROOT."conf/config_extended.php";
include_once _SRV_WEBROOT.'conf/config_charsets.php';
include_once _SRV_WEBROOT.'conf/database.php';
include_once _SRV_WEBROOT.'conf/paths.php';
include_once _SRV_WEBROOT.'conf/config_caches.php';
include_once _SRV_WEBROOT.'versioninfo.php';
include_once _SRV_WEBROOT."conf/debug.php";
include_once _SRV_WEBROOT.'conf/config_security.php';

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

// -- auto loader
require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'autoload.php';

// composer autoload
require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK .  'library/vendor/autoload.php';

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'function_handler.php'; 

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'database_handler.php';
global $xtPlugin, $db;
($plugin_code = $xtPlugin->PluginCode('main.php:xtPlugin_available')) ? eval($plugin_code) : false;

//--------------------------------------------------------------------------------------
// Loading Config Tab
//--------------------------------------------------------------------------------------

_buildDefine($db, TABLE_CONFIGURATION);
if (!defined('E_DEPRECATED')) define('E_DEPRECATED','8192');

//--------------------------------------------------------------------------------------
// Loading handlers
//--------------------------------------------------------------------------------------
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'plugin_handler.php';


$xtLink = new xtLink();
$system_status = new system_status();

($plugin_code = $xtPlugin->PluginCode('store_main.php:bottom')) ? eval($plugin_code) : false;
