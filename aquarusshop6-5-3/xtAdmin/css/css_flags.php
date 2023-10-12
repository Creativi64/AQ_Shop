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
?>
<style type="text/css">
<?php
foreach ($language->_getLanguageList() as $key => $val) {
	echo '.'.$val['code'].'_icon { background: url(../media/flags/'.$val['icon'].') no-repeat left center; background-position: 0px 12px !important;}'."\n";
}
echo ".xt_paypal_refunds {background-image: url("._SYSTEM_BASE_URL."/"._SRV_WEB_PLUGINS."xt_paypal/images/arrow_undo.png) !important;}";
($plugin_code = $xtPlugin->PluginCode('css_admin.php:css')) ? eval($plugin_code) : false;
?>
</style>
