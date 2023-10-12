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

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
define('_VALID_CALL','true');
define('USER_POSITION', 'admin');
define('SESSION_REFRESH_TIME', 60); // in sec
define('_SEO_MOD_REWRITE',false);
define('_SYSTEM_SQLLOG','false'); // auf false gesetzt für 5.1 todo ergebnisse in dev prüfen und ggf wieder aktivieren, eher nicht

ini_set('display_errors', 'Off');

//--------------------------------------------------------------------------------------
// Define Root Paths
//--------------------------------------------------------------------------------------

$cancel_path = array('xtFramework/admin', 'xtFramework\admin');

$root_dir = dirname(__FILE__);
$root_dir = str_replace($cancel_path,'',$root_dir);

$sys_dir = $_SERVER['SCRIPT_NAME'];
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/')+1);
$sys_dir = str_replace('xtFramework/admin', '', $sys_dir);

if (!defined('_SRV_WEBROOT')) define('_SRV_WEBROOT', $root_dir);
if (!defined('_SRV_WEB')) define('_SRV_WEB',  $sys_dir);
define('_SRV_WEBROOT_ADMIN',_SRV_WEBROOT.'xtAdmin/');

$upload_dir = str_replace('xtAdmin/', '', _SRV_WEB);
//$upload_dir = str_replace('xtadmin/', '', _SRV_WEB);
define('_SRV_WEB_UPLOAD', $upload_dir );
//--------------------------------------------------------------------------------------
// Load Config Files
//--------------------------------------------------------------------------------------

include_once _SRV_WEBROOT.'xtFramework/functions/shutdown.php';
include_once _SRV_WEBROOT.'conf/config.php';
include_once _SRV_WEBROOT."conf/config_extended.php";
include_once _SRV_WEBROOT.'conf/config_charsets.php';
include_once _SRV_WEBROOT.'conf/database.php';
include_once _SRV_WEBROOT.'conf/paths.php';
include_once _SRV_WEBROOT.'conf/config_caches.php';
include_once _SRV_WEBROOT.'conf/config_security.php';
include_once _SRV_WEBROOT.'versioninfo.php';
//--------------------------------------------------------------------------------------
// Files needed Include before Session
//--------------------------------------------------------------------------------------

spl_autoload_register(function ($class)
{
    global $is_pro_version;

    if(is_null($is_pro_version))
    {
        $is_pro_version = false;
        require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/read_license.php';
        $lic_info = getLicenseFileInfo(['versiontype']);
        if(is_array($lic_info))
        {
            if($lic_info['versiontype'] == 'PRO') $is_pro_version = true;
            elseif($lic_info['versiontype'] != 'FREE') die(' - license error - lic21');
        }
        else die(' license error - lic22');
    }

    if($is_pro_version)
    {
        // framework pro
        $class_file = _SRV_WEBROOT .'xtPro/'. _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }
        else
        {
            // admin pro
            $class_file = _SRV_WEBROOT.'xtPro/'._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
            if (file_exists($class_file))
            {
                include_once $class_file;
            }
            else
            {
                // framework free
                $class_file = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';

                if (file_exists($class_file))
                {
                    include_once $class_file;
                }
                else
                {
                    // admin free
                    $class_file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
                    if (file_exists($class_file))
                    {
                        include_once $class_file;
                    }
                }
            }
        }
    }
    else
    {
        // framework free
        $class_file = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }
        else
        {
            // admin free
            $class_file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
            if (file_exists($class_file))
            {
                include_once $class_file;
            }
        }
    }
});

// composer autoload
require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK .  'library/vendor/autoload.php';

//include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.Template.php';

//--------------------------------------------------------------------------------------
// Database Connection & Session
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/database_handler.php';

//--------------------------------------------------------------------------------------
// Required Functions and Helpers
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'function_handler.php';

//--------------------------------------------------------------------------------------
// Loading Plugins
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'plugin_handler.php';
global $xtPlugin, $db;
($plugin_code = $xtPlugin->PluginCode('main.php:xtPlugin_available')) ? eval($plugin_code) : false;

