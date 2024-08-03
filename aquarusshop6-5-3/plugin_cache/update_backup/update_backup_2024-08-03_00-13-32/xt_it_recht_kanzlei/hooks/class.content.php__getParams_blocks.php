<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $language, $store_handler;

foreach($store_handler->getStores() as $store)
{
    foreach($language->_getLanguageList('admin') as $lang)
    {
        $header['ts_last_updated_store'.$store['id'].'_'.$lang['content_language']] = array('type' => 'hidden');
    }
}
