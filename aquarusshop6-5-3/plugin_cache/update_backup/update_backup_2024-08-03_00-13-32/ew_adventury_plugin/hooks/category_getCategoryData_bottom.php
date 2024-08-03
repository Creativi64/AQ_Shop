<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Adds more images to categories
 */

use ew_adventury\plugin as ew_adventury_plugin;

if (isset($xtPlugin->active_modules['ew_adventury_plugin'])) {
    require_once __DIR__ . '/../pluginBootstrap.php';
}

if (class_exists('ew_adventury\plugin') && ew_adventury_plugin::status()) {
    // more images
    if (isset($_GET['page']) && $_GET['page'] == 'categorie') {
        if ($this->get_media_images($data['categories_id'], __CLASS__) != null && version_compare(_SYSTEM_VERSION, '5.0.0', '>=')) {
            $media_images = $this->get_media_images($data['categories_id'], __CLASS__);
            $data['more_images'] = $media_images['images'];
        }
    }

}
