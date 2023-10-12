<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(constant('XT_GOOGLE_ANALYTICS_ACTIVATE') == '1')
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_googleanalytics/classes/class.xt_googleanalytics.php';

    $google_analytics = new google_analytics();
    $google_analytics->_getCode();
}
