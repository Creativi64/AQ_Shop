<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_cookie_consent/classes/constants.php';

global $db, $language, $store_handler;

$supported_langs = array('de','en');

$txt_dir = _SRV_WEBROOT.'plugins/xt_cookie_consent/installer/consent/';
$bodies = array(
    'de' => _getFileContent($txt_dir.'consent_de.txt'),
    'en' => _getFileContent($txt_dir.'consent_en.txt'),
    'all' => _getFileContent($txt_dir.'consent_en.txt')
);
$titles = array(
    'de' => 'Zustimmung zur Verwendung von Cookies',
    'en' => 'Cookie Consent',
    'all' => 'Cookie Consent'
);

$maxContentId = $db->GetOne("SELECT MAX(content_id) FROM ".TABLE_CONTENT_ELEMENTS);
if ($maxContentId==false) $maxContentId = 0;
$maxContentId++;

$store_id_col_exists = $this->_FieldExists('content_store_id', TABLE_CONTENT_ELEMENTS);

foreach($store_handler->getStores() as $store)
{
    foreach($language->_getLanguageList('admin') as $lang)
    {
        if( in_array(strtolower($lang['id']),$supported_langs))
        {
            $title = $titles[$lang['id']];
            $body = $bodies[$lang['id']];
        }
        else {
            $title = $titles['all'];
            $body = $bodies['all'];
        }
        if ($store_id_col_exists)
        {

            $sql = "INSERT IGNORE INTO ".TABLE_CONTENT_ELEMENTS." (content_id,language_code,content_title,content_heading,content_body,content_store_id) VALUES (?, ?, ?, ?, ?, ?)";
            $db->Execute($sql, array(
                $maxContentId,
                $lang['id'],
                $title,
                $title,
                $body,
                $store['id']
            ));
        }
        else {
            $sql = "INSERT IGNORE INTO ".TABLE_CONTENT_ELEMENTS." (content_id,language_code,content_title,content_heading,content_body) VALUES (?, ?, ?, ?, ?)";
            $db->Execute($sql, array(
                $maxContentId,
                $lang['id'],
                $title,
                $title,
                $body
            ));
        }
    }
}




$sql = "INSERT IGNORE INTO ".TABLE_CONTENT." (content_id,content_parent,content_status,content_hook,content_form,content_image,content_sort) VALUES (?, ?, ?, ?, ?, ?, ?)";
$db->Execute($sql, array(
    $maxContentId,
    0,
    1,
    0,
    0,
    '',
    0
));


// set default for all stores in plg config
$db->Execute("UPDATE ".TABLE_PLUGIN_CONFIGURATION." SET `config_value`=? WHERE `config_key`='XT_COC_CONTENT_CONSENT'", array($maxContentId));
// set the content-id to be uninstalled
$db->Execute("UPDATE ".TABLE_PLUGIN_CONFIGURATION." SET `config_value`=? WHERE `config_key`='XT_COC_UNINSTALL_CONTENT_ID'", array($maxContentId));


function _getFileContent($filename) {
    $handle = fopen($filename, 'rb');
    $content = fread($handle, filesize($filename));
    fclose($handle);
    return $content;

}