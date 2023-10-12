<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once __DIR__ . '/../pluginBootstrap.php';
use ew_adventury\plugin as ew_adventury_plugin;
global $ew_adventury_plugin, $db;

/**
* DB Installation
*
* @example ew_adventury_plugin::mysqlAddColumn(TABLE_CATEGORIES, 'teaser_sort', "INT(4) DEFAULT '0' AFTER `products_sorting2`");
*
*/

//new content blocks
$content_block = $ew_adventury_plugin->getConfig('content_block');
if (!empty($content_block)) {
    foreach ((array)$content_block['block'] as $block) {
        if (ew_adventury_plugin::addContentBlock($block)) {
            ew_adventury_plugin::formatInstallerLogMessage("New content block `" . ew_adventury_plugin::CONTENT_BLOCK_PREFIX . "{$block['tag']}` added", $this->debug_output);
        }
    }
}

//content options
if (ew_adventury_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_adventury_show_text_status'),
    "INT(1) DEFAULT '0' AFTER `content_sort`"
)) {
    ew_adventury_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_adventury_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

if (ew_adventury_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_adventury_hyperlink'),
    "VARCHAR(255) DEFAULT 'http://' AFTER `content_sort`"
)) {
    ew_adventury_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_adventury_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

if (ew_adventury_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_adventury_hyperlink_status'),
    "INT(1) DEFAULT '0' AFTER `content_sort`"
)) {
    ew_adventury_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_adventury_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

//image types
if (($imageTypes = $ew_adventury_plugin->getTemplateImageTypes()) !== null) {
    foreach ($imageTypes as $type) {
        if ($ew_adventury_plugin->addImageType($type['dir'], $type['class'], $type['width'], $type['height'], $type['processing'])) {
            ew_adventury_plugin::formatInstallerLogMessage("New image size `{$type['dir']}` added", $this->debug_output);
        } else {
            ew_adventury_plugin::formatInstallerLogMessage("ERROR: Image size `{$type['dir']}` could not be added", $this->debug_output, 'error');
        }
    }
}