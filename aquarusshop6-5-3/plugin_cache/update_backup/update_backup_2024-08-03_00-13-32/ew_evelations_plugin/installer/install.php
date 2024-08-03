<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once __DIR__ . '/../pluginBootstrap.php';
use ew_evelations\plugin as ew_evelations_plugin;
global $ew_evelations_plugin, $db;

/**
* DB Installation
*
* @example ew_evelations_plugin::mysqlAddColumn(TABLE_CATEGORIES, 'teaser_sort', "INT(4) DEFAULT '0' AFTER `products_sorting2`");
*
*/

//new content blocks
$content_block = $ew_evelations_plugin->getConfig('content_block');
if (!empty($content_block)) {
    foreach ((array)$content_block['block'] as $block) {
        if (ew_evelations_plugin::addContentBlock($block)) {
            ew_evelations_plugin::formatInstallerLogMessage("New content block `" . ew_evelations_plugin::CONTENT_BLOCK_PREFIX . "{$block['tag']}` added", $this->debug_output);
        }
    }
}

//content options
if (ew_evelations_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_evelations_show_text_status'),
    "INT(1) DEFAULT '0' AFTER `content_sort`"
)) {
    ew_evelations_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_evelations_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

if (ew_evelations_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_evelations_hyperlink'),
    "VARCHAR(255) DEFAULT 'http://' AFTER `content_sort`"
)) {
    ew_evelations_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_evelations_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

if (ew_evelations_plugin::mysqlAddColumn(
    ($table = TABLE_CONTENT),
    ($column = 'ew_evelations_hyperlink_status'),
    "INT(1) DEFAULT '0' AFTER `content_sort`"
)) {
    ew_evelations_plugin::formatInstallerLogMessage("Column `$table`.`$column` added", $this->debug_output);
} else {
    ew_evelations_plugin::formatInstallerLogMessage("ERROR: Column `$table`.`$column` could not be added", $this->debug_output, 'error');
}

//image types
if (($imageTypes = $ew_evelations_plugin->getTemplateImageTypes()) !== null) {
    foreach ($imageTypes as $type) {
        if ($ew_evelations_plugin->addImageType($type['dir'], $type['class'], $type['width'], $type['height'], $type['processing'])) {
            ew_evelations_plugin::formatInstallerLogMessage("New image size `{$type['dir']}` added", $this->debug_output);
        } else {
            ew_evelations_plugin::formatInstallerLogMessage("ERROR: Image size `{$type['dir']}` could not be added", $this->debug_output, 'error');
        }
    }
}

if(defined('XT_WIZARD_STARTED') && XT_WIZARD_STARTED)
{
    $db->Execute("UPDATE `" . TABLE_PLUGIN_CONFIGURATION . "` SET config_value = 1 WHERE config_key = 'CONFIG_EW_EVELATIONS_PLUGIN_STATUS'");
}
