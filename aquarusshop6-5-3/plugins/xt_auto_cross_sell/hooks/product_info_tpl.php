<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT.'plugins/xt_auto_cross_sell/classes/class.auto_cross_sell.php';

global $p_info;

if(constant('XT_AUTO_CROSS_SELLING_ACTIVATED') == 1)
{
    $auto_cross_selling = new auto_cross_sell();
    echo $auto_cross_selling->_display($p_info->data['products_id']);
}
