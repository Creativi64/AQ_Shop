<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $store_handler, $db;

/** @var $data  */
if($data['code'] == 'xt_master_slave')
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_master_slave/classes/class.xt_master_slave_functions.php';

    $doFix = false;
    foreach ($store_handler->getStores() as $store)
    {
        $sid = $store['id'];

        if($data['conf_XT_MASTER_SLAVE_FIX_QUANTITIES_shop_'.$sid] == 1)
        {
            $doFix = true;
            break;
        }
    }

    if($doFix)
    {
        xt_master_slave_functions::fixMasterStocks();
    }
}
