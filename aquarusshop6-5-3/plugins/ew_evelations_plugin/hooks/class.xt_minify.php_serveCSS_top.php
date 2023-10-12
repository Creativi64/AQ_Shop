<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manipulate xt:Minify CSS
 */

use ew_evelations\plugin as ew_evelations_plugin;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $resources = array();

    if (isset($this->resources['header']['css']) && is_array($this->resources['header']['css'])) {
        $resources = array_merge($resources, $this->resources['header']['css']);
    }
    if (isset($this->resources['footer']['css']) && is_array($this->resources['footer']['css'])) {
        $resources = array_merge($resources, $this->resources['footer']['css']);
    }

    /** @var $filename string */
    if (ew_evelations_plugin::refreshMinify($resources, $filename)) {
        $this->css_cache_time = 0;
    }

}