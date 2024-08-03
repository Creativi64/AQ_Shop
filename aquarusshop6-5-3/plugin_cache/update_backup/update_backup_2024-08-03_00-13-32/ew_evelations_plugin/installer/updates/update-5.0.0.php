<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once __DIR__ . '/../pluginBootstrap.php';
use ew_evelations\plugin as ew_evelations_plugin;
global $ew_evelations_plugin, $db;

/**
* DB Update to version 5.0.0
*/

// new image types, because of disallowed chars in old names
if (($imageTypes = $ew_evelations_plugin->getTemplateImageTypes()) !== null) {
    foreach ($imageTypes as $type) {
        if ($ew_evelations_plugin->addImageType($type['dir'], $type['class'], $type['width'], $type['height'], $type['watermark'], $type['processing'])) {
            ew_evelations_plugin::formatInstallerLogMessage("New image size `{$type['dir']}` added. Please perform image processing for all images!", $this->debug_output);
        } else {
            ew_evelations_plugin::formatInstallerLogMessage("ERROR: Image size `{$type['dir']}` could not be added", $this->debug_output, 'error');
        }
    }
}

// remove old image types
if (($imageTypes = $ew_evelations_plugin->getTemplateImageTypes(ew_evelations_plugin::IMAGE_TYPE_PREFIX, false)) !== null) {
    foreach ($imageTypes as $type) {
        if (ew_evelations_plugin::removeImageType($type['dir'])) {
            ew_evelations_plugin::formatInstallerLogMessage("Removed image size `{$type['dir']}`.", $this->debug_output);
        } else {
            ew_evelations_plugin::formatInstallerLogMessage("ERROR: Could not remove image size `{$type['dir']}`", $this->debug_output, 'error');
        }
    }
}