//--------------------------------------------------------------------------------------
// Security & Links
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'security_handler.php';

//--------------------------------------------------------------------------------------
// Loading Config Tab
//--------------------------------------------------------------------------------------

_buildDefine($db, TABLE_CONFIGURATION);

//--------------------------------------------------------------------------------------
// Error Reporting
//--------------------------------------------------------------------------------------
if (!defined('E_DEPRECATED')) define('E_DEPRECATED','8192');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
include_once _SRV_WEBROOT."conf/debug.php";

// see logging config in conf/debug.php
// check if logging for specific ip should be enabled
global $debug_ip, $remote_ip;
$remote_ip = false;
$debug_ips = array();
$debug_ip  = false;
if(_SYSTEM_PHPLOG_IP!=false)
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
// Loading Store Handling
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'store_handler.php';


//--------------------------------------------------------------------------------------
// Loading Needed Classes
//--------------------------------------------------------------------------------------

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'main_handler.php';


//	$dbNav = new class_dbNav();

/*
function outpre($l) {
    echo "<pre>".print_r($l, true)."</pre>";
}
*/
//include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/auth_pear.php';

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/auth.php';

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/functions.inc.php';
//include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/getAdminDropdownData.php';
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/default_lang_definitions.inc.php';

include _SRV_WEBROOT._SRV_WEB_CORE.'form_handler.php';
($plugin_code = $xtPlugin->PluginCode('admin_main.php:bottom')) ? eval($plugin_code) : false;


function versionCheck() {
	global $db;

	$check_array = array();
	$check_array['shop_version']=_SYSTEM_VERSION;
	$check_array['domain']=_SYSTEM_BASE_HTTP;
    if (defined('_PLUGIN_UPDATE_SCOPE')) $check_array['scope']=_PLUGIN_UPDATE_SCOPE;

	// get plugin versions
	$rs = $db->Execute("SELECT code,version FROM ".TABLE_PLUGIN_PRODUCTS." WHERE plugin_status='1'");
	$check_array['plugins'] = array();
	while (!$rs->EOF) {
		$check_array['plugins'][$rs->fields['code']]=$rs->fields['version'];
		$rs->MoveNext();
	}

	$_lic = _SRV_WEBROOT . 'lic/license.txt';
	if (!file_exists($_lic))
		die('- main lic missing -');
	$val_line = '';
	$bline = '';
	$_file_content = file($_lic);
	foreach ($_file_content as $bline_num => $bline) {
		if (preg_match('/key:/', $bline)) {
			$val_line = $bline;
			break;
		}
	}

	$val_line = explode(':', $val_line);
	$_shop_lic = '';
	$_shop_lic = trim($val_line[1]);
	$check_array['license_key']=$_shop_lic;

	$postfields = json_encode($check_array);
	
	
	$ch = curl_init('https://webservices.xt-commerce.com/license/updatecheck/v2');
	curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_TIMEOUT=>5,
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Accept: application/json',
					'Authorization: Basic cHVibGljOnB1YmxpYw=='
			),
			CURLOPT_POSTFIELDS => json_encode($check_array)
	));

	$response = curl_exec($ch);

	
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($httpcode!=200) {
		$response = json_decode($response, TRUE);
		if (is_array($response['message'])) $response['message']=$response['message'][0];
		return $response;
	} 
	
	if(curl_exec($ch) === false)
	{
		$response['message']= curl_error($ch);
	}
	$responseData = json_decode($response, TRUE);

	// update plugin versions
    if (is_array($responseData['plugins']) && count ( $responseData['plugins'] ) > 0) {
        foreach ( $responseData['plugins'] as $key => $val ) {

            $version = preg_replace('[^0-9\.]','',$val['version']);

            $sql = "UPDATE ".TABLE_PLUGIN_PRODUCTS." SET version_available = ? WHERE code= ? ";
            $db->Execute($sql,array($version,$val['code']));

        }
    }
	
	return $responseData;

}


/**
 * Some helper functions
 */
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return '#' . random_color_part() . random_color_part() . random_color_part();
}
/**
 * End some helper functions
 */

$a = 0;

