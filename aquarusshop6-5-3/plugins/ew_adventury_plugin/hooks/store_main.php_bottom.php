<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manage html error reporting
 */

global $page;

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    //enable error messages
    if (ew_adventury_plugin::isDebugMode() && ew_adventury_plugin::check_conf('CONFIG_EW_ADVENTURY_PLUGIN_DEBUG_MODE_REPORTING')) {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
        ini_set("display_errors", "1");
    }

    //current shop id
    ew_adventury_plugin::setShopIdConstant();

}
