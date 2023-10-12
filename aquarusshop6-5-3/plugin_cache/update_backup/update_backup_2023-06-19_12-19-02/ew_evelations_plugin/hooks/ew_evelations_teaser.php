<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Main Teaser
 *
 * @example   use in templates like {hook key=ew_evelations_teaser}
 *
 */

use ew_evelations\plugin as ew_evelations_plugin;
use ew_evelations\Template as template;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    if (ew_evelations_plugin::show_teaser()) {
        $tpl_object = new template();
        $tpl = 'ew_evelations_teaser.html';
        $tpl_data = array();
        $tpl_object->getTemplatePath($tpl, 'ew_evelations_plugin', 'hooks', 'plugin');
        if (!ew_evelations_plugin::isFileCacheAllowed() || !$tpl_object->isTemplateCache($tpl)) {

            /* @var $ew_evelations_plugin ew_evelations_plugin */
            global $ew_evelations_plugin;

            $dataFormatted = [];
            if ($data = $ew_evelations_plugin->getContentsByBlock('ew_evelations_teaser', false)) {
                $dataFormatted = array();
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
                        'heading'       => !empty($dItem['content_heading']) ? $dItem['content_heading'] : $dItem['content_title'],
                        'short_content' => (trim(strip_tags($dItem['content_body_short'])) != '') ? $dItem['content_body_short'] : null,
                        'long_content'  => (trim(strip_tags($dItem['content_body'])) != '') ? $dItem['content_body'] : null,
                        'image'         => !empty($dItem['content_image']) ? $dItem['content_image'] : null,
                        'link'          => $dItemLink,
                        'show_text'     => ((int)$dItem['ew_evelations_show_text_status'] == 1) ? true : false,
                        'orgData'       => $dItem,
                    );
                }
            } elseif (class_exists('\slider')) {
                $slider = new \slider();
                if (($data = $slider->_getSlider(ew_evelations_plugin::getSliderId())) &&
                    isset($data['slides']) &&
                    is_array($data['slides'])) {

                    $dataFormatted = array();
                    foreach ($data['slides'] as $dKey => $dItem) {
                        $id = (int)$dItem['slide_id'];
                        $dataFormatted[$id] = array(
                            'id'            => $id,
                            'title'         => !empty($dItem['slide_alt_text']) ? $dItem['slide_alt_text'] : null,
                            'heading'       => !empty($dItem['slide_alt_text']) ? $dItem['slide_alt_text'] : null,
                            'image'         => !empty($dItem['slide_image']) ? $dItem['slide_image'] : null,
                            'link'          => !empty($dItem['slide_link']) ? $dItem['slide_link'] : null,
                            'show_text'     => !empty($dItem['slide_alt_text']),
                            'orgData'       => $dItem,
                        );
                        $dataFormatted[$id] = array_merge($dataFormatted[$id], $dItem);
                    }

                    $tpl_data = array_merge(
                        $tpl_data,
                        array(
                            'slider_id' => (int)$data['slider_id'],
                            'slide_speed' => (int)$data['slide_speed'],
                            'pagination_speed' => (int)$data['pagination_speed'],
                            'auto_play_speed' => (int)$data['auto_play_speed'],
                        )
                    );
                }
            }

            $output = '';
            if(is_array($dataFormatted))
            {
                $tpl_data = array_merge(
                    $tpl_data,
                    array(
                        'data' => $dataFormatted,
                        'compactTeaser' => ew_evelations_plugin::isCompactTeaser(),
                        'navItemWidth' => ((int)($elementCount = is_countable($dataFormatted) && count($dataFormatted)) > 1) ? ew_evelations_plugin::floorPrecision(100 / $elementCount, 2) . '%' : null,
                    )
                );

                $output = $tpl_object->getTemplate('ew_evelations_teaser', $tpl, $tpl_data);
            }
        } else {
            $output = $tpl_object->getCachedTemplate($tpl);
        }

        echo $output;
        unset($tpl_object, $tpl_data, $output, $tpl, $dataFormatted, $data);
    }
}
