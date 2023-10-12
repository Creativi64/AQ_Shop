<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if(XT_CROSS_SELLING_ACTIVATED)
{
    global $current_product_id;
    $cross_selling = new cross_selling(0);
    echo $cross_selling->_display($current_product_id);
}
