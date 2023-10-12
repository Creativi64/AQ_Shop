<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Startpage Area Top (Cached)
 *
 * @example   use in templates like {hook key=ew_adventury_teaser}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    if (ew_adventury_plugin::is_index()) {

        $tpl_object = new template();
        $tpl = 'ew_adventury_startpage_top.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
        if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
            global $ew_adventury_plugin, $xtPlugin;

            $tpl_data = array(
                'plugin_status' => $xtPlugin->active_modules,
            );

            $output = $tpl_object->getTemplate('ew_adventury_startpage_top', $tpl, $tpl_data);
        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        unset($tpl_object, $tpl_data, $output, $tpl);
    }

}
