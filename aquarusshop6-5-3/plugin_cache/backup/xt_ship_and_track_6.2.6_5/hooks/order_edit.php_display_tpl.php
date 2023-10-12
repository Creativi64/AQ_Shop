<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


// buttons und remote windows auf order_edit.php
if(!empty(constant('XT_SHIPCLOUD_API_KEY')))
{
    $extraButtonPanels [] = xt_ship_and_track::orderEdit_userButtonsWindows($this->oID);
}