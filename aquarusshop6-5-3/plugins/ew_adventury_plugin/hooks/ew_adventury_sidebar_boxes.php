<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Sidebar Boxes
 *
 * @example   use in templates like {hook key=ew_adventury_sidebar_boxes}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    if (!ew_adventury_plugin::is_index() && ew_adventury_plugin::get_current_pagename() != 'product' && ew_adventury_plugin::get_current_pagename() != '404') {

        $tpl_object = new template();
        $tpl = 'ew_adventury_sidebar_boxes.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
        if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
            global $ew_adventury_plugin;

            $dataFormatted = null;
            if ($data = $ew_adventury_plugin->getContentsByBlock('ew_adventury_sidebar_boxes', false)) {
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
                        'id'            => (int)$dItem['content_id'],
                        'title'         => $dItem['content_title'],
                        'heading'       => !empty($dItem['content_heading']) ? $dItem['content_heading'] : $dItem['content_title'],
                        'link'          => $dItemLink,
                        'short_content' => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_body_short'] : null,
                        'image'         => !empty($dItem['content_image']) ? $dItem['content_image'] : null,
                        'orgData'       => $dItem,
                    );
                }
            }

            $tpl_data = array(
                'data' => $dataFormatted,
            );

            $output = $tpl_object->getTemplate('ew_adventury_sidebar_boxes', $tpl, $tpl_data);
            unset($data, $dataFormatted);

        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        $smarty->assign('ew_adventury_sidebar_boxes', $output);
        unset($tpl_object, $tpl_data, $output, $tpl);

    }

}
