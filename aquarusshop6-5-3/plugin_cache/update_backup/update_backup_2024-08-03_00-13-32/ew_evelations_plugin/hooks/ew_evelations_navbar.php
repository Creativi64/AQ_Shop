<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Bootstrap navbar items
 *
 * @example   use in templates like {hook key=ew_evelations_navbar}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_evelations_navbar.html';
    $tpl_data = array();
    $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
    if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        $ew_evelations_plugin->testMode = true;
        $tpl_data = array(
            '_categories' => $ew_evelations_plugin->get_categories_array(true, (($d = ew_evelations_plugin::getCategoryDepth()) < 2) ? $d : 2, true, 0, 0),
        );
        $ew_evelations_plugin->testMode = false;

        $output = $tpl_object->getTemplate('ew_evelations_navbar', $tpl, $tpl_data);

    } else {

        $output = $tpl_object->getCachedTemplate($tpl);

    }

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl);

}
