<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (XT_CART_POPUP_STATUS == 1) {
    $_SESSION['show_xt_cart_popup'] = $data_array["product"];

    ($plugin_code = $xtPlugin->PluginCode('class.xt_cart_popup.php:add_product_bottom')) ? eval($plugin_code) : false;

//	$tmp_link = (!empty($_SERVER['HTTPS'])?'https://':'http://'). $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

    function _url_origin($s, $use_forwarded_host = false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;

        return $protocol . '://' . $host;
    }

    function _full_url($s, $use_forwarded_host = false)
    {
        return _url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
    }

    $tmp_link = _full_url($_SERVER);

    $xtLink->_redirect($tmp_link);
}
