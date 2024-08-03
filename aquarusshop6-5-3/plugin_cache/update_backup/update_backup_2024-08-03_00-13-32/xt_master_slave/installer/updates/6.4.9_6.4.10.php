<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_master_slave/classes/config.php';

global $db;
if (!$this->_FieldExists('attributes_desc_html', TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION) && $this->_FieldExists('attributes_desc', TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION))
{
    $db->Execute("ALTER TABLE ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." CHANGE COLUMN `attributes_desc` `attributes_desc_html` TEXT NULL DEFAULT NULL ;");
}

$db->Execute("UPDATE ".TABLE_LANGUAGE_CONTENT." SET `language_key` = 'TEXT_ATTRIBUTES_DESC_HTML' WHERE `language_key` = 'TEXT_ATTRIBUTES_DESC' AND `plugin_key`= 'xt_master_slave' ;");