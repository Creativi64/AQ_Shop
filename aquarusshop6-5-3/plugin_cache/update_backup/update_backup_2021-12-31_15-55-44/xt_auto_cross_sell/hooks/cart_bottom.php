<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(constant('XT_AUTO_CROSS_SELLING_ACTIVATED') == 1 && constant('XT_AUTO_CROSS_SELL_SHOW_IN_CART') == 1)
{
$auto_cross_selling = new auto_cross_sell(0);
    echo $auto_cross_selling->_display();
}
