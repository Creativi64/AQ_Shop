<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $language, $store_handler;

if (version_compare(_SYSTEM_VERSION, '4.2.00') >= 0)
{
    // XTC >= 4.2.00
    foreach($store_handler->getStores() as $store)
    {
        foreach($language->_getLanguageList('admin') as $lang)
        {
            $header['ts_last_updated_store'.$store['id'].'_'.$lang['content_language']] = array('type' => 'hidden');
        }
    }
}
else {
    // XTC < 4.2.00
    foreach($language->_getLanguageList('admin') as $lang)
    {
        $header['ts_last_updated_'.$lang['content_language']] = array('type' => 'hidden');
    }
}