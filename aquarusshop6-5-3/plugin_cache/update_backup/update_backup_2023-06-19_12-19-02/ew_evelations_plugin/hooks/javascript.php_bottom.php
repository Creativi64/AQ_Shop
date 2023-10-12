<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * JavaScript Hookpoint before content printing
 */

use ew_evelations\plugin as ew_evelations_plugin;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    //include assets
    if (method_exists('ew_evelations\plugin', 'registerAssets')) {

        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        $ew_evelations_plugin = (is_object($ew_evelations_plugin) && $ew_evelations_plugin instanceof ew_evelations_plugin) ? $ew_evelations_plugin : new ew_evelations_plugin();
        echo ($ew_evelations_plugin->registerAssets() === false) ? '<!-- ew_evelations_plugin :: Errors in merging the client-side scripts -->' : null;
    }

}
