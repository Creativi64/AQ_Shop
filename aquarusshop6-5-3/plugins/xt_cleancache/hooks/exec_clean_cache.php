<?php


defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php';

$cleanCache = new cleancache();
try {
    $cleanCache->cleanup('plg_hookpoints');
} catch (Exception $e) {

}
