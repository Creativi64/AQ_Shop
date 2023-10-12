<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'plugins/xt_easy_credit/classes/class.xt_easy_credit.php';

if (xt_easy_credit::isAvailable())
{
    global $xtMinify;
    $xtMinify->add_resource(_SRV_WEB_PLUGINS . 'xt_easy_credit/css/xt_easy_credit.css',100);
}