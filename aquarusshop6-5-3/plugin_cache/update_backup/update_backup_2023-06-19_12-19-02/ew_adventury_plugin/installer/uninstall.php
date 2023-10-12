<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once __DIR__ . '/../pluginBootstrap.php';
use ew_adventury\plugin as ew_adventury_plugin;

global $ew_adventury_plugin, $db;

/**
 * DB DeInstallation
 *
 * @example   ew_adventury_plugin::mysqlDropColumn(TABLE_CATEGORIES, 'teaser_sort');
 *
 */

//obsolete content blocks
$content_block = $ew_adventury_plugin->getConfig('content_block');
if (!empty($content_block)) {
    foreach ((array)$content_block['block'] as $block) {
        $db->Execute("DELETE FROM `" . TABLE_CONTENT_BLOCK . "` WHERE `block_tag` = '" . ew_adventury_plugin::CONTENT_BLOCK_PREFIX . (string)$block['tag'] . "'");
    }
}

//obsolete content options
ew_adventury_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_adventury_show_text_status');
ew_adventury_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_adventury_hyperlink');
ew_adventury_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_adventury_hyperlink_status');

//obsolete image types
if (($imageTypes = $ew_adventury_plugin->getTemplateImageTypes()) !== null) {
    foreach ($imageTypes as $type) {
        ew_adventury_plugin::removeImageType($type['dir']);
    }
}