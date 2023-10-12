<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $msp;
if (XT_MASTER_SLAVE_ACTIVE == '1' && $msp)
{
    global $current_product_id;
    //$msp->unsetFilter();
    $msp->setProductID($current_product_id);
    //$msp->getMasterSlave();
    $optionsHtml = $msp->getOptions();

    echo $optionsHtml;
}
