<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manage html error reporting
 */

use ew_evelations\plugin as ew_evelations_plugin;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    //enable error messages
    if (ew_evelations_plugin::isDebugMode() && ew_evelations_plugin::check_conf('CONFIG_EW_EVELATIONS_PLUGIN_DEBUG_MODE_REPORTING')) {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
        ini_set("display_errors", "1");
    }

    //current shop id
    ew_evelations_plugin::setShopIdConstant();

}