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

if(version_compare(PHP_VERSION, '5.6.0', '<')) {
    die('xt:Commerce Systemvorraussetzung: Mindestens PHP 5.6 erforderlich, 7.3 empfohlen. Sie verwenden PHP '.PHP_VERSION.'. Kontaktieren Sie Ihren Webhoster<br><br>
xt:Commerce system requirement: At least PHP 5.6 is required, 7.3 is recommended. You are using PHP '.PHP_VERSION.'. Contact your web hoster
');
}

if(function_exists('opcache_reset'))
{
    opcache_reset();
}
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Flag
define('XT_WIZARD_STARTED', true);
// Define short code for DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);
// Wizard root dir
define('ROOT_DIR_PATH', dirname(__FILE__) . DS);
// Needed for the logger. TRUE -> display in brower, FALSE -> log to file
define('DISPLAY_ERRORS', false);
date_default_timezone_set('Europe/Berlin');
$root_dir = str_replace('xtWizard', '', dirname(__FILE__));
$sys_dir = $_SERVER['SCRIPT_NAME'];
$sys_dir = substr($sys_dir, 0, strripos($sys_dir, '/') + 1);
$sys_dir = str_replace('xtWizard/', '', $sys_dir);

if (!defined('_SRV_WEBROOT')) define('_SRV_WEBROOT', $root_dir);
if (!defined('_SRV_WEB')) define('_SRV_WEB',  $sys_dir);

if(isset($_POST['_dbPrefix']))
{
    define('DB_PREFIX', $_POST['_dbPrefix']);
}

// Check if license file exists
$lic_file = _SRV_WEBROOT . "lic" . DIRECTORY_SEPARATOR . "license.txt";

$mainFile = _SRV_WEBROOT . 'xtCore' . DS . 'main.php';
if (file_exists($mainFile)) {
    require_once $mainFile;
}

spl_autoload_register(function ($class)
{
    global $is_pro_version;

    if(is_null($is_pro_version))
    {
        if(file_exists(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/read_license.php'))
        {
            $is_pro_version = false;

            require_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'functions/read_license.php';
            $lic_info = getLicenseFileInfo(['versiontype']);
            if (is_array($lic_info))
            {
                if ($lic_info['versiontype'] == 'PRO')
                {
                    $is_pro_version = true;
                }
                elseif ($lic_info['versiontype'] != 'FREE')
                {
                    die(' - license error - lic21');
                }
            }
            else
            {
                die(' license error - lic22');
            }
        }
    }

    if($is_pro_version)
    {
        // framework pro

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
    else if(!is_null($is_pro_version))
    {
        // framework free

        // admin free
        $class_file = _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }

    }
});


if (!file_exists($lic_file)) {
    header("Location: " . _SRV_WEB . "xtWizard/no-license.php?licerr=lic111");
    exit;
}
else if(version_compare(_SYSTEM_VERSION, '6.1.0', '>=') && !defined('IS_UPDATE_VERSIONINFO'))
{
    $fc = file_get_contents($lic_file);
    if (preg_match('/versiontype: PRO/m', $fc, $matches) !== 1
        &&
        preg_match('/versiontype: FREE/m', $fc, $matches) !== 1
    )  {
        header("Location: " . _SRV_WEB . "xtWizard/no-license.php?licerr=lic112");
        exit;
    }
}

$templates_c = _SRV_WEBROOT . "templates_c";

if (!file_exists($templates_c)){
	mkdir ($templates_c);
}

if (!is_writable($templates_c)) {
	chmod($templates_c, 755);
	if (!is_writable($templates_c)) {
		die ('- Benoetige Schreibrechte auf templates_c -');
	}
}


GLOBAL $ADODB_THROW_EXCEPTIONS;
$ADODB_THROW_EXCEPTIONS = true;
// Include core wizard file, with autoloader
require_once dirname(__FILE__) . DS . 'lib' . DS . 'Loader.php';
// Run the wizard
Wizard::getInstance()->run();