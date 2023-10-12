<?php

if(constant('XT_GCT_ACTIVATE') == '1')
{
    require_once _SRV_WEBROOT . 'plugins/xt_google_ct/classes/class.xt_google_ct.php';

    $gct = new xt_google_ct;
    $gct->_getCode();
}
