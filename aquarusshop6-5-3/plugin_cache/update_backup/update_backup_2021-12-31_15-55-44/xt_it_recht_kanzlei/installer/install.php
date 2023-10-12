<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_it_recht_kanzlei/classes/constants.php';

global $db;

if (!$this->_FieldExists('ts_last_updated',TABLE_CONTENT_ELEMENTS))
{
    $sql = "ALTER TABLE " . TABLE_CONTENT_ELEMENTS . " ADD COLUMN `ts_last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"; // sqlI: no user input, fixed sql values only
    $db->Execute($sql);
}

$apiKey = substr(rtrim(base64_encode(md5(microtime())),"="),0, 25);
$db->Execute("UPDATE ".TABLE_PLUGIN_CONFIGURATION." SET `config_value`=? WHERE `config_key`='ITRK_API_TOKEN_STORE_INDEPENDENT'", array($apiKey));


