<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $p_data array */
/** @var $objP stdClass */

if($objP->success && $p_data['products_master_model'] != '')
{
    xt_master_slave_functions::updateMainQuantityFromVariant($p_data['products_master_model']);
}
