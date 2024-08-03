<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/** @var $data array */
/** @var $objP stdClass */

if($objP->success && $data['products_master_model'] != '')
{
    xt_master_slave_functions::updateMainQuantityFromVariant($data['products_master_model']);
}
