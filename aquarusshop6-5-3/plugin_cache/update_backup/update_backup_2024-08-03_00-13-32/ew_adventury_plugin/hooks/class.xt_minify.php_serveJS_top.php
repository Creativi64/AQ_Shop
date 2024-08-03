<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manipulate xt:Minify JS
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $resources = array();

    if (version_compare(_SYSTEM_VERSION, '5.0.0', '>=')) {
        if (isset($this->resources['header']['js']) && is_array($this->resources['header']['js'])) {
            $resources = array_merge($resources, $this->resources['header']['js']);
        }
        if (isset($this->resources['footer']['js']) && is_array($this->resources['footer']['js'])) {
            $resources = array_merge($resources, $this->resources['footer']['js']);
        }
    } else {
        $resources = $this->resources['js'];
    }

    if (ew_adventury_plugin::refreshMinify($resources, $filename)) {
        $this->js_cache_time = 0;
    }

}