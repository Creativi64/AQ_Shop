<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($this->code == 'order')
{
    require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_klarna_kp/classes/constants.php';
    $kp_panel = xt_klarna_kp::orderFormGrid_userButtonsWindows();
    $grid->addItem($kp_panel);
}
