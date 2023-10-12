<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db, $store_handler;

$configKeys = array();
$rs = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION_MULTI . (int)$store_handler->shop_id . " WHERE 1");
while (!$rs->EOF) {
    $configKeys[] = $rs->fields['config_key'];
    $rs->MoveNext();
}
$rs->Close();
foreach($configKeys as $configKey)
{
    $db->Execute('UPDATE '.TABLE_PDF_MANAGER_CONTENT.' SET template_body = REPLACE(template_body, ?, ?)', array('data.config.'.$configKey, 'data.config.'.strtolower($configKey)) );
    $db->Execute('UPDATE '.TABLE_MAIL_TEMPLATES_CONTENT.' SET mail_subject = REPLACE(mail_subject, ?, ?)', array('data.config.'.$configKey, 'data.config.'.strtolower($configKey)) );
}
