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

global $db, $page, $is_pro_version, $xtLink, $language, $store_handler, $xtPlugin;

$type = '';
if(_STORE_META_DOCTYPE_HTML != "html5"){
    $type = 'type="text/javascript" ';
}
$domain = $db->GetOne('SELECT shop_ssl_domain from '.TABLE_MANDANT_CONFIG.' WHERE shop_id = ?', [$store_handler->shop_id]);

echo "<script $type> 

const getUrl = window.location;
const baseUri = '"._SRV_WEB."';
const baseUrl = getUrl.protocol + \"//\" + '".$domain._SRV_WEB."';

window.XT = {
    baseUrl: baseUrl,
    baseUri: baseUri,
    language:  '" . $language->code . "',
        page : {
            page_name : '" . $page->page_name . "'
            },
        version : {
            type: '".($is_pro_version ? 'PRO' : 'FREE')."',
            version : '" . _SYSTEM_VERSION . "'
        }
};

</script>
";

($plugin_code = $xtPlugin->PluginCode('javascript.php:bottom')) ? eval($plugin_code) : false;
