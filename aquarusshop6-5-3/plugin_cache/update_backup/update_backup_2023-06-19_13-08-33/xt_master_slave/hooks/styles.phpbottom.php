<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $xtMinify;

if (XT_MASTER_SLAVE_ACTIVE == '1')
{
    $xtMinify->add_resource(_SRV_WEB_PLUGINS . 'xt_master_slave/css/master_slave.css',144, 'header');
}
