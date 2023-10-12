<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

function smarty_function_cronjob($params, & $smarty) {
	global $xtPlugin, $xtLink;

	$allowed_types = ['img','ajax'];

	$type = isset($params['type']) && in_array($params['type'], $allowed_types) ? $params['type'] : 'ajax';

	switch($type)
    {
        case 'img':
            echo '<img src="cronjob.php" width="1" height="1" alt="">';
            break;
        case 'ajax':
            $url = $xtLink->_link(['default_page' => 'cronjob.php']);
            echo '
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.debug("setting cronjob timeout");
    setTimeout(function(){
        console.debug("calling cronjob");
        const httpRequest = new XMLHttpRequest();
        httpRequest.open("GET", "'.$url.'", true);
        httpRequest.send();
   },4000);
});  
</script>
';
            break;
    }
}
