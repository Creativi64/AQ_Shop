<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * JavaScript Hookpoint before content printing
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    //include assets
    if (method_exists('ew_adventury\plugin', 'registerAssets')) {
        global $ew_adventury_plugin;

        $ew_adventury_plugin = (is_object($ew_adventury_plugin) && $ew_adventury_plugin instanceof ew_adventury_plugin) ? $ew_adventury_plugin : new ew_adventury_plugin();
        echo ($ew_adventury_plugin->registerAssets() === false) ? '<!-- ew_adventury_plugin :: Errors in merging the client-side scripts -->' : null;
    }
}
