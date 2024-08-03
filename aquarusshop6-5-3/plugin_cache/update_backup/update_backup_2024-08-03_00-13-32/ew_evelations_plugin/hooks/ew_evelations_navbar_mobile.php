<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Bootstrap mobile nav
 *
 * @example   use in templates like {hook key=ew_evelations_navbar}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_evelations_navbar_mobile.html';
    $tpl_data = [];
    $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
    if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        $max = ew_evelations_plugin::getCategoryDepth();
        $c = $ew_evelations_plugin->get_categories_array(true, $max < 3 ? $max : 3);

        $tpl_data = [
            'menuContainer' => !empty($c) ? ew_evelations_plugin::buildMobileMenuContainer($c) : null,
        ];

        $output = $tpl_object->getTemplate('ew_evelations_navbar_mobile', $tpl, $tpl_data);

    } else {

        $output = $tpl_object->getCachedTemplate($tpl);

    }

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl);

}
