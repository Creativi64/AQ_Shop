<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Seo Text
 *
 * @example   use in templates like {hook key=ew_adventury_eyecatcher}
 *
 */

use ew_adventury\plugin as ew_adventury_plugin;
use ew_adventury\Template as template;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    if (ew_adventury_plugin::show_eyecatcher()) {
        $tpl_object = new template();
        $tpl = 'ew_adventury_eyecatcher.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_adventury_plugin', 'hooks', 'plugin');
        if (!ew_adventury_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {
            global $ew_adventury_plugin;

            $dataFormatted = null;
            if ($data = $ew_adventury_plugin->getContentsByBlock('ew_adventury_eyecatcher', false)) {
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
                        'title'         => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_title'] : null,
                        'heading'       => !empty($dItem['content_heading']) ? $dItem['content_heading'] : null,
                        'short_content' => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_body_short'] : null,
                        'long_content'  => (trim(strip_tags($dItem['content_body'])) != '') ? $dItem['content_body'] : null,
                        'image'         => !empty($dItem['content_image']) ? $dItem['content_image'] : null,
                        'link'          => $dItemLink,
                        'show_text'     => ((int)$dItem['ew_adventury_show_text_status'] == 1) ? true : false,
                        'orgData'       => $dItem,
                    );
                }
            }

            $dataFormattedCount = is_countable($dataFormatted) ? count($dataFormatted) : 0;
            $tpl_data = array(
                'data'         => $dataFormatted,
                'navItemWidth' => ((int)$dataFormattedCount > 1) ? ew_adventury_plugin::floorPrecision(100 / $dataFormattedCount, 2) . '%' : null,
            );

            $output = $tpl_object->getTemplate('ew_adventury_eyecatcher', $tpl, $tpl_data);
        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        unset($tpl_object, $tpl_data, $output, $tpl, $dataFormatted, $data);
    }

}
