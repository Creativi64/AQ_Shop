<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $page;

if($page->page_name == 'checkout' && $page->page_action == 'payment' &&
    !array_key_exists('xt_klarna_kp', $data))
{
    //unset($_SESSION['kp_session']);
}