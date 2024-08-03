<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $kp_order;
if($kp_order) $extraButtonPanels[] = xt_klarna_kp::orderEdit_userButtonsWindows($this->oID);
