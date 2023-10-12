<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Bootstrap navbar items
 *
 * @example   use in templates like {hook key=ew_adventury_navbar}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_adventury_navbar.html';
    $tpl_data = array();
    $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
    if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        global $ew_adventury_plugin;

        $ew_adventury_plugin->testMode = true;
        $tpl_data = array(
            '_categories' => $ew_adventury_plugin->get_categories_array(true, (($d = ew_adventury_plugin::getCategoryDepth()) < 2) ? $d : 2, true, 0, 0),
        );

        $output = $tpl_object->getTemplate('ew_adventury_navbar', $tpl, $tpl_data);

    } else {

        $ew_adventury_plugin->testMode = false;
        $output = $tpl_object->getCachedTemplate($tpl);

    }

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl);

}
