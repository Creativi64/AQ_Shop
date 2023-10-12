<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_cookie_consent/classes/constants.php';

global $xtMinify;

$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_cookie_consent/css/xt_cookie_consent.css', 900);
$xtMinify->add_resource(_SRV_WEB_PLUGINS.'xt_cookie_consent/javascript/xt_cookie_consent.js', 900, 'footer');

if(constant('XT_COC_ACTIVATED') == '1')
{
    $initFnc = json_encode(CookieRegistry::getInitFunctions());
    $js = "<script> const cookieConsentInitFunctions = " . $initFnc . "; </script>
";
    echo $js;
}
