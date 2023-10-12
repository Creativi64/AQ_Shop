<?php defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once __DIR__ . '/../pluginBootstrap.php';
use ew_evelations\plugin as ew_evelations_plugin;

global $ew_evelations_plugin, $db;

/**
 * DB DeInstallation
 *
 * @example   ew_evelations_plugin::mysqlDropColumn(TABLE_CATEGORIES, 'teaser_sort');
 *
 */

//obsolete content blocks
$content_block = $ew_evelations_plugin->getConfig('content_block');
if (!empty($content_block)) {
    foreach ((array)$content_block['block'] as $block) {
        $block_id = $db->GetOne("SELECT block_id FROM ". TABLE_CONTENT_BLOCK. " WHERE `block_tag` = ?", [ew_evelations_plugin::CONTENT_BLOCK_PREFIX . (string)$block['tag']]);
        if($block_id)
            $db->Execute("DELETE FROM `" . TABLE_CONTENT_TO_BLOCK . "` WHERE `block_id` = ?", [$block_id]);
        $db->Execute("DELETE FROM `" . TABLE_CONTENT_BLOCK . "` WHERE `block_tag` = ?", [ew_evelations_plugin::CONTENT_BLOCK_PREFIX . (string)$block['tag']]);
    }
}

//obsolete content options
ew_evelations_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_evelations_show_text_status');
ew_evelations_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_evelations_hyperlink');
ew_evelations_plugin::mysqlDropColumn(TABLE_CONTENT, 'ew_evelations_hyperlink_status');

//obsolete image types
if (($imageTypes = $ew_evelations_plugin->getTemplateImageTypes()) !== null) {
    foreach ($imageTypes as $type) {
        ew_evelations_plugin::removeImageType($type['dir']);
    }
}