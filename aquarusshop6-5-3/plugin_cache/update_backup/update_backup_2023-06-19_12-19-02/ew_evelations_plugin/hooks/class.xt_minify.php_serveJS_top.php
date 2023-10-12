<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manipulate xt:Minify JS
 */

use ew_evelations\plugin as ew_evelations_plugin;

if (class_exists('ew_evelations\plugin') && ew_evelations_plugin::status()) {

    $resources = array();

    if (isset($this->resources['header']['js']) && is_array($this->resources['header']['js'])) {
        $resources = array_merge($resources, $this->resources['header']['js']);
    }
    if (isset($this->resources['footer']['js']) && is_array($this->resources['footer']['js'])) {
        $resources = array_merge($resources, $this->resources['footer']['js']);
    }

    /** @var $filename string */
    if (ew_evelations_plugin::refreshMinify($resources, $filename)) {
        $this->js_cache_time = 0;
    }

}