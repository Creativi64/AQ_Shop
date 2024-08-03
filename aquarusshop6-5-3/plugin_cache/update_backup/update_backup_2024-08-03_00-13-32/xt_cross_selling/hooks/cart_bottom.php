<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(XT_CROSS_SELLING_ACTIVATED == 1 && XT_CROSS_SELLING_SHOW_IN_CART == 1)
{
    require_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_cross_selling/classes/class.cross_selling.php';
    $cross_selling = new cross_selling(0);
    echo $cross_selling->_display('', true);
}
