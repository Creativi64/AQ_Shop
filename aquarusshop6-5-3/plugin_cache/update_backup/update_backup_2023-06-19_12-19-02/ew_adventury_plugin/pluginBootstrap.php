<?php

/**
 * Plugin bootstrap file
 */

//absolute path config
define('EW_ADVENTURY_PLUGIN_ROOT_DIR', __DIR__);
define('EW_ADVENTURY_PLUGIN_LIB_DIR', EW_ADVENTURY_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('EW_ADVENTURY_PLUGIN_ASSETS_DIR', EW_ADVENTURY_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'assets');
define('EW_ADVENTURY_PLUGIN_CONFIG_DIR', EW_ADVENTURY_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'config');
define('EW_ADVENTURY_PLUGIN_CLASSES_DIR', EW_ADVENTURY_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'classes');
define('EW_ADVENTURY_PLUGIN_HOOKS_DIR', EW_ADVENTURY_PLUGIN_ROOT_DIR . DIRECTORY_SEPARATOR . 'hooks');

//less.php compiler
if (!class_exists('lessc')) {
    require_once EW_ADVENTURY_PLUGIN_LIB_DIR . DIRECTORY_SEPARATOR . 'oyejorge/less.php/lessc.inc.php';
}
if (!class_exists('\cakebake\lesscss\LessConverter')) {
    require_once EW_ADVENTURY_PLUGIN_LIB_DIR . DIRECTORY_SEPARATOR . 'cakebake/php-lesscss-compiler/src/LessConverter.php';
}

//plugin class
require_once EW_ADVENTURY_PLUGIN_CLASSES_DIR . DIRECTORY_SEPARATOR . 'class.ew_adventury_plugin.php';
require_once EW_ADVENTURY_PLUGIN_CLASSES_DIR . DIRECTORY_SEPARATOR . 'Template.php';

//global plugin object
if (class_exists('ew_adventury\plugin')) {
    global $ew_adventury_plugin;

    $ew_adventury_plugin = (is_object($ew_adventury_plugin) && $ew_adventury_plugin instanceof ew_adventury\plugin) ? $ew_adventury_plugin : new ew_adventury\plugin();
}
