<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

switch ($request['get'])
{
    case "all_ACTIVATED_PAYMENTS":
        include_once _SRV_WEBROOT._SRV_WEB_PLUGINS.'xt_skrill/classes/class.xt_skrill.php';
        $result = xt_skrill::dropdownAllPayments();
        break;

    case'skrill_refunds_type':
        $result = array();
        $result[] = array('id' => 'Full', 'name' => TEXT_SKRILL_REFUNDS_TYPE_FULL);
        $result[] = array('id' => 'Partial', 'name' => TEXT_SKRILL_REFUNDS_TYPE_PARTIAL);
        break;
}