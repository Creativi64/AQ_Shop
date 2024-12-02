<?php

/*
 * CKFinder Configuration File
 *
 * For the official documentation visit http://docs.cksource.com/ckfinder3-php/
 */

/*============================ PHP Error Reporting ====================================*/
// http://docs.cksource.com/ckfinder3-php/debugging.html

// Production
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 0);

// Development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

global $xtPlugin;

$baseDir = _SRV_WEBROOT.'media/';

$baseUrl = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/xtAdmin/')).'/media/';

/*============================ General Settings =======================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html

$config = array();

/*============================ Enable PHP Connector HERE ==============================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_authentication

$config['authentication'] = function () {
	
	global $xtc_acl;
	return $xtc_acl->isLoggedIn();

};

/*============================ License Key ============================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_licenseKey

$config['licenseName'] = 'xt:Commerce 4';
$config['licenseKey'] = 'H4Q6-V7UB-3E8D-B4WV-CBHS-N86E-P1W8';

/*============================ CKFinder Internal Directory ============================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_privateDir

$config['privateDir'] = array(
    'backend' => 'default',
    'tags'   => '.ckfinder/tags',
    'logs'   => '.ckfinder/logs',
    'cache'  => false, // '.ckfinder/cache',
    'thumbs' => false, // '.ckfinder/cache/thumbs',
);

/*============================ Images and Thumbnails ==================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_images

$config['images'] = array(
    'maxWidth'  => 3840,
    'maxHeight' => 2160,
    'quality'   => 80,
    'sizes' => array(
        'small'  => array('width' => 480, 'height' => 320, 'quality' => 80),
        'medium' => array('width' => 600, 'height' => 480, 'quality' => 80),
        'large'  => array('width' => 800, 'height' => 600, 'quality' => 80)
    )
);

/*=================================== Backends ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_backends

$config['backends'][] = array(
    'name'         => 'default',
    'adapter'      => 'local',
  //  'baseUrl'      => '/images/',
    'baseUrl'		=>$baseUrl,
    'root'         => $baseDir, // Can be used to explicitly set the CKFinder user files directory.
    'chmodFiles'   => 0777,
    'chmodFolders' => 0755,
    'filesystemEncoding' => 'UTF-8',
);

/*================================ Resource Types =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_resourceTypes

$config['defaultResourceTypes'] = '';

global $db;
$allowed_extensions = 'bmp,gif,jpeg,jpg,png';
$webp_enabled = $db->GetOne('SELECT 1 FROM '. TABLE_MEDIA_FILE_TYPES . " where file_ext = 'webp' AND file_type = 'images' ");
if($webp_enabled)
    $allowed_extensions = 'bmp,gif,jpeg,jpg,png,webp';

$config['resourceTypes'][] = array(
		'name'              => 'originals',
		'directory'         => 'images/org',
        'label'             => 'media/images/org',
		'maxSize'           => 0,
		'allowedExtensions' => $allowed_extensions,
		'deniedExtensions'  => '',
		'backend'           => 'default'
);

$config['resourceTypes'][] = array(
    'name'              => 'category',
    'directory'         => 'images/category',
    'maxSize'           => 0,
    'allowedExtensions' => $allowed_extensions,
    'deniedExtensions'  => '',
    'backend'           => 'default'
);

$config['resourceTypes'][] = array(
		'name'              => 'content',
		'directory'         => 'images/content',
		'maxSize'           => 0,
		'allowedExtensions' => $allowed_extensions,
		'deniedExtensions'  => '',
		'backend'           => 'default'
);

$config['resourceTypes'][] = array(
		'name'              => 'manufacturer',
		'directory'         => 'images/manufacturer',
		'maxSize'           => 0,
		'allowedExtensions' => $allowed_extensions,
		'deniedExtensions'  => '',
		'backend'           => 'default'
);

$config['resourceTypes'][] = array(
		'name'              => 'product popup',
		'directory'         => 'images/popup',
		'maxSize'           => 0,
		'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
		'deniedExtensions'  => '',
		'backend'           => 'default'
);

$config['resourceTypes'][] = array(
		'name'              => 'product info',
		'directory'         => 'images/info',
		'maxSize'           => 0,
		'allowedExtensions' => $allowed_extensions,
		'deniedExtensions'  => '',
		'backend'           => 'default'
);

$config['resourceTypes'][] = array(
    'name'              => 'free_files',
    'directory'         => 'files',
    'label'             => 'media/files',
    'maxSize'           => 0,
    'allowedExtensions' => _SYSTEM_EXTENSION_WHITELIST,
    'deniedExtensions'  => '',
    'backend'           => 'default'
);

/*================================ Access Control =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_roleSessionVar

$config['roleSessionVar'] = 'CKFinder_UserRole';

// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_accessControl
$config['accessControl'][] = array(
    'role'                => '*',
    'resourceType'        => '*',
    'folder'              => '/',

    'FOLDER_VIEW'         => true,
    'FOLDER_CREATE'       => false,
    'FOLDER_RENAME'       => false,
    'FOLDER_DELETE'       => false,

    'FILE_VIEW'           => true,
    'FILE_CREATE'         => true,
    'FILE_RENAME'         => true,
    'FILE_DELETE'         => true,

    'IMAGE_RESIZE'        => true,
    'IMAGE_RESIZE_CUSTOM' => true
);


/*================================ Other Settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html

$config['overwriteOnUpload'] = false;
$config['checkDoubleExtension'] = true;
$config['disallowUnsafeCharacters'] = false;
$config['secureImageUploads'] = true;
$config['checkSizeAfterScaling'] = true;
$config['htmlExtensions'] = array('html', 'htm', 'xml', 'js');
$config['hideFolders'] = array('.*', 'CVS', '__thumbs');
$config['hideFiles'] = array('.*');
$config['forceAscii'] = true;
$config['xSendfile'] = false;
$config['dontListFiles'] = isset($_GET['dontListFiles']) && $_GET['dontListFiles'] == 1 ? true : false;

// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_debug
$config['debug'] = false;

/*==================================== Plugins ========================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_plugins

$config['pluginsDirectory'] = __DIR__ . '/plugins';
$config['plugins'] = array('xtcommerce');

/*================================ Cache settings =====================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_cache

$config['cache'] = array(
    'imagePreview' => 24 * 3600,
    'thumbnails'   => 24 * 3600 * 365,
    'proxyCommand' => 0
);

/*============================ Temp Directory settings ================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_tempDirectory

$config['tempDirectory'] = sys_get_temp_dir();

/*============================ Session Cause Performance Issues =======================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_sessionWriteClose

$config['sessionWriteClose'] = true;

/*================================= CSRF protection ===================================*/
// http://docs.cksource.com/ckfinder3-php/configuration.html#configuration_options_csrfProtection

$config['csrfProtection'] = true;

/*============================== End of Configuration =================================*/

($plugin_code = $xtPlugin->PluginCode('ckfinder.config_php:bottom')) ? eval($plugin_code) : false;

// Config must be returned - do not change it.
return $config;
