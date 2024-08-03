<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Meta Navigation
 *
 * @example   use in templates like {hook key=ew_evelations_navmeta}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $tpl_object = new template();
    $tpl = 'ew_evelations_navmeta.html';
    $tpl_data = array();
    $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
    if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

        /* @var $ew_evelations_plugin ew_evelations_plugin */
        global $ew_evelations_plugin;

        $dataFormatted = null;
        if ($data = $ew_evelations_plugin->getContentsByBlock('ew_evelations_meta_nav', false)) {
            foreach ($data as $dKey => $dItem) {
                $dItemLink = null;
                if ((int)$dItem['ew_evelations_hyperlink_status'] == 1) {
                    if (!empty($dItem['ew_evelations_hyperlink']) && $test = trim(str_replace('http://', '', $dItem['ew_evelations_hyperlink'])) != '') {
                        $dItemLink = trim($dItem['ew_evelations_hyperlink']);
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

        $output = $tpl_object->getTemplate('ew_evelations_navmeta', $tpl, $tpl_data);
    } else {
        $output = $tpl_object->getCachedTemplate($tpl);
    }

    $output = str_replace('modalIdSuffix', md5(rand().time()), $output);

    echo $output;
    unset($tpl_object, $tpl_data, $output, $tpl, $data, $dataFormatted);

}
