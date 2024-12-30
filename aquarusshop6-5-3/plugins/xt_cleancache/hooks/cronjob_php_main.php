<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(isset($_GET['cleancache']) && isset($_GET['typeid'])){

	require_once _SRV_WEBROOT.'plugins/xt_cleancache/classes/class.xt_cleancache.php';
	$cc = new cleancache();
	$type = $_GET['typeid'];
    try {
        $cc->cleanup($type);
        echo constant('TEXT_XT_CACHE_DELETED');
    }
    catch(Exception $e) {
        echo $e->getMessage();
    }
}
