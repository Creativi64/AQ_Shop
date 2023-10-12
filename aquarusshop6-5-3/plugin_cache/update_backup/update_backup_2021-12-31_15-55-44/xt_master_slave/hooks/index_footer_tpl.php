<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $page;
if($page->page_name == 'product' && _PLUGIN_MASTER_SLAVE_STAY_ON_MASTER_URL == 1 && XT_MASTER_SLAVE_ACTIVE == 1 && array_key_exists('xt_master_slave', $xtPlugin->active_modules))
{
    if (_SYSTEM_SSL == true || _SYSTEM_FULL_SSL == true) {
        $base = _SYSTEM_BASE_HTTPS;
    } else {
        $base = _SYSTEM_BASE_HTTP;
    }
    $javascript_file = $base . _SRV_WEB . _SRV_WEB_PLUGINS . 'xt_master_slave/js/ajax_call.js';
    echo '<script type="text/javascript" src="'.$javascript_file.'"></script>';
}