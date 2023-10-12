<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
global $db,$xtLink,$language;

if (isset($xtPlugin->active_modules['xt_paypal_plus']) && isset($_REQUEST['ppp_available_data']))
{
	$template4 = new Template();
	$tpl = 'paypal_plus_footer.html';
	$template4->getTemplatePath($tpl, 'xt_paypal_plus', '', 'plugin');
	$tmp_data = $template4->getTemplate('smarty_xt_paypal_plus', $tpl, $_REQUEST['ppp_available_data']);

	echo $tmp_data;

	$css_local_path = '/' . _SRV_WEB_PLUGINS. 'xt_paypal_plus/css/xt_paypal_plus.css';
    $css_file = _SRV_WEBROOT . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . $css_local_path;
	if(!file_exists($css_file))
        $css_file = _SRV_WEBROOT . _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE . $css_local_path;
    if(!file_exists($css_file))
        $css_file = _SRV_WEBROOT  . $css_local_path;
	$css = file_get_contents($css_file);
	echo "<style>".PHP_EOL.$css.PHP_EOL."</style>";
}
