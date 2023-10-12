<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Manipulate xt:Minify CSS
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {

    $resources = array();

    if (version_compare(_SYSTEM_VERSION, '5.0.0', '>=')) {
        if (isset($this->resources['header']['css']) && is_array($this->resources['header']['css'])) {
            $resources = array_merge($resources, $this->resources['header']['css']);
        }
        if (isset($this->resources['footer']['css']) && is_array($this->resources['footer']['css'])) {
            $resources = array_merge($resources, $this->resources['footer']['css']);
        }
    } else {
        $resources = $this->resources['css'];
    }

    if (ew_adventury_plugin::refreshMinify($resources, $filename)) {
        $this->css_cache_time = 0;
    }

}