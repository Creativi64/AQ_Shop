<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (isset($xtPlugin->active_modules['xt_cleancache']))
{
    require_once _SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php';
    $cc = new cleancache();

    $r = $cc->cleanTemplateCached('cache_css');
}