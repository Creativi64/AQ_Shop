<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Bootstrap nav / Sidebar nav
 *
 * @example   use in templates like {hook key=ew_evelations_navbar}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_evelations_nav.html';
    $tpl_data = [];
    $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
    if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        $displaySubLevel = 1;
        $depth = ew_evelations_plugin::getCategoryDepth() - 1;
        $parentCategoryData = ew_evelations_plugin::getCategoryDataById(ew_evelations_plugin::getParentCategoryIdByLevel($displaySubLevel));

        if (empty($parentCategoryData)) {
            $_categories = $ew_evelations_plugin->get_categories_array();
        } else {
            $_categories = $ew_evelations_plugin->get_categories_array(false, $depth, true, $displaySubLevel);
        }

        $tpl_data = [
            '_categories'     => $_categories,
            'categories_list' => ew_evelations_plugin::buildHtmlList($_categories),
            'parent'          => $parentCategoryData
        ];

        $output = $tpl_object->getTemplate('ew_evelations_nav', $tpl, $tpl_data);

    } else {

        $output = $tpl_object->getCachedTemplate($tpl);

    }

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl);

}
