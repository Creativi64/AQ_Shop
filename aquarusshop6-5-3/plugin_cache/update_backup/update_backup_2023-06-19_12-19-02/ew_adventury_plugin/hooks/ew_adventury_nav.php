<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Bootstrap nav / Sidebar nav
 *
 * @example   use in templates like {hook key=ew_adventury_navbar}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_adventury_nav.html';
    $tpl_data = [];
    $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
    if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        global $ew_adventury_plugin;

        $displaySubLevel = 1;
        $depth = ew_adventury_plugin::getCategoryDepth() - 1;

        $parentCategoryData = ew_adventury_plugin::getCategoryDataById(ew_adventury_plugin::getParentCategoryIdByLevel($displaySubLevel));
        if (empty($parentCategoryData)) {
            $_categories = $ew_adventury_plugin->get_categories_array(false, 1, true, 0, 0, true);
        } else {
            $_categories = $ew_adventury_plugin->get_categories_array(false, $depth, true, $displaySubLevel, 0, true);
        }

        $tpl_data = [
            '_categories'     => $_categories,
            'categories_list' => ew_adventury_plugin::buildHtmlList($_categories),
            'parent'          => $parentCategoryData
        ];

        $output = $tpl_object->getTemplate('ew_adventury_nav', $tpl, $tpl_data);

    } else {

        $output = $tpl_object->getCachedTemplate($tpl);

    }

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl);

}
