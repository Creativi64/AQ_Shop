<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Seo Text
 *
 * @example   use in templates like {hook key=ew_evelations_seotext}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    if (ew_evelations_plugin::show_seotext()) {
        $tpl_object = new template();
        $tpl = 'ew_evelations_seotext.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
        if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

            /* @var $ew_evelations_plugin ew_evelations_plugin */
            global $ew_evelations_plugin;

            $dataFormatted = null;
            if ($data = $ew_evelations_plugin->getContentsByBlock('ew_evelations_seotext', false)) {
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
                        'id'            => (int)$dItem['content_id'],
                        'title'         => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_title'] : null,
                        'heading'       => !empty($dItem['content_heading']) ? $dItem['content_heading'] : null,
                        'short_content' => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_body_short'] : null,
                        'long_content'  => (trim(strip_tags($dItem['content_body'])) != '') ? $dItem['content_body'] : null,
                        'image'         => !empty($dItem['content_image']) ? $dItem['content_image'] : null,
                        'link'          => $dItemLink,
                        'show_text'     => ((int)$dItem['ew_evelations_show_text_status'] == 1) ? true : false,
                        'orgData'       => $dItem,
                    );
                }
            }

            $dataFormattedCount = is_countable($dataFormatted) ? count($dataFormatted) : 0;
            $tpl_data = array(
                'data'         => $dataFormatted,
                'navItemWidth' => ((int)$dataFormattedCount > 1) ? ew_evelations_plugin::floorPrecision(100 / $dataFormattedCount, 2) . '%' : null,
            );

            $output = $tpl_object->getTemplate('ew_evelations_seotext', $tpl, $tpl_data);
        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        unset($tpl_object, $tpl_data, $output, $tpl, $dataFormatted, $data);
    }

}
