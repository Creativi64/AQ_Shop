<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Startpage Area Top (Cached)
 *
 * @example   use in templates like {hook key=ew_evelations_teaser}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    if (ew_evelations_plugin::is_index()) {

        $tpl_object = new template();
        $tpl = 'ew_evelations_startpage_top.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
        if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

            /* @var $ew_evelations_plugin ew_evelations_plugin */
            global $ew_evelations_plugin, $xtPlugin;

            $tpl_data = array(
                'plugin_status' => $xtPlugin->active_modules,
            );

            $output = $tpl_object->getTemplate('ew_evelations_startpage_top', $tpl, $tpl_data);
        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        unset($tpl_object, $tpl_data, $output, $tpl);
    }

}
