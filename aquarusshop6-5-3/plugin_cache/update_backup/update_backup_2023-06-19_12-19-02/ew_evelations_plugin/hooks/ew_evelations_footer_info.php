<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Footer Content Block `ew_evelations_footer_info`
 *
 * @example   use in templates like {hook key=ew_evelations_footer_info}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_evelations_footer_info.html';
    $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
    $output = null;

    if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        if ($data = $ew_evelations_plugin->getContentsByBlock('ew_evelations_footer_info', false)) {
            $output = $tpl_object->getTemplate('ew_evelations_footer_info', $tpl, array('data' => $data));
            unset($data);
        }

    } else {
        $output = $tpl_object->getCachedTemplate($tpl);
    }

    echo $output;
    unset($tpl_object, $output, $tpl);

}
