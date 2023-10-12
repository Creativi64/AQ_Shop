<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Meta Navigation
 *
 * @example   use in templates like {hook key=ew_adventury_navmeta}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_adventury_navmeta.html';
    $tpl_data = array();
    $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
    if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
        global $ew_adventury_plugin;

        $dataFormatted = null;
        if ($data = $ew_adventury_plugin->getContentsByBlock('ew_adventury_meta_nav', false)) {
            foreach ($data as $dKey => $dItem) {
                $dItemLink = null;
                if ((int)$dItem['ew_adventury_hyperlink_status'] == 1) {
                    if (!empty($dItem['ew_adventury_hyperlink']) && $test = trim(str_replace('http://', '', $dItem['ew_adventury_hyperlink'])) != '') {
                        $dItemLink = trim($dItem['ew_adventury_hyperlink']);
                    } else {
                        $dItemLink = $dItem['link'];
                    }
                }
                $dataFormatted[] = array(
                    'id'      => (int)$dItem['content_id'],
                    'title'   => $dItem['content_title'],
                    'heading' => !empty($dItem['content_heading']) ? $dItem['content_heading'] : $dItem['content_title'],
                    'link'    => $dItemLink,
                    'orgData' => $dItem,
                );
            }
        }

        $tpl_data = array(
            'data' => $dataFormatted,
        );

        $output = $tpl_object->getTemplate('ew_adventury_navmeta', $tpl, $tpl_data);
    } else {
        $output = $tpl_object->getCachedTemplate($tpl);
    }

    $output = str_replace('modalIdSuffix', md5(rand().time()), $output);

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl, $data, $dataFormatted);

}
