<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

// additional js in shop frontend
global $xtMinify;

$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/jquery-bindWithDelay.js',10001, 'footer');
$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/jquery-delayedTextfield.js',10002, 'footer');
$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/klarna_kp_vars.js',0);
$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_klarna_kp/javascript/klarna_kp.js',10003, 'footer');
