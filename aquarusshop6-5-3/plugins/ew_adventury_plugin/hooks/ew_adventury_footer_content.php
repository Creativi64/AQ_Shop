<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Footer Content Block `ew_adventury_footer_content`
 *
 * @example   use in templates like {hook key=ew_adventury_footer_content}
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_adventury_footer_content.html';
    $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
    $output = null;

    if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
        global $ew_adventury_plugin;

        if ($data = $ew_adventury_plugin->getContentsByBlock('ew_adventury_footer_content', false)) {
            $output = $tpl_object->getTemplate('ew_adventury_footer_content', $tpl, array('data' => $data));
            unset($data);
        }

    } else {
        $output = $tpl_object->getCachedTemplate($tpl);
    }

    echo $output;
    unset($tpl_object, $output, $tpl);

}
