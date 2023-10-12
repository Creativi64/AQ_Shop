<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $system_status;
/** @var $status int|string*/

$serials = new product_serials();
if ($system_status->values['order_status'][$status]['data']['enable_download']=='1') {
    $serials->assignSerials($this->oID);
}
